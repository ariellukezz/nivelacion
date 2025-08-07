<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Tutor;
use App\Models\Docente;
use App\Models\CursoDetalle;
use App\Models\Curso;
use App\Models\Periodo;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class AsignacionController extends Controller
{

    public function index()
    {
        return Inertia::render('Asignacion/index');
    }

    public function getCursos(Request $request){

        $query_where = [];

        if ($request->competencia !== null) array_push($query_where, ['curso.id_competencia', '=', $request->competencia]);

        $res = Curso::select(
            'curso.id', 'curso.nombre',
            'docente.id as id_docente', DB::raw("CONCAT( docente.nombres,' ',docente.paterno,' ',docente.materno) as docente"),
            'competencia.id as id_competencia', 'competencia.nombre as competencia',
            'curso.grupo','curso.id_programa', 'programa.programa', 'curso.escuela', 'curso.estado','periodo.id_periodo', 'periodo.nombre as periodo'
        )
        ->leftJoin('docente','docente.id','curso.id_docente')
        ->join('competencia','competencia.id','curso.id_competencia')
        ->join('programa', 'programa.id', '=', 'curso.id_programa')
        ->join('periodo', 'curso.id_periodo', '=', 'periodo.id_periodo')
        ->where('curso.escuela',"=",$request->escuela)
        ->where('curso.id_usuario',"=",auth()->id())
        ->where('periodo.estado', '=', 'activo') // 👈 FILTRAR SOLO PERIODO ACTIVO
        ->where($query_where)
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('curso.nombre', 'LIKE', '%' . $request->term . '%')
                ->orWhere('competencia.nombre', 'LIKE', '%' . $request->term . '%');
        })->orderBy('curso.id', 'DESC')
        ->paginate(100);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }

    public function getDetalleCurso(Request $request){

        $query_where = [];
        //if ($request->competencia !== null) array_push($query_where, ['curso.id_competencia', '=', $request->competencia]);


        $res = CursoDetalle::select(
            'estudiante.codigo_est', 'datos_ingreso.semestre', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno',
           //bdhh 'estudiante.dni', 'datos_ingreso.semestre', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno',
            'curso.nombre as curso',
            'curso_detalle.nota'
        )
        ->join('curso','curso_detalle.id_curso','curso.id')
        ->join('estudiante','estudiante.id','curso_detalle.id_alumno')
        ->join('datos_ingreso', 'estudiante.codigo_est', 'datos_ingreso.codigo_est')
        //bdhh ->join('datos_ingreso', 'estudiante.dni', 'datos_ingreso.dni')
        ->where('curso.id',"=",$request->curso)
        ->where($query_where)
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('curso.nombre', 'LIKE', '%' . $request->term . '%');
        })->orderBy('curso.id', 'DESC')
        ->paginate(200);

        $registrados = CursoDetalle::select(
            'estudiante.id', 'estudiante.codigo_est', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno')
          //bdhh  'estudiante.id', 'estudiante.dni', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno')
        ->join('curso','curso_detalle.id_curso','curso.id')
        ->join('estudiante','estudiante.id','curso_detalle.id_alumno')
        ->where('curso.id',"=",$request->curso)
        ->where($query_where)
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('curso.nombre', 'LIKE', '%' . $request->term . '%');
        })->orderBy('curso.id', 'DESC')
        ->paginate(200);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        $this->response['registrados'] = $registrados;

        return response()->json($this->response, 200);

    }


    public function save(Request $request ) {

        $curso = null;
        if (!$request->id) {
            $curso = Curso::create([
                'nombre' => $request->nombre,
                'id_competencia' => $request->id_competencia,
                'id_docente' => $request->id_docente,
                'grupo' => $request->grupo,
                'escuela' => $request->escuela,
                'estado' => $request->estado,
                'id_programa' => $request->id_programa,
                'id_usuario' => auth()->id(),
                'id_periodo' => Periodo::activoId(), // ✅ AÑADIDO
            ]);
            $this->response['tipo'] = 'success';
            $this->response['titulo'] = 'REGISTRO NUEVO';
            $this->response['mensaje'] = 'Curso de nivelación '.$curso->nombre.' registrado con exito';
            $this->response['estado'] = true;
            $this->response['datos'] = $curso;
        } else {
            $curso = Curso::find($request->id);
            $curso->nombre= $request->nombre;
            $curso->id_competencia= $request->id_competencia;
            $curso->id_docente = $request->id_docente;
            $curso->grupo = $request->grupo;
            $curso->escuela = $request->escuela;
            $curso->estado = $request->estado;
            $curso->id_programa = $request->id_programa;
            $curso->save();

            $this->response['tipo'] = 'info';
            $this->response['titulo'] = '!REGISTRO MODIFICADO!';
            $this->response['mensaje'] = 'Docente '.$curso->nombre.' acaba de ser modificado.';
            $this->response['estado'] = true;
            $this->response['datos'] = $curso;
        }

        return response()->json($this->response, 200);
    }


    public function getDocentesXcompetencia(Request $request){

        $competencia = $request->competencia;

        $res = Docente::select(
            'docente.id',
            DB::raw("CONCAT( docente.nombres,' ',docente.paterno,' ',docente.materno) as nombres"),
        )
        ->join('docente_competencia','docente.id','docente_competencia.id_docente')
        ->where('estado','=',1)
        ->where('docente_competencia.id_competencia',"=",$competencia)
#        ->where('docente.id_usuario','=',auth()->id())
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('docente.nombres', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.paterno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.materno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.nro_doc', 'LIKE', '%' . $request->term . '%');
        })->orderBy('docente.id', 'DESC')
        ->paginate(1000);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }

    public function asignarCursoNivelacion(Request $request){

      try {
          DB::transaction(function () use ($request) {

            foreach ($request->diferencia as $alumno) {
                $this->asignarCurso($request->curso, $alumno['id']);
            }

            foreach ($request->diferencia2 as $alumno) {
                $this->eliminarCurso($request->curso, $alumno['id']);
            }

                $this->response['tipo'] = 'success';
                $this->response['titulo'] = '!REGISTROS NUEVOS!';
                $this->response['mensaje'] = 'ALUMNOS REGISTRADOS CON EXITOS';
                $this->response['estado'] = true;
                // $this->response['datos'] = $curso;
            return response()->json($this->response, 200);
          });
        } catch (\Throwable $e) {
          echo "Error en la transacción: " . $e->getMessage();
      }

    }

    public function asignarCurso($id_curso, $id_alumno)
{
    // Buscar el curso y obtener su periodo
    $curso = Curso::findOrFail($id_curso);

    // Verificar si el curso pertenece al periodo activo
    if ($curso->id_periodo != Periodo::activoId()) {
        abort(403, 'No se puede matricular en un curso que no pertenece al periodo activo.');
    }

    // Crear el registro de matrícula
    CursoDetalle::create([
        'id_curso'   => $id_curso,
        'id_alumno'  => $id_alumno,
      //  'id_periodo' => $curso->id_periodo,
    ]);
}

    public function eliminarCurso($id_curso, $id_alumno) {
        $cursoDetalle = CursoDetalle::where('id_curso', $id_curso)
                                    ->where('id_alumno', $id_alumno)
                                    ->first();
        if ($cursoDetalle) {
            $cursoDetalle->delete();
            return response()->json(['message' => 'Registro eliminado con éxito']);
        } else {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }
    }



        // ---------- genrar pdf ------------
public function pdf($curso){

    $res = Curso::select(
        'curso.id AS id_curso',
        'curso.nombre AS curso',
        'periodo.nombre AS ciclo',
        'competencia.id AS id_competencia',
        'competencia.nombre AS competencia',
        'docente.nro_doc AS dni_docente',
        'docente.nombres AS nombre',
        'docente.paterno as paterno',
        'docente.materno as materno',
        'curso.grupo',
        'curso.escuela',
        'programa.programa',
        'escuela.filial'
    )
    ->join('competencia','competencia.id','curso.id_competencia')
    ->leftjoin('docente','curso.id_docente','docente.id')
    ->join('programa','curso.id_programa','programa.id')
    ->join('escuela', 'programa.id_escuela', '=', 'escuela.id')
    ->join('periodo', 'periodo.id_periodo', '=', 'curso.id_periodo')
    ->where('curso.id','=',$curso)
    ->get();

    $estudiantes = CursoDetalle::select(
        'estudiante.codigo_est', 'datos_ingreso.semestre', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno',
       //bdhh 'estudiante.dni', 'datos_ingreso.semestre', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno',
        'curso_detalle.nota', 'curso_detalle.condicion'
    )
    ->join('estudiante','curso_detalle.id_alumno','estudiante.id')
    ->join('datos_ingreso', 'datos_ingreso.codigo_est', 'estudiante.codigo_est')
   //bdhh ->join('datos_ingreso', 'datos_ingreso.dni', 'estudiante.dni')
    ->where('curso_detalle.id_curso','=',$curso)
    ->get();

    $data = $res[0];
    $pdf = pdf::loadView('RepCursoPDF/indexcursos', compact('data','estudiantes'));

    return $pdf->download('Reporte.pdf');

}
// ******** fin pdf ---------------

}
