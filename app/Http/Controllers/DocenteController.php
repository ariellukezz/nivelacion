<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Docente;
use App\Models\Usuario;
use App\Models\Curso;
use App\Models\Programa;
use App\Models\CursoDetalle;
use App\Models\DocenteCompetencia;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use DB;

class DocenteController extends Controller
{

    public function index()
    {
        return Inertia::render('Tutores/index');
    }

    public function getDocentes(Request $request){

        $res = Docente::select('docente.id', 'docente.tipo_doc','docente.nro_doc', 'docente.nombres', 'docente.paterno', 'docente.materno', 'docente.telefono',
        'docente.email', 'docente.direccion', 'docente.f_nac', 'docente.sexo', 'docente.estado'
        )
        ->join('users','users.id','docente.usuario_id')
        ->where('users.id_escuela','=',auth()->user()->id_escuela)
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('docente.nombres', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.paterno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.materno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.nro_doc', 'LIKE', '%' . $request->term . '%');
        })->orderBy('docente.id', 'DESC')
        ->paginate(200);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }

    public function save(Request $request)
{
    $competencias = $request->competencias ?? [];

    $docenteExistente = Docente::where('tipo_doc', $request->tipo_doc)
        ->where('nro_doc', $request->nro_doc)
        ->when($request->id, function ($query) use ($request) {
            $query->where('id', '<>', $request->id);
        })
        ->first();

    if ($docenteExistente) {
        return response()->json([
            'estado' => false,
            'tipo' => 'warn',
            'titulo' => 'DOCENTE YA REGISTRADO',
            'mensaje' => 'El docente ' .
                $docenteExistente->nombres . ' ' .
                $docenteExistente->paterno . ' ' .
                $docenteExistente->materno .
                ' ya se encuentra registrado en el sistema. Revise el módulo Asignación Docente para realizar la asignación correspondiente.',
            'datos' => $docenteExistente
        ], 200);
    }

    if (!$request->id) {

        $correoExiste = Usuario::where('email', $request->correo)->exists();

        if ($correoExiste) {
            return response()->json([
                'estado' => false,
                'tipo' => 'warn',
                'titulo' => 'CORREO YA REGISTRADO',
                'mensaje' => 'El correo ingresado ya se encuentra registrado en el sistema.',
                'datos' => null
            ], 200);
        }

        DB::beginTransaction();

        try {
            $usuario = Usuario::create([
                'email' => $request->correo,
                'password' => Hash::make($request->nro_doc),
                'rol' => 4,
                'estado' => 1,
                'estado_contraseña' => 1,
                'id_escuela' => auth()->user()->id_escuela,
                'id_usuario' => auth()->id()
            ]);

            $docente = Docente::create([
                'tipo_doc' => $request->tipo_doc,
                'nro_doc' => $request->nro_doc,
                'nombres' => $request->nombres,
                'paterno' => $request->primer_apellido,
                'materno' => $request->segundo_apellido,
                'telefono' => $request->celular,
                'email' => $request->correo,
                'direccion' => $request->direccion,
                'f_nac' => $request->fecha,
                'sexo' => $request->sexo,
                'estado' => $request->estado,
                'usuario_id' => $usuario->id,
                'id_usuario' => auth()->id()
            ]);

            foreach ($competencias as $item) {
                $this->saveCompetencias($docente->id, $item);
            }

            DB::commit();

            return response()->json([
                'estado' => true,
                'tipo' => 'success',
                'titulo' => 'REGISTRO NUEVO',
                'mensaje' => 'Docente ' . $docente->nombres . ' registrado con éxito.',
                'datos' => $docente
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'estado' => false,
                'tipo' => 'error',
                'titulo' => 'ERROR DE REGISTRO',
                'mensaje' => 'No se pudo registrar al docente. Verifique que el correo y celular no se encuentren registrados.',
                'datos' => null
            ], 200);
        }
    }

    $docenteCompetencias = DB::select(
        'SELECT id_competencia as c
         FROM docente_competencia
         WHERE id_docente = ' . (int) $request->id
    );

    $actuales = $this->convert($docenteCompetencias);

    $opcionesEliminadas = array_diff($actuales, $competencias);
    $opcionesNuevas = array_diff($competencias, $actuales);

    foreach ($opcionesNuevas as $item) {
        $this->saveCompetencias($request->id, $item);
    }

    foreach ($opcionesEliminadas as $item) {
        $competencia = DB::select(
            'SELECT id
             FROM docente_competencia
             WHERE id_docente = ' . (int) $request->id . '
             AND id_competencia = ' . (int) $item
        );

        if (!empty($competencia)) {
            $this->deletedocentecompetencia($competencia[0]->id);
        }
    }

    $docente = Docente::find($request->id);

    if (!$docente) {
        return response()->json([
            'estado' => false,
            'tipo' => 'error',
            'titulo' => 'DOCENTE NO ENCONTRADO',
            'mensaje' => 'No se encontró el docente que desea modificar.',
            'datos' => null
        ], 200);
    }

    $docente->tipo_doc = $request->tipo_doc;
    $docente->nro_doc = $request->nro_doc;
    $docente->nombres = $request->nombres;
    $docente->paterno = $request->primer_apellido;
    $docente->materno = $request->segundo_apellido;
    $docente->telefono = $request->celular;
    $docente->email = $request->correo;
    $docente->direccion = $request->direccion;
    $docente->f_nac = $request->fecha;
    $docente->sexo = $request->sexo;
    $docente->estado = $request->estado;
    $docente->save();

    $usuario = Usuario::find($docente->usuario_id);

    if ($usuario) {
        $usuario->email = $request->correo;
        $usuario->save();
    }

    return response()->json([
        'estado' => true,
        'tipo' => 'info',
        'titulo' => 'REGISTRO MODIFICADO',
        'mensaje' => 'Docente ' . $docente->nombres . ' modificado correctamente.',
        'datos' => $docente
    ], 200);
}


    public function delete($id){
        $docente = Docente::find($id);
        $p = $docente;
        $docente->delete();

        $this->response['tipo'] = 'error';
        $this->response['titulo'] = '!REGISTRO ELIMINADO!';
        $this->response['mensaje'] = 'El Docente '.$p->nombre.' acaba de ser eliminado.';
        $this->response['estado'] = true;
        $this->response['datos'] = $p;
        return response()->json($this->response, 200);
    }

    public function saveCompetencias($docente, $competencia){
        $docente_competencia = DocenteCompetencia::create([
            'id_competencia' => $competencia,
            'id_docente' => $docente
        ]);
    }

    public function saveEditCompetencias($docente, $competencia){

        $competencias = DB::select('SELECT id, id_competencia, id_docente FROM docente_competencia
        WHERE id_competencia =' .$competencia.' AND id_docente = '.$docente);

        $docente_competencia = DocenteCompetencia::find($competencias[0]->id);
        $docente_competencia->id_competencia = $competencia;
        $docente_competencia->id_docente = $docente;
        $docente_competencia->save();
    }

    public function getCompetenciasByDocente(Request $request){

        $res = DocenteCompetencia::select('docente_competencia.id_competencia')
        ->join('docente','docente_competencia.id_docente','docente.id')
        ->where('docente_competencia.id_docente','=',$request->id_docente)
        ->paginate(10);

        $competencia = [];

        foreach($res as $item ){
            array_push($competencia,$item->id_competencia);
        }

        $this->response['estado'] = true;
        $this->response['datos'] = $competencia;
        return response()->json($this->response, 200);

    }

    public function convert($ar){
        $arr = [];
        foreach($ar as $item ){
            array_push($arr,$item->c);
        }
        return $arr;
    }

    public function deletedocentecompetencia($id){
        $docente_compe = DocenteCompetencia::find($id);
        $docente_compe->delete();
    }


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
        ->join('docente','curso.id_docente','docente.id')
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
      //bdhh  ->join('datos_ingreso', 'datos_ingreso.dni', 'estudiante.dni')
        ->where('curso_detalle.id_curso','=',$curso)
        ->get();

        $data = $res[0];
        $pdf = Pdf::loadView('RepCursoPDF/index', compact('data','estudiantes'));

        return $pdf->download('Reporte.pdf');
        //return $pdf->stream('Reporte.pdf');

    }



    // DOCENTE
    public function dashboardDocente()
    {
        return Inertia::render('Docente/Dashboard/index');
    }


    //GET DATA DE APIBRAYAN
    public function getDataPrisma($dni)
    {
        $url = "https://erpprisma.com/rucdni/l_dni.php?dni=" . $dni;
       // $url = "https://mgg.com.pe/rucdni/l_dni.php?dni=" . $dni;

        $response = Http::get($url);
        $data = explode("|", $response->body());

        $resultado = [
            'codigo' => $data[0],
            'dni' => $data[1],
            'nombre' => $data[2],
            'paterno' => $data[3],
            'materno' => $data[4],
        ];
        // $resultado = [

        //     'dni' => $data[0],
        //     'nombre' => $data[1],
        //     'paterno' => $data[2],
        //     'materno' => $data[3],
        // ];

        return response()->json($resultado);
    }


public function save2(Request $request)
{
    $competencias = $request->competencias ?? [];

    $docenteExistente = Docente::where('tipo_doc', $request->tipo_doc)
        ->where('nro_doc', $request->nro_doc)
        ->when($request->id, function ($query) use ($request) {
            $query->where('id', '<>', $request->id);
        })
        ->first();

    if ($docenteExistente) {
        return response()->json([
            'estado' => false,
            'tipo' => 'warn',
            'titulo' => 'DOCENTE YA REGISTRADO',
            'mensaje' => 'El docente ' .
                $docenteExistente->nombres . ' ' .
                $docenteExistente->paterno . ' ' .
                $docenteExistente->materno .
                ' ya se encuentra registrado en el sistema. Revise el módulo Asignación Docente para realizar la asignación correspondiente.',
            'datos' => $docenteExistente
        ], 200);
    }

    $esc = Programa::find($request->id_docente);

    if (!$esc) {
        return response()->json([
            'estado' => false,
            'tipo' => 'warn',
            'titulo' => 'PROGRAMA REQUERIDO',
            'mensaje' => 'Debe seleccionar un programa para registrar al docente.',
            'datos' => null
        ], 200);
    }

    $usuarioActualId = null;

    if ($request->id) {
        $docenteActual = Docente::find($request->id);

        if (!$docenteActual) {
            return response()->json([
                'estado' => false,
                'tipo' => 'error',
                'titulo' => 'DOCENTE NO ENCONTRADO',
                'mensaje' => 'No se encontró el docente que desea modificar.',
                'datos' => null
            ], 200);
        }

        $usuarioActualId = $docenteActual->usuario_id;
    }

    $correoUsuario = Usuario::where('email', $request->correo)
        ->when($usuarioActualId, function ($query) use ($usuarioActualId) {
            $query->where('id', '<>', $usuarioActualId);
        })
        ->exists();

    $correoDocente = Docente::where('email', $request->correo)
        ->when($request->id, function ($query) use ($request) {
            $query->where('id', '<>', $request->id);
        })
        ->exists();

    if ($correoUsuario || $correoDocente) {
        return response()->json([
            'estado' => false,
            'tipo' => 'warn',
            'titulo' => 'CORREO YA REGISTRADO',
            'mensaje' => 'El correo ingresado ya se encuentra registrado en el sistema.',
            'datos' => null
        ], 200);
    }

    if ($request->celular) {
        $telefonoExiste = Docente::where('telefono', $request->celular)
            ->when($request->id, function ($query) use ($request) {
                $query->where('id', '<>', $request->id);
            })
            ->exists();

        if ($telefonoExiste) {
            return response()->json([
                'estado' => false,
                'tipo' => 'warn',
                'titulo' => 'CELULAR YA REGISTRADO',
                'mensaje' => 'El número de celular ingresado ya pertenece a otro docente.',
                'datos' => null
            ], 200);
        }
    }

    DB::beginTransaction();

    try {
        if (!$request->id) {
            $usuario = Usuario::create([
                'email' => $request->correo,
                'password' => Hash::make($request->nro_doc),
                'rol' => 4,
                'estado' => 1,
                'estado_contraseña' => 1,
                'programa_id' => $request->id_docente,
                'id_escuela' => $esc->id_escuela,
                'id_usuario' => auth()->id()
            ]);

            $docente = Docente::create([
                'tipo_doc' => $request->tipo_doc,
                'nro_doc' => $request->nro_doc,
                'nombres' => $request->nombres,
                'paterno' => $request->primer_apellido,
                'materno' => $request->segundo_apellido,
                'telefono' => $request->celular,
                'email' => $request->correo,
                'direccion' => $request->direccion,
                'f_nac' => $request->fecha,
                'sexo' => $request->sexo,
                'estado' => $request->estado,
                'usuario_id' => $usuario->id,
                'id_usuario' => auth()->id()
            ]);

            foreach ($competencias as $item) {
                $this->saveCompetencias($docente->id, $item);
            }

            DB::commit();

            return response()->json([
                'estado' => true,
                'tipo' => 'success',
                'titulo' => 'REGISTRO NUEVO',
                'mensaje' => 'Docente ' . $docente->nombres . ' registrado con éxito.',
                'datos' => $docente
            ], 200);
        }

        $docenteCompetencias = DB::select(
            'SELECT id_competencia AS c
             FROM docente_competencia
             WHERE id_docente = ' . (int) $request->id
        );

        $actuales = $this->convert($docenteCompetencias);

        $opcionesEliminadas = array_diff($actuales, $competencias);
        $opcionesNuevas = array_diff($competencias, $actuales);

        foreach ($opcionesNuevas as $item) {
            $this->saveCompetencias($request->id, $item);
        }

        foreach ($opcionesEliminadas as $item) {
            $competencia = DB::select(
                'SELECT id
                 FROM docente_competencia
                 WHERE id_docente = ' . (int) $request->id . '
                 AND id_competencia = ' . (int) $item
            );

            if (!empty($competencia)) {
                $this->deletedocentecompetencia($competencia[0]->id);
            }
        }

        $docente = Docente::find($request->id);

        $docente->tipo_doc = $request->tipo_doc;
        $docente->nro_doc = $request->nro_doc;
        $docente->nombres = $request->nombres;
        $docente->paterno = $request->primer_apellido;
        $docente->materno = $request->segundo_apellido;
        $docente->telefono = $request->celular;
        $docente->email = $request->correo;
        $docente->direccion = $request->direccion;
        $docente->f_nac = $request->fecha;
        $docente->sexo = $request->sexo;
        $docente->estado = $request->estado;
        $docente->save();

        $usuario = Usuario::find($docente->usuario_id);

        if ($usuario) {
            $usuario->programa_id = $request->id_docente;
            $usuario->id_escuela = $esc->id_escuela;
            $usuario->email = $request->correo;
            $usuario->save();
        }

        DB::commit();

        return response()->json([
            'estado' => true,
            'tipo' => 'info',
            'titulo' => 'REGISTRO MODIFICADO',
            'mensaje' => 'Docente ' . $docente->nombres . ' modificado correctamente.',
            'datos' => $docente
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'estado' => false,
            'tipo' => 'error',
            'titulo' => 'ERROR',
            'mensaje' => 'No se pudo guardar la información del docente.',
            'datos' => null
        ], 500);
    }
}


    public function getDocentesSuperAdmin(Request $request){

        $res = Docente::select('docente.id', 'docente.tipo_doc', 'docente.nro_doc', 'docente.nombres',
        'docente.paterno', 'docente.materno', 'docente.telefono', 'docente.email',
        'docente.direccion', 'docente.f_nac', 'docente.sexo', 'docente.estado',
        'programa.programa', 'escuela.nombre', 'users.estado_contraseña')
->join('users', 'docente.usuario_id', '=', 'users.id')
->leftJoin('escuela', 'users.id_escuela', '=', 'escuela.id')
->leftJoin('programa', 'users.programa_id', '=', 'programa.id')
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('docente.nombres', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.paterno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.materno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.nro_doc', 'LIKE', '%' . $request->term . '%')
                ->orWhere('users.programa_id', 'LIKE', '%' . $request->term . '%')
                ->orWhere('users.id_escuela', 'LIKE', '%' . $request->term . '%');
        })->orderBy('docente.id', 'DESC')
        ->paginate(200);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }




}
