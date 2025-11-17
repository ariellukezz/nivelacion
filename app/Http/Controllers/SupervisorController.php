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
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;


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
public function getDocumentos(Request $request)
    {
        $term   = trim((string) $request->input('term', ''));
        $pageSz = (int) ($request->input('paginashoja') ?: 10);

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
        ->join('users', 'users.id', '=', 'documento.id_usuario');

        // Búsqueda libre
        if ($term !== '') {
            $query->where(function ($q) use ($term) {
                $q->where('escuela.nombre', 'LIKE', "%{$term}%")
                  ->orWhere('documento.tipo', 'LIKE', "%{$term}%")
                  ->orWhere('users.nombres', 'LIKE', "%{$term}%")
                  ->orWhere('users.apellidos', 'LIKE', "%{$term}%")
                  ->orWhereRaw("CAST(documento.aceptado AS CHAR) LIKE ?", ["%{$term}%"]);
            });
        }

        // Filtros (si llegan)
        if ($request->filled('programa')) {
            $query->where('documento.id_escuela', '=', $request->programa);
        }
        if ($request->filled('periodo')) {
            // documento.periodo es un texto tipo '2025-I'
            $query->where('documento.periodo', '=', $request->periodo);
        }
        if ($request->filled('tipo')) {
            $query->where('documento.tipo', '=', $request->tipo);
        }
        // Aceptado: 1, 0 o null ('null' string -> null real)
        if ($request->exists('aceptado')) {
            $val = $request->input('aceptado');
            if ($val === 'null' || is_null($val)) {
                $query->whereNull('documento.aceptado');
            } else {
                $query->where('documento.aceptado', '=', (int)$val);
            }
        }

        // Orden: pendientes primero (NULL), luego por fecha
        $query->orderByRaw('documento.aceptado IS NULL DESC')
              ->orderByDesc('documento.fecha_subida');

        $res = $query->paginate($pageSz);

        return response()->json([
            'estado' => true,
            'datos'  => $res,
        ], 200);
    }

    /**
     * Devuelve periodos desde la tabla `periodo`
     * Formato para dropdown: [{value:nombre, label:nombre}, ...]
     */
    public function getPeriodos()
    {
        $rows = DB::table('periodo')
            ->orderByDesc('id_periodo') // ordena 5, 4, 3...
            ->get(['id_periodo', 'nombre', 'estado']);

        $options = $rows->map(fn($r) => [
            'value' => $r->nombre,
            'label' => $r->nombre
        ]);

        return response()->json([
            'estado'  => true,
            'raw'     => $rows,
            'options' => $options,
        ], 200);
    }

    /**
     * Guardar observación y, opcionalmente, cambiar estado (aceptado).
     * - Enviar aceptado = 1, 0 o 'null' (string) para ponerlo en NULL (Pendiente).
     */
    public function ObservarDocumento(Request $request)
    {
        $doc = Documento::find($request->id);
        if (!$doc) {
            return response()->json([
                'tipo'   => 'error',
                'titulo' => '!ERROR!',
                'mensaje'=> 'Documento no encontrado.',
                'estado' => false,
            ], 404);
        }

        // Observación (permite string vacío)
        if ($request->has('obser')) {
            $doc->obser = $request->obser;
        }

        // Estado (opcional): 1, 0 o null
        if ($request->has('aceptado')) {
            $val = $request->input('aceptado');
            $doc->aceptado = ($val === 'null' || is_null($val)) ? null : (int)$val;
        }

        $doc->save();

        return response()->json([
            'tipo'   => 'info',
            'titulo' => '!REGISTRO MODIFICADO!',
            'mensaje'=> ' ',
            'estado' => true,
            'datos'  => [
                'id'       => $doc->id,
                'obser'    => $doc->obser,
                'aceptado' => $doc->aceptado,
                'label'    => is_null($doc->aceptado) ? 'Pendiente' : ($doc->aceptado ? 'Aceptado' : 'Rechazado'),
            ],
        ], 200);
    }

    /**
     * Cambiar estado del documento.
     * - Si llega 'aceptado', se asigna explícito (1, 0 o 'null' -> NULL).
     * - Si NO llega 'aceptado', se hace toggle (NULL/0 -> 1 ; 1 -> 0).
     */
    public function cambiarEstado(Request $request)
    {
        $doc = Documento::find($request->id);
        if (!$doc) {
            return response()->json([
                'estado' => false,
                'mensaje'=> 'Documento no encontrado.',
            ], 404);
        }

        if ($request->has('aceptado')) {
            $val = $request->input('aceptado');
            $doc->aceptado = ($val === 'null' || is_null($val)) ? null : (int)$val;
        } else {
            // Toggle simple si no especifican estado
            $doc->aceptado = ($doc->aceptado == 1) ? 0 : 1;
        }

        $doc->save();

        return response()->json([
            'estado' => true,
            'datos'  => [
                'id'       => $doc->id,
                'aceptado' => $doc->aceptado,
                'label'    => is_null($doc->aceptado) ? 'Pendiente' : ($doc->aceptado ? 'Aceptado' : 'Rechazado'),
            ],
        ], 200);
    }

    /**
     * Cambiar periodo (texto) del documento.
     * - Opcional: validar que exista en tabla `periodo.nombre`.
     */
    public function cambiarPeriodo(Request $request)
    {
        $doc = Documento::find($request->id);
        if (!$doc) {
            return response()->json([
                'tipo'   => 'error',
                'titulo' => '!ERROR!',
                'mensaje'=> 'Documento no encontrado.',
                'estado' => false,
            ], 404);
        }

        $doc->periodo = $request->periodo;
        $doc->save();

        return response()->json([
            'tipo'   => 'info',
            'titulo' => '!PERIODO MODIFICADO!',
            'mensaje'=> ' ',
            'estado' => true,
            'datos'  => $doc,
        ], 200);
    }

public function eliminarDocumento(Request $request)
{
    $request->validate([
        'id' => 'required|integer|exists:documento,id',
    ]);

    $doc = Documento::find($request->id);
    if (!$doc) {
        return response()->json([
            'estado'  => false,
            'tipo'    => 'error',
            'titulo'  => '!ERROR!',
            'mensaje' => 'Documento no encontrado.',
        ], 404);
    }

    // --- 1) Normalizar ruta relativa guardada en BD ---
    // En tu BD guardas algo como: "documentos/resoluciones/ESCUELA/archivo.pdf"
    // (sin "../" al inicio). Nos aseguramos de eso aquí:
    $relative = $doc->url ? trim(str_replace('\\', '/', $doc->url)) : '';
    $relative = ltrim($relative, '/');                 // quitar "/" inicial si lo hubiera
    $relative = str_replace(['..'], '', $relative);    // seguridad básica

    // --- 2) Rutas posibles a probar ---
    $pathsToTry = [];
    // Tu caso principal: archivos subidos a public/<url>
    $pathsToTry[] = public_path($relative);

    // Si algún tipo lo guardaste en "storage/..." y lo sirves por el symlink public/storage:
    if (Str::startsWith($relative, 'storage/')) {
        $pathsToTry[] = storage_path('app/public/' . substr($relative, 8)); // quita "storage/"
    }

    // (Opcional) otra variante si alguien guardó con "public/" delante
    if (Str::startsWith($relative, 'public/')) {
        $pathsToTry[] = base_path($relative);              // /path/app/public/...
        $pathsToTry[] = public_path(substr($relative, 7)); // quita "public/"
    }

    // --- 3) Borrar el archivo si existe en alguna ---
    $fileExisted = false;
    $fileDeleted = false;

    foreach ($pathsToTry as $abs) {
        try {
            if (is_file($abs)) {
                $fileExisted = true;
                if (@unlink($abs)) {       // o File::delete($abs)
                    $fileDeleted = true;
                    break;
                }
            }
        } catch (\Throwable $e) {
            // opcional: \Log::warning('Delete failed: '.$abs.' -> '.$e->getMessage());
        }
    }

    // --- 4) Borrar el registro en BD ---
    $doc->delete();

    return response()->json([
        'estado'      => true,
        'tipo'        => 'success',
        'titulo'      => 'Eliminado',
        'mensaje'     => 'Documento eliminado ' . ($fileDeleted ? 'y archivo borrado.' : '(archivo no encontrado o sin permisos).'),
        'debug'       => [
            // Útil si quieres inspeccionar en red; elimínalo en producción
            // 'paths_checked' => $pathsToTry,
            'file_existed'  => $fileExisted,
            'file_deleted'  => $fileDeleted,
        ],
    ], 200);
}


public function getTestResultsvisor(Request $request)
{
    // 1. Periodo (por parámetro o activo)
    $idPeriodo = $request->input('periodo');

    if (!$idPeriodo) {
        $periodoActivo = DB::table('periodo')->where('estado', 'activo')->first();

        if (!$periodoActivo) {
            return response()->json([
                'estado'  => false,
                'mensaje' => 'No existe un periodo activo',
            ], 400);
        }

        $idPeriodo = $periodoActivo->id_periodo;
    }

    // 2. Consulta pivot con AP / S/N / nota
    $sql = "
        SELECT
            t.id_estudiante,
            t.codigo_est,
            t.paterno,
            t.materno,
            t.nombres AS nombre,
            t.programa,
            t.semestre,
            t.filial,

            CASE
                WHEN t.C1_has = 1 AND t.C1_nota IS NULL THEN 'S/N'
                WHEN t.C1_has = 1 AND t.C1_nota IS NOT NULL THEN CAST(t.C1_nota AS CHAR)
                ELSE 'AP'
            END AS C1,

            CASE
                WHEN t.C2_has = 1 AND t.C2_nota IS NULL THEN 'S/N'
                WHEN t.C2_has = 1 AND t.C2_nota IS NOT NULL THEN CAST(t.C2_nota AS CHAR)
                ELSE 'AP'
            END AS C2,

            CASE
                WHEN t.C3_has = 1 AND t.C3_nota IS NULL THEN 'S/N'
                WHEN t.C3_has = 1 AND t.C3_nota IS NOT NULL THEN CAST(t.C3_nota AS CHAR)
                ELSE 'AP'
            END AS C3,

            CASE
                WHEN t.C4_has = 1 AND t.C4_nota IS NULL THEN 'S/N'
                WHEN t.C4_has = 1 AND t.C4_nota IS NOT NULL THEN CAST(t.C4_nota AS CHAR)
                ELSE 'AP'
            END AS C4,

            CASE
                WHEN t.C5_has = 1 AND t.C5_nota IS NULL THEN 'S/N'
                WHEN t.C5_has = 1 AND t.C5_nota IS NOT NULL THEN CAST(t.C5_nota AS CHAR)
                ELSE 'AP'
            END AS C5,

            CASE
                WHEN t.C6_has = 1 AND t.C6_nota IS NULL THEN 'S/N'
                WHEN t.C6_has = 1 AND t.C6_nota IS NOT NULL THEN CAST(t.C6_nota AS CHAR)
                ELSE 'AP'
            END AS C6,

            CASE
                WHEN t.C7_has = 1 AND t.C7_nota IS NULL THEN 'S/N'
                WHEN t.C7_has = 1 AND t.C7_nota IS NOT NULL THEN CAST(t.C7_nota AS CHAR)
                ELSE 'AP'
            END AS C7,

            CASE
                WHEN t.C8_has = 1 AND t.C8_nota IS NULL THEN 'S/N'
                WHEN t.C8_has = 1 AND t.C8_nota IS NOT NULL THEN CAST(t.C8_nota AS CHAR)
                ELSE 'AP'
            END AS C8,

            CASE
                WHEN t.C9_has = 1 AND t.C9_nota IS NULL THEN 'S/N'
                WHEN t.C9_has = 1 AND t.C9_nota IS NOT NULL THEN CAST(t.C9_nota AS CHAR)
                ELSE 'AP'
            END AS C9,

            CASE
                WHEN t.C10_has = 1 AND t.C10_nota IS NULL THEN 'S/N'
                WHEN t.C10_has = 1 AND t.C10_nota IS NOT NULL THEN CAST(t.C10_nota AS CHAR)
                ELSE 'AP'
            END AS C10,

            CASE
                WHEN t.C11_has = 1 AND t.C11_nota IS NULL THEN 'S/N'
                WHEN t.C11_has = 1 AND t.C11_nota IS NOT NULL THEN CAST(t.C11_nota AS CHAR)
                ELSE 'AP'
            END AS C11

        FROM (
            SELECT
                est.id AS id_estudiante,
                est.codigo_est,
                est.paterno,
                est.materno,
                est.nombres,
                prog.programa,
                di.semestre,
                esc.filial,

                MAX(CASE WHEN curso.id_competencia = 1  THEN cd.nota END) AS C1_nota,
                MAX(CASE WHEN curso.id_competencia = 2  THEN cd.nota END) AS C2_nota,
                MAX(CASE WHEN curso.id_competencia = 3  THEN cd.nota END) AS C3_nota,
                MAX(CASE WHEN curso.id_competencia = 4  THEN cd.nota END) AS C4_nota,
                MAX(CASE WHEN curso.id_competencia = 5  THEN cd.nota END) AS C5_nota,
                MAX(CASE WHEN curso.id_competencia = 6  THEN cd.nota END) AS C6_nota,
                MAX(CASE WHEN curso.id_competencia = 7  THEN cd.nota END) AS C7_nota,
                MAX(CASE WHEN curso.id_competencia = 8  THEN cd.nota END) AS C8_nota,
                MAX(CASE WHEN curso.id_competencia = 9  THEN cd.nota END) AS C9_nota,
                MAX(CASE WHEN curso.id_competencia = 10 THEN cd.nota END) AS C10_nota,
                MAX(CASE WHEN curso.id_competencia = 11 THEN cd.nota END) AS C11_nota,

                MAX(CASE WHEN curso.id_competencia = 1  THEN 1 END) AS C1_has,
                MAX(CASE WHEN curso.id_competencia = 2  THEN 1 END) AS C2_has,
                MAX(CASE WHEN curso.id_competencia = 3  THEN 1 END) AS C3_has,
                MAX(CASE WHEN curso.id_competencia = 4  THEN 1 END) AS C4_has,
                MAX(CASE WHEN curso.id_competencia = 5  THEN 1 END) AS C5_has,
                MAX(CASE WHEN curso.id_competencia = 6  THEN 1 END) AS C6_has,
                MAX(CASE WHEN curso.id_competencia = 7  THEN 1 END) AS C7_has,
                MAX(CASE WHEN curso.id_competencia = 8  THEN 1 END) AS C8_has,
                MAX(CASE WHEN curso.id_competencia = 9  THEN 1 END) AS C9_has,
                MAX(CASE WHEN curso.id_competencia = 10 THEN 1 END) AS C10_has,
                MAX(CASE WHEN curso.id_competencia = 11 THEN 1 END) AS C11_has

            FROM curso
            JOIN curso_detalle cd ON curso.id = cd.id_curso
            JOIN estudiante est ON est.id = cd.id_alumno
            JOIN datos_ingreso di ON est.codigo_est = di.codigo_est
            JOIN programa prog ON di.id_programa = prog.id
            JOIN escuela esc ON prog.id_escuela = esc.id
            JOIN periodo p ON curso.id_periodo = p.id_periodo

            WHERE p.id_periodo = :periodo

            GROUP BY
                est.id,
                est.codigo_est,
                est.paterno,
                est.materno,
                est.nombres,
                prog.programa,
                di.semestre,
                esc.filial
        ) AS t

        ORDER BY
            t.programa ASC,
            t.paterno ASC
    ";

    $alumnos = DB::select($sql, ['periodo' => $idPeriodo]);

    return response()->json([
        'estado'           => true,
        'datos'            => $alumnos,
        'competencias'     => [],          // ya no las usas en el Vue
        'periodo_filtrado' => $idPeriodo,
    ], 200);
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



     public function listarEncargadosSistema(Request $request)
    {
        $q = trim($request->query('q', ''));

        $sql = "
            SELECT
                es.nombres,
                es.apellidos,
                es.correo,
                es.num_celular,
                es.cargo,
                es.estado,
                es.observaciones,
                p.escuela AS escuela
            FROM encargados_sistema es
            INNER JOIN users    us ON es.usuario_id = us.id
            INNER JOIN programa p  ON us.id_escuela = p.id_escuela
        ";

        $params = [];
        if ($q !== '') {
            $sql .= "
                WHERE es.nombres LIKE ?
                   OR es.apellidos LIKE ?
                   OR CONCAT(es.nombres, ' ', es.apellidos) LIKE ?
                   OR CONCAT(es.apellidos, ' ', es.nombres) LIKE ?
                   OR p.escuela LIKE ?
            ";
            $like = "%{$q}%";
            $params = [$like, $like, $like, $like, $like];
        }

        $sql .= " ORDER BY p.escuela ASC, es.apellidos ASC, es.nombres ASC";

        $rows = DB::select($sql, $params);

        return response()->json([
            'success' => true,
            'data'    => $rows,
        ], 200);
    }



public function getDashboardDocentes(Request $request)
{
    $idPeriodo = $request->input('periodo');
    $idPrograma = $request->input('programa');

    if (!$idPeriodo) {
        $periodoActivo = DB::table('periodo')->where('estado', 'activo')->first();
        if (!$periodoActivo) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'No existe un periodo activo',
            ], 400);
        }
        $idPeriodo = $periodoActivo->id_periodo;
    }

    // Competencias sin docente - CORREGIDA
    $sqlCompetenciasSinDocente = "
        SELECT
            comp.id as competencia_id,
            comp.nombre as competencia_nombre,
            p.programa,
            esc.filial,
            COUNT(DISTINCT est.id) as estudiantes_afectados,
            COUNT(DISTINCT CASE WHEN cd.nota IS NULL THEN est.id END) as estudiantes_sin_nota
        FROM curso c
        JOIN competencia comp ON c.id_competencia = comp.id
        JOIN programa p ON c.id_programa = p.id
        JOIN escuela esc ON p.id_escuela = esc.id
        JOIN curso_detalle cd ON c.id = cd.id_curso
        JOIN estudiante est ON cd.id_alumno = est.id
        JOIN periodo per ON c.id_periodo = per.id_periodo
        WHERE per.id_periodo = :periodo
        AND (cd.nota IS NULL OR cd.nota BETWEEN 0 AND 20)  -- Incluir S/N y notas 0-20
        AND c.id_docente IS NULL
    ";

    $params = ['periodo' => $idPeriodo];

    if ($idPrograma) {
        $sqlCompetenciasSinDocente .= " AND p.id = :programa";
        $params['programa'] = $idPrograma;
    }

    $sqlCompetenciasSinDocente .= "
        GROUP BY comp.id, comp.nombre, p.programa, esc.filial
        ORDER BY estudiantes_afectados DESC
        LIMIT 10
    ";

    $competenciasSinDocente = DB::select($sqlCompetenciasSinDocente, $params);

    // Docentes que faltan subir notas - CORREGIDA
    $sqlDocentesFaltanNotas = "
        SELECT
            doc.id as docente_id,
            CONCAT(doc.paterno, ' ', doc.materno, ', ', doc.nombres) as docente_nombre,
            comp.nombre as competencia_nombre,
            p.programa,
            esc.filial,
            COUNT(DISTINCT est.id) as total_estudiantes,
            COUNT(DISTINCT CASE WHEN cd.nota IS NULL THEN est.id END) as estudiantes_sin_nota,
            COUNT(DISTINCT CASE WHEN cd.nota BETWEEN 0 AND 20 THEN est.id END) as estudiantes_con_nota,
            ROUND((COUNT(DISTINCT CASE WHEN cd.nota IS NULL THEN est.id END) * 100.0 / COUNT(DISTINCT est.id)), 2) as porcentaje_sin_nota
        FROM curso c
        JOIN docente doc ON c.id_docente = doc.id
        JOIN competencia comp ON c.id_competencia = comp.id
        JOIN programa p ON c.id_programa = p.id
        JOIN escuela esc ON p.id_escuela = esc.id
        JOIN curso_detalle cd ON c.id = cd.id_curso
        JOIN estudiante est ON cd.id_alumno = est.id
        JOIN periodo per ON c.id_periodo = per.id_periodo
        WHERE per.id_periodo = :periodo
        AND (cd.nota IS NULL OR cd.nota BETWEEN 0 AND 20)  -- Incluir S/N y notas 0-20
        AND c.id_docente IS NOT NULL
    ";

    if ($idPrograma) {
        $sqlDocentesFaltanNotas .= " AND p.id = :programa";
    }

    $sqlDocentesFaltanNotas .= "
        GROUP BY doc.id, doc.paterno, doc.materno, doc.nombres, comp.nombre, p.programa, esc.filial
        HAVING estudiantes_sin_nota > 0  -- Solo mostrar docentes que tienen estudiantes sin nota
        ORDER BY estudiantes_sin_nota DESC
        LIMIT 10
    ";

    $docentesFaltanNotas = DB::select($sqlDocentesFaltanNotas, $params);

    // Estadísticas generales - CORREGIDA
    $sqlEstadisticas = "
        SELECT
            COUNT(DISTINCT CASE WHEN c.id_docente IS NULL AND (cd.nota IS NULL OR cd.nota BETWEEN 0 AND 20) THEN c.id_competencia END) as competencias_sin_docente,
            COUNT(DISTINCT CASE WHEN c.id_docente IS NOT NULL AND cd.nota IS NULL THEN c.id_docente END) as docentes_faltan_notas,
            COUNT(DISTINCT CASE WHEN cd.nota IS NULL THEN est.id END) as total_estudiantes_sin_nota,
            COUNT(DISTINCT p.id) as programas_afectados
        FROM curso c
        JOIN programa p ON c.id_programa = p.id
        JOIN curso_detalle cd ON c.id = cd.id_curso
        JOIN estudiante est ON cd.id_alumno = est.id
        JOIN periodo per ON c.id_periodo = per.id_periodo
        WHERE per.id_periodo = :periodo
        AND (cd.nota IS NULL OR cd.nota BETWEEN 0 AND 20)  -- Incluir S/N y notas 0-20
    ";

    if ($idPrograma) {
        $sqlEstadisticas .= " AND p.id = :programa";
    }

    $estadisticas = DB::select($sqlEstadisticas, $params);

    return response()->json([
        'estado' => true,
        'estadisticas' => $estadisticas[0] ?? [],
        'competencias_sin_docente' => $competenciasSinDocente,
        'docentes_faltan_notas' => $docentesFaltanNotas,
        'periodo_filtrado' => $idPeriodo,
    ], 200);
}


public function getCompetenciasSinDocente(Request $request)
{
    $idPeriodo = $request->input('periodo');
    $idPrograma = $request->input('programa');

    if (!$idPeriodo) {
        $periodoActivo = DB::table('periodo')->where('estado', 'activo')->first();
        if (!$periodoActivo) {
            return response()->json([
                'estado'  => false,
                'mensaje' => 'No existe un periodo activo',
            ], 400);
        }
        $idPeriodo = $periodoActivo->id_periodo;
    }

    $sql = "
        SELECT
            comp.id as competencia_id,
            comp.nombre as competencia_nombre,
            p.programa,
            esc.filial,
            COUNT(DISTINCT est.id) as total_estudiantes,
            COUNT(DISTINCT CASE WHEN cd.nota IS NULL THEN est.id END) as estudiantes_sin_nota,
            COUNT(DISTINCT CASE WHEN cd.nota BETWEEN 0 AND 20 THEN est.id END) as estudiantes_con_nota,
            ROUND((COUNT(DISTINCT CASE WHEN cd.nota IS NULL THEN est.id END) * 100.0 / COUNT(DISTINCT est.id)), 2) as porcentaje_sin_nota
        FROM curso c
        JOIN competencia comp ON c.id_competencia = comp.id
        JOIN programa p ON c.id_programa = p.id
        JOIN escuela esc ON p.id_escuela = esc.id
        JOIN curso_detalle cd ON c.id = cd.id_curso
        JOIN estudiante est ON cd.id_alumno = est.id
        JOIN periodo per ON c.id_periodo = per.id_periodo
        WHERE per.id_periodo = :periodo
        AND (cd.nota IS NULL OR cd.nota BETWEEN 0 AND 20)  -- Incluir S/N y notas 0-20
        AND c.id_docente IS NULL
    ";

    $params = ['periodo' => $idPeriodo];

    if ($idPrograma) {
        $sql .= " AND p.id = :programa";
        $params['programa'] = $idPrograma;
    }

    $sql .= "
        GROUP BY comp.id, comp.nombre, p.programa, esc.filial
        ORDER BY estudiantes_sin_nota DESC
    ";

    $competencias = DB::select($sql, $params);

    return response()->json([
        'estado' => true,
        'datos' => $competencias,
        'periodo_filtrado' => $idPeriodo,
    ], 200);
}

// Función para obtener docentes que faltan subir notas - CORREGIDA
public function getDocentesFaltanNotas(Request $request)
{
    $idPeriodo = $request->input('periodo');
    $idPrograma = $request->input('programa');

    if (!$idPeriodo) {
        $periodoActivo = DB::table('periodo')->where('estado', 'activo')->first();
        if (!$periodoActivo) {
            return response()->json([
                'estado'  => false,
                'mensaje' => 'No existe un periodo activo',
            ], 400);
        }
        $idPeriodo = $periodoActivo->id_periodo;
    }

    $sql = "
        SELECT
            doc.id as docente_id,
            CONCAT(doc.paterno, ' ', doc.materno, ', ', doc.nombres) as docente_nombre,
            comp.nombre as competencia_nombre,
            c.id_competencia,
            p.programa,
            esc.filial,
            COUNT(DISTINCT est.id) as total_estudiantes,
            COUNT(DISTINCT CASE WHEN cd.nota IS NULL THEN est.id END) as estudiantes_sin_nota,
            COUNT(DISTINCT CASE WHEN cd.nota BETWEEN 0 AND 20 THEN est.id END) as estudiantes_con_nota,
            ROUND((COUNT(DISTINCT CASE WHEN cd.nota IS NULL THEN est.id END) * 100.0 / COUNT(DISTINCT est.id)), 2) as porcentaje_sin_nota,
            ROUND((COUNT(DISTINCT CASE WHEN cd.nota BETWEEN 0 AND 20 THEN est.id END) * 100.0 / COUNT(DISTINCT est.id)), 2) as porcentaje_con_nota
        FROM curso c
        JOIN docente doc ON c.id_docente = doc.id
        JOIN competencia comp ON c.id_competencia = comp.id
        JOIN programa p ON c.id_programa = p.id
        JOIN escuela esc ON p.id_escuela = esc.id
        JOIN curso_detalle cd ON c.id = cd.id_curso
        JOIN estudiante est ON cd.id_alumno = est.id
        JOIN periodo per ON c.id_periodo = per.id_periodo
        WHERE per.id_periodo = :periodo
        AND (cd.nota IS NULL OR cd.nota BETWEEN 0 AND 20)  -- Incluir S/N y notas 0-20
        AND c.id_docente IS NOT NULL
    ";

    $params = ['periodo' => $idPeriodo];

    if ($idPrograma) {
        $sql .= " AND p.id = :programa";
        $params['programa'] = $idPrograma;
    }

    $sql .= "
        GROUP BY
            doc.id,
            doc.paterno,
            doc.materno,
            doc.nombres,
            comp.nombre,
            c.id_competencia,
            p.programa,
            esc.filial
        HAVING estudiantes_sin_nota > 0  -- Solo mostrar docentes que tienen estudiantes sin nota
        ORDER BY estudiantes_sin_nota DESC
    ";

    $docentes = DB::select($sql, $params);

    return response()->json([
        'estado' => true,
        'datos' => $docentes,
        'periodo_filtrado' => $idPeriodo,
    ], 200);
}
    // Nueva función para obtener programas con filial
    public function getProgramasConFilial()
    {
        try {
            $programas = DB::select("
                SELECT p.id, p.programa, e.filial
                FROM programa p
                INNER JOIN escuela e ON p.id_escuela = e.id
                ORDER BY p.programa ASC
            ");

            return response()->json([
                'estado' => true,
                'programas' => $programas
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'Error al obtener programas: ' . $e->getMessage()
            ], 500);
        }
    }





public function getReporteMatriculados(Request $request)
{
    try {
        $idPeriodo = $request->input('periodo');
        $idPrograma = $request->input('programa');
        $idCompetencia = $request->input('competencia');
        $filial = $request->input('filial');

        // Si no se envía periodo, usar el activo
        if (!$idPeriodo) {
            $periodoActivo = DB::table('periodo')->where('estado', 'activo')->first();
            if (!$periodoActivo) {
                return response()->json([
                    'estado' => false,
                    'mensaje' => 'No existe un periodo activo',
                ], 400);
            }
            $idPeriodo = $periodoActivo->id_periodo;
        }

        $sql = "
            SELECT
                p.id as programa_id,
                p.programa,
                e.filial,
                comp.id as competencia_id,
                comp.cod as competencia_codigo,
                comp.nombre as competencia_nombre,
                CASE
                    WHEN c.id_docente IS NULL THEN 'SIN DOCENTE'
                    ELSE CONCAT(doc.paterno, ' ', doc.materno, ', ', doc.nombres)
                END as docente_nombre,
                COUNT(DISTINCT est.id) as total_matriculados,
                COUNT(DISTINCT CASE WHEN cd.nota IS NULL THEN est.id END) as estudiantes_sin_nota,
                COUNT(DISTINCT CASE WHEN cd.nota BETWEEN 0 AND 20 THEN est.id END) as estudiantes_con_nota,
                CASE
                    WHEN COUNT(DISTINCT est.id) > 0 THEN
                        ROUND((COUNT(DISTINCT CASE WHEN cd.nota BETWEEN 0 AND 20 THEN est.id END) * 100.0 / COUNT(DISTINCT est.id)), 2)
                    ELSE 0
                END as porcentaje_con_nota
            FROM programa p
            CROSS JOIN competencia comp
            LEFT JOIN curso c ON c.id_programa = p.id
                AND c.id_competencia = comp.id
                AND c.id_periodo = :periodo
            LEFT JOIN escuela e ON p.id_escuela = e.id
            LEFT JOIN curso_detalle cd ON c.id = cd.id_curso
            LEFT JOIN estudiante est ON cd.id_alumno = est.id
            LEFT JOIN docente doc ON c.id_docente = doc.id
            WHERE (cd.nota IS NULL OR cd.nota BETWEEN 0 AND 20 OR cd.id IS NULL)
        ";

        $params = ['periodo' => $idPeriodo];

        // Aplicar filtros adicionales
        if ($idPrograma) {
            $sql .= " AND p.id = :programa";
            $params['programa'] = $idPrograma;
        }

        if ($idCompetencia) {
            $sql .= " AND comp.id = :competencia";
            $params['competencia'] = $idCompetencia;
        }

        if ($filial) {
            $sql .= " AND e.filial = :filial";
            $params['filial'] = $filial;
        }

        $sql .= "
            GROUP BY
                p.id, p.programa, e.filial,
                comp.id, comp.cod, comp.nombre,
                docente_nombre
            ORDER BY
                p.programa ASC,
                comp.id ASC
        ";

        $reporte = DB::select($sql, $params);

        return response()->json([
            'estado' => true,
            'datos' => $reporte,
            'periodo_filtrado' => $idPeriodo,
            'total_registros' => count($reporte)
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'estado' => false,
            'mensaje' => 'Error al generar el reporte: ' . $e->getMessage()
        ], 500);
    }
}




}
