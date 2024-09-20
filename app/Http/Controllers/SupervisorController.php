<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\CursoDetalle;
use App\Models\Alumno;
use App\Models\Documento;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;


class SupervisorController extends Controller
{
//     public function getDocumentos(Request $request) {

//         $query_where = [];

//         if ($request->programa) array_push($query_where,[DB::raw('documento.id_programa'), '=', $request->programa]);
//         $res = Documento::select(
//             'documento.tipo',
//             'escuela.nombre AS escuela', 'escuela.nombre_corto', 'documento.fecha_subida', 'users.nombres AS username', 'users.apellidos AS userlastname')
//         ->join('escuela','documento.id_escuela', 'escuela.id')
//         ->join('users','users.id', 'documento.id_usuario')
//         ->where($query_where)
//         ->where(function ($query) use ($request) {
//             return $query
//                 ->orWhere('escuela.nombre', 'LIKE', '%' . $request->term . '%')
//                 ->orWhere('escuela.tipo', 'LIKE', '%' . $request->term . '%')
//                 ->orWhere('users.nombres', 'LIKE', '%' . $request->term . '%');
//         })
//         ->paginate($request->paginashoja);

//         $this->response['estado'] = true;
//         $this->response['datos'] = $res;
//         return response()->json($this->response, 200);

//     }

public function getDocumentos(Request $request) {
    $conditions = [];

    // Condición por programa
    if ($request->programa) {
        array_push($conditions, [DB::raw('documento.id_escuela'), '=', $request->programa]);
    }

    // Condición por periodo
    if ($request->periodo) {
        array_push($conditions, ['documento.periodo', '=', $request->periodo]);
    }

    // Condición por tipo de documento
    if ($request->tipo) {
        array_push($conditions, ['documento.tipo', '=', $request->tipo]);
    }

    // Condición por aceptado
    if ($request->aceptado === 'null') {
        // Si aceptado es 'null', buscamos los documentos con valor null en la columna aceptado
        $query->whereNull('documento.aceptado');
    } elseif (isset($request->aceptado)) {
        // Si se proporciona un valor diferente de null (1 o 0), filtramos por ese valor
        array_push($conditions, ['documento.aceptado', '=', $request->aceptado]);
    }

    // Construir la consulta principal
    $query = Documento::select(
        'documento.id',
        'documento.tipo',
        'escuela.nombre AS escuela',
        'escuela.nombre_corto',
        'documento.fecha_subida',
        'documento.periodo',
        'users.nombres AS username',
        'users.apellidos AS userlastname',
        'documento.url',
        'documento.aceptado',
        'documento.aceptado as aceptados',
        'documento.obser'
    )
    ->join('escuela', 'documento.id_escuela', '=', 'escuela.id')
    ->join('users', 'users.id', '=', 'documento.id_usuario')
    ->where(function ($query) use ($request) {
        $query->where('escuela.nombre', 'LIKE', '%' . $request->term . '%')
            ->orWhere('documento.tipo', 'LIKE', '%' . $request->term . '%')
            ->orWhere('users.nombres', 'LIKE', '%' . $request->term . '%')
            ->orWhere('documento.aceptado', 'LIKE', '%' . $request->term . '%');
    });

    // Aplicar las condiciones adicionales si existen
    if (!empty($conditions)) {
        $query->where($conditions);
    }

    // Paginación de resultados
    $res = $query->paginate(200);

    // Respuesta en formato JSON
    return response()->json([
        'estado' => true,
        'datos' => $res,
    ], 200);
}

    public function ObservarDocumento(Request $request){

        $doc = Documento::find($request->id);
        $doc->obser = $request->obser;
        $doc->save();

        $this->response['tipo'] = 'info';
        $this->response['titulo'] = '!REGISTRO MODIFICADO!';
        $this->response['mensaje'] = ' ';
        $this->response['estado'] = true;
        $this->response['datos'] = $doc;

        return response()->json($this->response, 200);

    }

    public function cambiarEstado(Request $request){

        $doc = Documento::find($request->id);
        if($doc->aceptado  == 0 || $doc->aceptado == null ){
            $doc->aceptado = 1;
        }else{
            $doc->aceptado = 0;
        }
        $doc->save();
        $this->response['estado'] = true;
        $this->response['datos'] = $doc;

        return response()->json($this->response, 200);

    }


    public function getTestResultsvisor() {
        $competencias = DB::select("SELECT DISTINCT curso.id_competencia
        FROM curso
        ORDER BY curso.id_competencia ASC");

        $alumnos = [];

        foreach ($competencias as $competencia) {
            $res = DB::select("SELECT estudiante.id as estudiante, estudiante.dni, estudiante.nombres, curso_detalle.nota,
            estudiante.paterno, estudiante.materno, programa.programa, datos_ingreso.semestre AS semestre
            FROM curso
            JOIN curso_detalle ON curso.id = curso_detalle.id_curso
            JOIN estudiante ON estudiante.id = curso_detalle.id_alumno
            JOIN datos_ingreso ON estudiante.dni = datos_ingreso.dni
            JOIN programa ON datos_ingreso.id_programa=programa.id
            WHERE curso.id_competencia = :competencia
            ORDER BY programa.programa ASC, estudiante.paterno ASC;", ['competencia' => $competencia->id_competencia]);

            foreach ($res as $row) {
                $id = $row->estudiante;
                $nota = $row->nota;

                if (!isset($alumnos[$id])) {
                    $alumnos[$id] = [
                        'id_estudiante' => $row->estudiante,
                        'nombre' => $row->nombres,
                        'programa' => $row->programa,
                        'semestre' => $row->semestre,
                        'dni' => $row->dni,
                        'paterno' => $row->paterno,
                        'materno' => $row->materno,
                        'notas' => [],
                    ];
                }
                $alumnos[$id]['notas'][] = [
                    'nota' => $nota,
                    'competencia' => $competencia->id_competencia,
                ];
            }
        }

        $alumnos = array_values($alumnos);

        $this->response['estado'] = true;
        $this->response['datos'] = $alumnos;
        $this->response['competencias'] = $competencias;

        return response()->json($this->response, 200);
    }





}
