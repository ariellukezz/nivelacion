<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Curso;
use App\Models\Alumno;
use App\Models\CursoDetalle;
use App\Models\PermisoAsignacionEscuela;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CursoController extends Controller
{
    //DOCENTE
    public function cursoDocente()
    {
        return Inertia::render('Docente/Cursos/cursos');
    }


    public function getCursos(Request $request){

        $res = Curso::select('curso.id', 'curso.nombre','curso.grupo', 'curso.escuela', 'curso.estado', 'curso.editable','programa.programa')
        ->join('docente','curso.id_docente','docente.id')
        ->join('programa', 'programa.id', '=', 'curso.id_programa')
        ->join('periodo',  'periodo.id_periodo', '=', 'curso.id_periodo')
        ->where('curso.estado',"=",1)
        ->where('periodo.estado', 'activo')
        ->where('docente.usuario_id',"=", auth()->user()->id)
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('curso.nombre', 'LIKE', '%' . $request->term . '%')
                ->orWhere('curso.grupo', 'LIKE', '%' . $request->term . '%')
                ->orWhere('curso.escuela', 'LIKE', '%' . $request->term . '%');
        })->orderBy('curso.id', 'DESC')
        ->paginate(10);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }



    public function getAlumnosXCurso(Request $request){

        $comp = Curso::find($request->curso);

        $res = CursoDetalle::select(
            'curso_detalle.id','curso_detalle.nota', 'curso_detalle.condicion', 'curso_detalle.editable',
            'estudiante.codigo_est', 'datos_ingreso.semestre', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno', 'matriz.C'.$comp->id_competencia.'_R as ant'
           //bdhh 'estudiante.dni', 'datos_ingreso.semestre', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno', 'matriz.C'.$comp->id_competencia.'_R as ant'
            )
        ->join('estudiante','estudiante.id','curso_detalle.id_alumno')
        ->join('matriz','matriz.codigo_est','estudiante.codigo_est')
       //dbhh ->join('matriz','matriz.dni','estudiante.dni')
        ->join('curso','curso.id','curso_detalle.id_curso')
        ->join('datos_ingreso', 'estudiante.codigo_est', 'datos_ingreso.codigo_est')
       //bdhh ->join('datos_ingreso', 'estudiante.dni', 'datos_ingreso.dni')
        ->where('curso_detalle.id_curso',"=", $request->curso)
        ->where('estudiante.estado_nivelacion', 1)
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('estudiante.codigo_est', 'LIKE', '%' . $request->term . '%')
               //bdhh ->orWhere('estudiante.dni', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.nombres', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.paterno', 'LIKE', '%' . $request->term . '%');
        })->orderBy('estudiante.paterno', 'ASC')
        ->paginate(200);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }


    public function updateNota(Request $request ) {

        $curso_detalle = CursoDetalle::find($request->id_detallecurso);
        $curso_detalle->nota = $request->nota;
        if( $request->nota >= 10.5){
            $curso_detalle->condicion = 1;
        }else {
            $curso_detalle->condicion = 0;
        }

        $curso_detalle->save();
        $this->response['tipo'] = 'info';
        $this->response['titulo'] = '!REGISTRO MODIFICADO!';
        $this->response['mensaje'] = 'La nota acaba de ser modificado.';
        $this->response['estado'] = true;
        $this->response['datos'] = $curso_detalle;

        return response()->json($this->response, 200);
    }

    //END DOCENTE



    //ALUMNO

    public function getNotasByAlumno(Request $request ) {

        $res = Alumno::select('estudiante.id','docente.nombres', 'docente.paterno','docente.materno', 'curso.nombre','curso.grupo', 'curso.escuela', 'curso_detalle.nota')
        ->join('curso_detalle','curso_detalle.id_alumno','estudiante.id')
        ->join('curso','curso.id','curso_detalle.id_curso')
        ->join('users','users.id','estudiante.usuario_id')
        ->leftJoin('docente', 'docente.id', '=', 'curso.id_docente')
        ->join('periodo', 'periodo.id_periodo', '=', 'curso.id_periodo')
        ->where('curso.estado',"=",1)
        ->where('estudiante.usuario_id',"=", auth()->user()->id)
        ->where('periodo.estado', 'activo')
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('curso.nombre', 'LIKE', '%' . $request->term . '%');
        })->orderBy('curso.id', 'DESC')
        ->paginate(10);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
    }


    // SELECT curso_detalle.encuesta, curso.id, curso.nombre FROM curso
    // JOIN curso_detalle ON curso_detalle.id_curso = curso.id
    // WHERE curso_detalle.id_alumno = 1000


    public function getCursosEncuesta() {

        $alumno = DB::select('SELECT id from estudiante where usuario_id = '.auth()->user()->id );

        $res = Curso::select('curso_detalle.encuesta', 'curso.id', 'curso.nombre')
        ->join('curso_detalle','curso_detalle.id_curso','curso.id')
        ->where('curso.estado',"=",1)
        ->where('curso_detalle.id_alumno',"=", $alumno[0]->id)
        ->get();

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
    }

    public function getCursosEncuestaD() {

        $docente = DB::select('SELECT id from docente where usuario_id = '.auth()->user()->id );
        $res = Curso::select('encuesta', 'id', 'nombre')
        ->where('curso.estado',"=",1)
        ->where('id_docente',"=", $docente[0]->id)
        ->get();

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
    }

    public function delete($id){
        $curso = Curso::find($id);

        if (!$curso) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'Curso no encontrado.'
            ], 404);
        }

        $periodoActivo = DB::table('periodo')->where('estado', 'activo')->value('id_periodo');
        if ($periodoActivo && (int) $curso->id_periodo !== (int) $periodoActivo) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'Los cursos de períodos anteriores son solo de consulta.'
            ], 422);
        }

        $esSuperadmin = (int) (auth()->user()->rol ?? -1) === 0;

        if (!$esSuperadmin) {
            $relacion = DB::table('curso')
                ->join('programa', 'programa.id', '=', 'curso.id_programa')
                ->where('curso.id', $id)
                ->where('programa.id_escuela', auth()->user()->id_escuela)
                ->select('programa.id_escuela', 'curso.id_periodo')
                ->first();

            if (!$relacion) {
                return response()->json([
                    'estado' => false,
                    'mensaje' => 'No tiene permiso para eliminar este curso.'
                ], 403);
            }

            if (!PermisoAsignacionEscuela::permitido((int) $relacion->id_escuela, (int) $relacion->id_periodo, 'eliminar')) {
                return response()->json([
                    'estado' => false,
                    'mensaje' => 'Super Admin no ha habilitado la eliminación de cursos para su escuela en este período.'
                ], 403);
            }
        }

        $cantidadAlumnos = CursoDetalle::where('id_curso', $curso->id)->count();
        if ($cantidadAlumnos > 0) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'No se puede eliminar el curso porque tiene ' . $cantidadAlumnos . ' alumno(s) matriculado(s). Retire primero las matrículas o deje el curso inactivo.'
            ], 409);
        }

        $tieneDocumentos = DB::table('documentocurso')
            ->where('id_curso', $curso->id)
            ->exists();

        if ($tieneDocumentos) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'No se puede eliminar el curso porque tiene documentos relacionados.'
            ], 409);
        }

        $p = clone $curso;
        $curso->delete();

        $this->response['tipo'] = 'error';
        $this->response['titulo'] = '!REGISTRO ELIMINADO!';
        $this->response['mensaje'] = 'El Curso '.$p->nombre.' acaba de ser eliminado.';
        $this->response['estado'] = true;
        $this->response['datos'] = $p;
        return response()->json($this->response, 200);
    }

public function getEventoInduccionByAlumno(Request $request)
{
    $userId = auth()->id();

    $query = DB::table('estudiante')
        ->leftJoin('evento_est', 'evento_est.codigo_est', '=', 'estudiante.codigo_est')
        ->where('estudiante.usuario_id', $userId);

    // Si tu tabla evento_est tiene una columna que identifica el evento (p. ej. 'evento' o 'tipo'),
    // descomenta y ajusta esta línea:
    // $query->where('evento_est.evento', 'induccion');

    $res = $query->select(
            'evento_est.mesa',
            'evento_est.numero',
            'evento_est.firma_ingreso'
        )
        ->first();

    // Siempre devuelve un objeto con las claves esperadas,
    // aunque no exista registro en evento_est aún:
    $payload = $res ?? (object)[
        'mesa' => null,
        'numero' => null,
        'firma_ingreso' => null,
    ];

    return response()->json([
        'estado' => true,
        'datos'  => $payload,
    ], 200);
}

public function getHistorialNotas(Request $request)
{
$res = DB::select("SELECT
        estudiante.id,
        docente.nombres,
        docente.paterno,
        docente.materno,
        competencia.cod,
        competencia.nombre AS competencia_nombre,
        curso.nombre AS curso_nombre,
        curso.grupo,
        curso.escuela,
        curso_detalle.nota,
        periodo.id_periodo,
        periodo.nombre AS periodo_nombre,
        periodo.estado AS periodo_estado
    FROM estudiante
    JOIN curso_detalle ON curso_detalle.id_alumno = estudiante.id
    JOIN curso ON curso.id = curso_detalle.id_curso
    JOIN competencia ON curso.id_competencia = competencia.id
    LEFT JOIN docente ON docente.id = curso.id_docente
    JOIN periodo ON periodo.id_periodo = curso.id_periodo
    WHERE curso.estado = 1
      AND estudiante.usuario_id = ?
    ORDER BY periodo.id_periodo DESC, competencia.cod ASC
", [auth()->id()]);


    $this->response['estado'] = true;
    $this->response['datos']  = $res;
    return response()->json($this->response, 200);
}






}
