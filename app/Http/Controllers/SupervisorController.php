<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\CursoDetalle;
use App\Models\Alumno;
use App\Models\Escuela;
use App\Models\Documento;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


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

    public function cambiarPeriodo(Request $request){
    // Buscar el documento por ID
    $doc = Documento::find($request->id);

    // Verificar si el documento existe
    if (!$doc) {
        $this->response['tipo'] = 'error';
        $this->response['titulo'] = '!ERROR!';
        $this->response['mensaje'] = 'Documento no encontrado.';
        $this->response['estado'] = false;
        return response()->json($this->response, 404);
    }

    // Cambiar el período del documento
    $doc->periodo = $request->periodo;
    $doc->save();

    // Preparar la respuesta JSON
    $this->response['tipo'] = 'info';
    $this->response['titulo'] = '!PERIODO MODIFICADO!';
    $this->response['mensaje'] = ' ';
    $this->response['estado'] = true;
    $this->response['datos'] = $doc;

    // Devolver la respuesta en formato JSON
    return response()->json($this->response, 200);
}



    public function getTestResultsvisor() {
        $competencias = DB::select("SELECT DISTINCT curso.id_competencia
        FROM curso
        ORDER BY curso.id_competencia ASC");

        $alumnos = [];

        foreach ($competencias as $competencia) {
           //bdhh $res = DB::select("SELECT estudiante.id as estudiante, estudiante.dni, estudiante.nombres, curso_detalle.nota,
           //bdhh JOIN datos_ingreso ON estudiante.dni = datos_ingreso.dni
            $res = DB::select("SELECT estudiante.id as estudiante, estudiante.codigo_est, estudiante.nombres, curso_detalle.nota,
            estudiante.paterno, estudiante.materno, programa.programa, datos_ingreso.semestre AS semestre, escuela.filial
            FROM curso
            JOIN curso_detalle ON curso.id = curso_detalle.id_curso
            JOIN estudiante ON estudiante.id = curso_detalle.id_alumno
            JOIN datos_ingreso ON estudiante.codigo_est = datos_ingreso.codigo_est
            JOIN programa ON datos_ingreso.id_programa=programa.id
            JOIN escuela ON programa.id_escuela = escuela.id
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
                        'filial' => $row->filial,
                       //bdhh 'dni' => $row->dni,
                        'codigo_est' => $row->codigo_est,
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

    public function getDocentesCompetencias()
{
    try {
        $query = "
            SELECT
                c.nombre AS curso_nombre,
                c.grupo,
                d.paterno,
                d.materno,
                d.nombres,
                d.telefono,
                d.email,
                com.nombre AS competencia_nombre,
                com.cod AS competencia_cod,
                pro.id AS programa_id,
                p.id_periodo,
                p.nombre AS periodo_nombre,
                c.id_competencia,
                pro.programa,
                pro.escuela
            FROM curso AS c
            LEFT JOIN docente AS d ON c.id_docente = d.id
            JOIN competencia AS com ON c.id_competencia = com.id
            LEFT JOIN programa AS pro ON c.id_programa = pro.id
            JOIN periodo p ON p.id_periodo = c.id_periodo
            ORDER BY d.paterno ASC, d.materno ASC
        ";

        $docentes = DB::select($query);

        // Obtener opciones para los filtros
        $programas = DB::table('programa')->select('id AS programa_id', 'programa')->get();
        $periodos = DB::table('periodo')->select('id_periodo', 'nombre as periodo_nombre')->orderByDesc('id_periodo')->get();
        $competencias = DB::table('competencia')->select('id AS id_competencia', 'nombre AS competencia_nombre', 'cod AS competencia_cod')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'docentes' => $docentes,
                'filtros' => [
                    'programas' => $programas,
                    'periodos' => $periodos,
                    'competencias' => $competencias,
                ]
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al obtener los datos: ' . $e->getMessage()
        ], 500);
    }
}

public function busquedaEstudiantes(Request $request)
    {
        try {
            $search = $request->query('search', '');

            $query = "
                SELECT
                    estudiante.codigo_est,
                    estudiante.dni,
                    estudiante.nombres,
                    estudiante.paterno,
                    estudiante.materno,
                    estudiante.sexo,
                    estudiante.telefono,
                    estudiante.email,
                    estudiante.direccion,
                    datos_ingreso.semestre,
                    datos_ingreso.modalidad,
                    datos_ingreso.t_examen,
                    datos_ingreso.anio,
                    programa.programa AS nombre_programa,
                    programa.escuela AS nombre_escuela,
                    escuela.filial,
                    matriz.c1_R AS competencia_1,
                    matriz.c2_R AS competencia_2,
                    matriz.c3_R AS competencia_3,
                    matriz.c4_R AS competencia_4,
                    matriz.c5_R AS competencia_5,
                    matriz.c6_R AS competencia_6,
                    matriz.c7_R AS competencia_7,
                    matriz.c8_R AS competencia_8,
                    matriz.c9_R AS competencia_9,
                    matriz.c10_R AS competencia_10,
                    matriz.c11_R AS competencia_11
                FROM estudiante
                INNER JOIN datos_ingreso ON estudiante.codigo_est = datos_ingreso.codigo_est
                INNER JOIN programa ON datos_ingreso.id_programa = programa.id
                INNER JOIN escuela ON escuela.id = programa.id_escuela
                INNER JOIN matriz ON datos_ingreso.codigo_est = matriz.codigo_est
            ";

            // Si hay un término de búsqueda, agregar condiciones WHERE
            if ($search) {
                $query .= " WHERE estudiante.codigo_est LIKE ?
                           OR estudiante.dni LIKE ?
                           OR estudiante.nombres LIKE ?
                           OR estudiante.paterno LIKE ?
                           OR estudiante.materno LIKE ?
                           OR CONCAT(estudiante.nombres, ' ', estudiante.paterno, ' ', estudiante.materno) LIKE ?
                           OR CONCAT(estudiante.paterno, ' ', estudiante.materno, ' ', estudiante.nombres) LIKE ?";
                $searchTerm = "%$search%";
                $estudiantes = DB::select($query, [
                    $searchTerm, // codigo_est
                    $searchTerm, // dni
                    $searchTerm, // nombres
                    $searchTerm, // paterno
                    $searchTerm, // materno
                    $searchTerm, // nombres + apellidos
                    $searchTerm  // apellidos + nombres
                ]);
            } else {
                $estudiantes = DB::select($query);
            }

            return response()->json([
                'success' => true,
                'data' => $estudiantes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los datos: ' . $e->getMessage()
            ], 500);
        }
    }


public function generateReport(Request $request)
    {
        // Obtener filtros del request
        $periodo = $request->input('periodo');
        $tipo = $request->input('tipo');
        $escuela = $request->input('escuela');
        $estado = $request->input('estado');

        // Consulta principal para el reporte
        $query = Documento::select(
            'escuela.nombre AS escuela',
            'documento.periodo',
            'documento.tipo',
            \DB::raw("
                CASE
                    WHEN documento.aceptado = 1 THEN 'Aceptado'
                    WHEN documento.aceptado = 0 THEN 'Rechazado'
                    ELSE 'Pendiente'
                END AS estado
            "),
            \DB::raw('COUNT(*) AS cantidad')
        )
        ->join('escuela', 'documento.id_escuela', '=', 'escuela.id')
        ->groupBy('escuela.nombre', 'documento.periodo', 'documento.tipo', 'documento.aceptado');

        // Aplicar filtros si existen
        if ($periodo) {
            $query->where('documento.periodo', $periodo);
        }
        if ($tipo) {
            $query->where('documento.tipo', $tipo);
        }
        if ($escuela) {
            $query->where('escuela.nombre', $escuela);
        }
        if ($estado) {
            $query->where('documento.aceptado', $estado === 'Aceptado' ? 1 : ($estado === 'Rechazado' ? 0 : null));
        }

        $reporte = $query->orderBy('escuela.nombre')
                         ->orderBy('documento.periodo')
                         ->orderBy('documento.tipo')
                         ->orderBy('estado')
                         ->get();

        // Obtener datos para los filtros (selects en la vista)
        $periodos = Documento::select('periodo')->distinct()->orderBy('periodo')->pluck('periodo');
        $tipos = Documento::select('tipo')->distinct()->orderBy('tipo')->pluck('tipo');
        $escuelas = Escuela::select('nombre')->orderBy('nombre')->pluck('nombre');

        // Generar el PDF
        $pdf = PDF::loadView('reportes.documentos', compact('reporte', 'periodos', 'tipos', 'escuelas', 'periodo', 'tipo', 'escuela', 'estado'));

        return $pdf->stream('Reporte_Documentos.pdf');
       // return $pdf->download('Reporte_Documentos.pdf');

    }
}
