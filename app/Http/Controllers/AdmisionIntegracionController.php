<?php

namespace App\Http\Controllers;

use App\Models\AdmisionMatriz;
use App\Models\AdmisionPostulante;
use App\Models\AdmisionProceso;
use App\Services\AdmisionApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdmisionIntegracionController extends Controller
{
    public function index()
    {
        $periodos = DB::table('periodo')
            ->select('id_periodo', 'nombre', 'estado')
            ->orderByDesc('id_periodo')
            ->get();

        return Inertia::render('Superadmi/Admision/Index', [
            'periodos' => $periodos
        ]);
    }

    public function procesos()
    {
        $datos = AdmisionProceso::query()
            ->leftJoin('periodo as pe', 'pe.id_periodo', '=', 'admision_proceso.id_periodo')
            ->select(
                'admision_proceso.*',
                'pe.nombre as periodo_nombre'
            )
            ->orderByDesc('admision_proceso.id_admision')
            ->get();

        return response()->json(['estado' => true, 'datos' => $datos]);
    }

    public function sincronizarProcesos(AdmisionApiService $api)
    {
        $response = $api->procesos();
        $items = $response['res'] ?? [];
        $total = 0;

        foreach ($items as $item) {
            $nombre = $item['nombre'] ?? '';
            $semestre = null;

            if (preg_match('/(20\d{2}-(?:I|II))/i', $nombre, $match)) {
                $semestre = strtoupper($match[1]);
            }

            AdmisionProceso::updateOrCreate(
                ['id_admision' => $item['id']],
                [
                    'nombre' => $nombre,
                    'slug' => $item['slug'] ?? null,
                    'anio' => $item['anio'] ?? null,
                    'estado_api' => $item['estado'] ?? null,
                    'fec_fin' => $item['fec_fin'] ?? null,
                    'id_sede_filial' => $item['id_sede_filial'] ?? null,
                    'fecha_examen' => $item['fecha_examen'] ?? null,
                    'url' => $item['url'] ?? null,
                    'fec_1' => $item['fec_1'] ?? null,
                    'fec_2' => $item['fec_2'] ?? null,
                    'semestre_detectado' => $semestre,
                    'sincronizado_at' => now()
                ]
            );

            $total++;
        }

        return response()->json([
            'estado' => true,
            'tipo' => 'success',
            'titulo' => 'PROCESOS ACTUALIZADOS',
            'mensaje' => "Se sincronizaron {$total} procesos de Admisión."
        ]);
    }

    public function asignarPeriodo(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:admision_proceso,id',
            'id_periodo' => 'nullable|integer|exists:periodo,id_periodo'
        ]);

        $proceso = AdmisionProceso::findOrFail($request->id);
        $proceso->id_periodo = $request->id_periodo;
        $proceso->save();

        return response()->json([
            'estado' => true,
            'tipo' => 'success',
            'titulo' => 'PERIODO ASIGNADO',
            'mensaje' => 'El proceso quedó relacionado con el periodo de Nivelación.'
        ]);
    }

    public function programas(AdmisionApiService $api)
    {
        $response = $api->programas();
        $items = $response['data'] ?? [];

        $locales = DB::table('programa')
            ->whereNotNull('id_admision')
            ->select('id', 'programa', 'escuela', 'id_admision')
            ->get()
            ->keyBy('id_admision');

        $datos = collect($items)->map(function ($item) use ($locales) {
            $local = $locales->get($item['id']);

            return [
                'id_admision' => $item['id'],
                'siu' => $item['siu'] ?? null,
                'nombre_admision' => $item['nombre'] ?? null,
                'id_programa_nivelacion' => $local->id ?? null,
                'programa_nivelacion' => $local->programa ?? null,
                'escuela' => $local->escuela ?? null,
                'vinculado' => (bool) $local
            ];
        })->values();

        return response()->json([
            'estado' => true,
            'datos' => $datos,
            'total' => $datos->count(),
            'sin_vincular' => $datos->where('vinculado', false)->count()
        ]);
    }

    public function sincronizarPostulantes(Request $request, AdmisionApiService $api)
    {
        $request->validate([
            'id_proceso_admision' => 'required|integer',
            'id_programa_admision' => 'required|integer'
        ]);

        $proceso = AdmisionProceso::where(
            'id_admision',
            $request->id_proceso_admision
        )->first();

        if (!$proceso) {
            return response()->json([
                'estado' => false,
                'tipo' => 'warn',
                'titulo' => 'PROCESO NO SINCRONIZADO',
                'mensaje' => 'Primero actualice la lista de procesos de Admisión.'
            ]);
        }

        $programaLocal = DB::table('programa')
            ->where('id_admision', $request->id_programa_admision)
            ->select('id', 'programa')
            ->first();

        if (!$programaLocal) {
            return response()->json([
                'estado' => false,
                'tipo' => 'warn',
                'titulo' => 'PROGRAMA SIN EQUIVALENCIA',
                'mensaje' => 'El programa de Admisión no tiene id_admision relacionado en la tabla programa.'
            ]);
        }

        $response = $api->postulantes(
            (int) $request->id_proceso_admision,
            (int) $request->id_programa_admision
        );

        $items = $response['data'] ?? [];

        $totalApi = count($items);
        $insertados = 0;
        $actualizados = 0;
        $sinCodigo = [];
        $omitidosSinDni = 0;

        // Limpia registros antiguos que hubieran quedado con código nulo
        // en versiones anteriores de la integración.
        $limpiadosSinCodigo = AdmisionPostulante::query()
            ->where('id_proceso_admision', $request->id_proceso_admision)
            ->where('id_programa_admision', $request->id_programa_admision)
            ->where(function ($q) {
                $q->whereNull('codigo')
                    ->orWhere('codigo', '');
            })
            ->delete();

        DB::transaction(function () use (
            $items,
            $request,
            $programaLocal,
            &$insertados,
            &$actualizados,
            &$sinCodigo,
            &$omitidosSinDni
        ) {
            foreach ($items as $item) {
                $dni = trim((string) ($item['DNI'] ?? ''));
                $codigo = trim((string) ($item['codigo'] ?? ''));

                if ($dni === '') {
                    $omitidosSinDni++;
                    continue;
                }

                // Regla: mientras Admisión no asigne código después
                // del control biométrico, el registro NO se guarda.
                if ($codigo === '') {
                    $sinCodigo[] = [
                        'dni' => $dni,
                        'estudiante' => trim(
                            ($item['primer_apellido'] ?? '') . ' ' .
                            ($item['segundo_apellido'] ?? '') . ' ' .
                            ($item['nombres'] ?? '')
                        ),
                        'programa' => $item['programa'] ?? $programaLocal->programa,
                        'id_programa_admision' => $item['id_programa']
                            ?? (int) $request->id_programa_admision
                    ];

                    continue;
                }

                $registro = AdmisionPostulante::where([
                    'id_proceso_admision' => $request->id_proceso_admision,
                    'dni' => $dni
                ])->first();

                $datos = [
                    'id_programa_admision' => $item['id_programa']
                        ?? $request->id_programa_admision,
                    'id_programa_nivelacion' => $programaLocal->id,
                    'codigo' => $codigo,
                    'paterno' => $item['primer_apellido'] ?? null,
                    'materno' => $item['segundo_apellido'] ?? null,
                    'nombres' => $item['nombres'] ?? null,
                    'sexo' => $item['sexo'] ?? null,
                    'email' => $item['email'] ?? null,
                    'f_nacimiento' => $item['fec_nacimiento'] ?? null,
                    'ubigeo_nacimiento' => $item['ubigeo_nacimiento'] ?? null,
                    'estado_civil' => $item['estado_civil'] ?? null,
                    'anio_egreso' => $item['anio_egreso'] ?? null,
                    'tipo_colegio' => $item['gestion'] ?? null,
                    'nombre_colegio' => $item['nombre'] ?? null,
                    'ubigeo_colegio' => $item['c_ubigeo'] ?? null,
                    'direccion' => $item['direccion'] ?? null,
                    'telefono' => $item['celular'] ?? null,
                    'f_examen' => $item['f_examen'] ?? null,
                    'modalidad' => $item['modalidad'] ?? null,
                    'puntaje' => $item['puntaje'] ?? null,
                    'proceso_nombre' => $item['proceso'] ?? null,
                    'programa_nombre' => $item['programa'] ?? null,
                    'departamento' => $item['departamento'] ?? null,
                    'provincia' => $item['provincia'] ?? null,
                    'distrito' => $item['distrito'] ?? null,
                    'sincronizado_at' => now()
                ];

                if ($registro) {
                    $registro->fill($datos);
                    $registro->save();
                    $actualizados++;
                } else {
                    AdmisionPostulante::create(array_merge(
                        [
                            'id_proceso_admision' =>
                                $request->id_proceso_admision,
                            'dni' => $dni
                        ],
                        $datos
                    ));

                    $insertados++;
                }
            }
        });

        $conCodigo = $insertados + $actualizados;

        return response()->json([
            'estado' => true,
            'tipo' => 'success',
            'titulo' => 'POSTULANTES SINCRONIZADOS',
            'mensaje' => "{$conCodigo} registros con código fueron procesados. "
                . count($sinCodigo)
                . " registros sin código no fueron almacenados.",
            'total_api' => $totalApi,
            'con_codigo' => $conCodigo,
            'sin_codigo' => count($sinCodigo),
            'insertados' => $insertados,
            'actualizados' => $actualizados,
            'limpiados_sin_codigo' => $limpiadosSinCodigo,
            'omitidos_sin_dni' => $omitidosSinDni,
            'pendientes_sin_codigo' => $sinCodigo,
            'id_programa_admision' =>
                (int) $request->id_programa_admision,
            'programa' => $programaLocal->programa
        ]);
    }


    public function verificarPostulantes(Request $request, AdmisionApiService $api)
    {
        $request->validate([
            'id_proceso_admision' => 'required|integer',
            'id_programa_admision' => 'required|integer'
        ]);

        $programaLocal = DB::table('programa')
            ->where('id_admision', $request->id_programa_admision)
            ->select('id', 'programa')
            ->first();

        if (!$programaLocal) {
            return response()->json([
                'estado' => false,
                'tipo' => 'warn',
                'titulo' => 'PROGRAMA SIN EQUIVALENCIA',
                'mensaje' => 'El programa no tiene equivalencia local.'
            ]);
        }

        $response = $api->postulantes(
            (int) $request->id_proceso_admision,
            (int) $request->id_programa_admision
        );

        $items = $response['data'] ?? [];

        $existentes = AdmisionPostulante::query()
            ->where('id_proceso_admision', $request->id_proceso_admision)
            ->pluck('codigo', 'dni');

        $totalApi = count($items);
        $conCodigo = 0;
        $sinCodigo = 0;
        $nuevosConCodigo = 0;
        $yaSincronizados = 0;
        $codigoCambiado = 0;
        $pendientes = [];

        foreach ($items as $item) {
            $dni = trim((string) ($item['DNI'] ?? ''));
            $codigo = trim((string) ($item['codigo'] ?? ''));

            if ($dni === '') {
                continue;
            }

            if ($codigo === '') {
                $sinCodigo++;

                $pendientes[] = [
                    'dni' => $dni,
                    'estudiante' => trim(
                        ($item['primer_apellido'] ?? '') . ' ' .
                        ($item['segundo_apellido'] ?? '') . ' ' .
                        ($item['nombres'] ?? '')
                    ),
                    'programa' => $item['programa']
                        ?? $programaLocal->programa,
                    'id_programa_admision' =>
                        $item['id_programa']
                        ?? (int) $request->id_programa_admision
                ];

                continue;
            }

            $conCodigo++;

            if (!$existentes->has($dni)) {
                $nuevosConCodigo++;
                continue;
            }

            $codigoGuardado = trim((string) $existentes->get($dni));

            if ($codigoGuardado !== $codigo) {
                $codigoCambiado++;
            } else {
                $yaSincronizados++;
            }
        }

        return response()->json([
            'estado' => true,
            'programa' => $programaLocal->programa,
            'id_programa_admision' =>
                (int) $request->id_programa_admision,
            'total_api' => $totalApi,
            'con_codigo' => $conCodigo,
            'sin_codigo' => $sinCodigo,
            'nuevos_con_codigo' => $nuevosConCodigo,
            'ya_sincronizados' => $yaSincronizados,
            'codigo_cambiado' => $codigoCambiado,
            'pendientes_sin_codigo' => $pendientes
        ]);
    }

    public function importarMatriz(Request $request)
    {
        $request->validate([
            'id_periodo' => 'required|integer|exists:periodo,id_periodo',
            'archivo_origen' => 'nullable|string|max:255',
            'datos' => 'required|array|min:1',
            'datos.*.id_periodo' => 'required|integer',
            'datos.*.dni' => 'required|string|max:12',
            'datos.*.observacion' => 'nullable|string|max:255'
        ]);

        $idPeriodo = (int) $request->id_periodo;

        foreach ($request->datos as $item) {
            if ((int) ($item['id_periodo'] ?? 0) !== $idPeriodo) {
                return response()->json([
                    'estado' => false,
                    'tipo' => 'warn',
                    'titulo' => 'PERIODO DIFERENTE',
                    'mensaje' => 'El Excel contiene registros de un periodo diferente al seleccionado.'
                ], 200);
            }
        }

        $total = 0;

        DB::transaction(function () use ($request, $idPeriodo, &$total) {
            foreach ($request->datos as $item) {
                $dni = trim((string) ($item['dni'] ?? ''));

                $datos = [
                    'nivelar' => $item['nivelar'] ?? null,
                    'no_nivelar' => $item['no_nivelar'] ?? null,
                    'observacion' => isset($item['observacion']) ? trim((string) $item['observacion']) : null,
                    'archivo_origen' => $request->archivo_origen,
                    'importado_at' => now()
                ];

                for ($i = 1; $i <= 11; $i++) {
                    $datos["C{$i}"] = $item["C{$i}"] ?? null;
                    $datos["C{$i}_R"] = $item["C{$i}_R"] ?? null;
                }

                AdmisionMatriz::updateOrCreate(
                    [
                        'id_periodo' => $idPeriodo,
                        'dni' => $dni
                    ],
                    $datos
                );

                $total++;
            }
        });

        $periodoNombre = DB::table('periodo')
            ->where('id_periodo', $idPeriodo)
            ->value('nombre');

        return response()->json([
            'estado' => true,
            'tipo' => 'success',
            'titulo' => 'MATRIZ IMPORTADA',
            'mensaje' => "Se almacenaron/actualizaron {$total} registros en el periodo ID {$idPeriodo} - {$periodoNombre}."
        ]);
    }

    public function resumen(Request $request)
    {
        $request->validate([
            'id_periodo' => 'required|integer|exists:periodo,id_periodo'
        ]);

        $periodo = (int) $request->id_periodo;
        $base = $this->cruceBase($periodo);

        $estados = DB::query()
            ->fromSub($base, 'cruce')
            ->select('estado_cruce', DB::raw('COUNT(*) AS total'))
            ->groupBy('estado_cruce')
            ->pluck('total', 'estado_cruce');

        $totalApi = DB::query()
            ->fromSub($this->apiSeleccionPeriodo($periodo), 'api_periodo')
            ->count();

        $ultimaSincronizacion = DB::table('admision_postulante as ap')
            ->join(
                'admision_proceso as pr',
                'pr.id_admision',
                '=',
                'ap.id_proceso_admision'
            )
            ->where('pr.id_periodo', $periodo)
            ->max('ap.sincronizado_at');

        return response()->json([
            'estado' => true,
            'datos' => [
                'api' => $totalApi,
                'matriz' => AdmisionMatriz::where(
                    'id_periodo',
                    $periodo
                )->count(),
                'listos' => (int) ($estados['LISTO'] ?? 0),
                'solo_matriz' => (int) ($estados['SOLO MATRIZ'] ?? 0),
                'solo_api' => (int) ($estados['SOLO API'] ?? 0),
                'error_programa' =>
                    (int) ($estados['ERROR PROGRAMA'] ?? 0),
                'ya_registrado' =>
                    (int) ($estados['YA REGISTRADO'] ?? 0),
                'ultima_sincronizacion' => $ultimaSincronizacion
            ]
        ]);
    }

    public function cruce(Request $request)
    {
        $request->validate([
            'id_periodo' => 'required|integer|exists:periodo,id_periodo',
            'estado' => 'nullable|string|max:30',
            'term' => 'nullable|string|max:100'
        ]);

        $query = DB::query()->fromSub(
            $this->cruceBase((int) $request->id_periodo),
            'cruce'
        );

        if ($request->filled('estado')) {
            $query->where('estado_cruce', $request->estado);
        }

        if ($request->filled('term')) {
            $term = '%' . $request->term . '%';

            $query->where(function ($q) use ($term) {
                $q->where('dni', 'like', $term)
                    ->orWhere('codigo', 'like', $term)
                    ->orWhere('estudiante', 'like', $term)
                    ->orWhere('programa', 'like', $term);
            });
        }

        return response()->json([
            'estado' => true,
            'datos' => $query->orderBy('estudiante')->paginate(50)
        ]);
    }

    protected function apiSeleccionPeriodo(int $periodo)
    {
        return DB::table('admision_postulante as ap')
            ->join(
                'admision_proceso as pr',
                'pr.id_admision',
                '=',
                'ap.id_proceso_admision'
            )
            ->where('pr.id_periodo', $periodo)
            ->whereNotNull('ap.codigo')
            ->where('ap.codigo', '<>', '')
            ->select(
                'ap.dni',
                DB::raw('MAX(ap.id) AS api_id')
            )
            ->groupBy('ap.dni');
    }

    protected function cruceBase(int $periodo)
    {
        $apiDni = DB::query()
            ->fromSub($this->apiSeleccionPeriodo($periodo), 'api_sel')
            ->select('dni');

        $matrizDni = DB::table('admision_matriz')
            ->where('id_periodo', $periodo)
            ->select('dni');

        $dniUnion = $apiDni->union($matrizDni);

        $periodoNombre = DB::table('periodo')
            ->where('id_periodo', $periodo)
            ->value('nombre');

        // "Ya registrado" significa MISMO DNI en el MISMO periodo.
        // El mismo DNI en otro periodo es válido porque tendrá otro codigo_est.
        $registrados = DB::table('estudiante as e')
            ->join('datos_ingreso as di', 'di.codigo_est', '=', 'e.codigo_est')
            ->whereNotNull('e.dni')
            ->whereRaw('UPPER(TRIM(di.semestre)) = ?', [strtoupper(trim((string) $periodoNombre))])
            ->select('e.dni')
            ->distinct();

        return DB::query()
            ->fromSub($dniUnion, 'x')
            ->leftJoinSub($this->apiSeleccionPeriodo($periodo), 'api_sel', 'api_sel.dni', '=', 'x.dni')
            ->leftJoin('admision_postulante as a', 'a.id', '=', 'api_sel.api_id')
            ->leftJoin('admision_matriz as m', function ($join) use ($periodo) {
                $join->on('m.dni', '=', 'x.dni')
                    ->where('m.id_periodo', '=', $periodo);
            })
            ->leftJoin('programa as p', 'p.id', '=', 'a.id_programa_nivelacion')
            ->leftJoinSub($registrados, 'e', 'e.dni', '=', 'x.dni')
            ->select(
                'x.dni',
                'a.codigo',
                'a.id_proceso_admision',
                'a.proceso_nombre',
                DB::raw("TRIM(CONCAT_WS(' ', a.paterno, a.materno, a.nombres)) AS estudiante"),
                'a.programa_nombre as programa_admision',
                'p.programa',
                'a.id_programa_admision',
                'a.id_programa_nivelacion',
                'm.observacion as observacion_matriz',
                DB::raw("CASE
                    WHEN a.dni IS NULL THEN 'SOLO MATRIZ'
                    WHEN m.dni IS NULL THEN 'SOLO API'
                    WHEN a.id_programa_nivelacion IS NULL THEN 'ERROR PROGRAMA'
                    WHEN e.dni IS NOT NULL THEN 'YA REGISTRADO'
                    ELSE 'LISTO'
                END AS estado_cruce")
            );
    }

    public function reporte()
    {
        $periodos = DB::table('periodo')
            ->select('id_periodo', 'nombre', 'estado')
            ->orderByDesc('id_periodo')
            ->get();

        return Inertia::render('Superadmi/Admision/Reporte', [
            'periodos' => $periodos
        ]);
    }

    public function reporteData(Request $request)
    {
        $request->validate([
            'id_periodo' => 'required|integer|exists:periodo,id_periodo',
            'id_proceso_admision' => 'nullable|integer',
            'id_programa_admision' => 'nullable|integer',
            'estado' => 'nullable|string|max:30',
            'dni' => 'nullable|string|max:20',
            'codigo' => 'nullable|string|max:30',
            'nombre' => 'nullable|string|max:120',
            'observacion' => 'nullable|in:si,no',
            'per_page' => 'nullable|integer|min:10|max:200'
        ]);

        $query = DB::query()->fromSub(
            $this->cruceBase((int) $request->id_periodo),
            'cruce'
        );

        if ($request->filled('id_proceso_admision')) {
            $query->where(
                'id_proceso_admision',
                $request->id_proceso_admision
            );
        }

        if ($request->filled('id_programa_admision')) {
            $query->where(
                'id_programa_admision',
                $request->id_programa_admision
            );
        }

        if ($request->filled('estado')) {
            $query->where('estado_cruce', $request->estado);
        }

        if ($request->filled('dni')) {
            $query->where(
                'dni',
                'like',
                '%' . trim($request->dni) . '%'
            );
        }

        if ($request->filled('codigo')) {
            $query->where(
                'codigo',
                'like',
                '%' . trim($request->codigo) . '%'
            );
        }

        if ($request->filled('nombre')) {
            $nombre = '%' . trim($request->nombre) . '%';

            $query->where(function ($q) use ($nombre) {
                $q->where('estudiante', 'like', $nombre)
                    ->orWhere('programa', 'like', $nombre)
                    ->orWhere('programa_admision', 'like', $nombre)
                    ->orWhere('proceso_nombre', 'like', $nombre);
            });
        }

        if ($request->observacion === 'si') {
            $query->whereNotNull('observacion_matriz')
                ->where('observacion_matriz', '<>', '');
        }

        if ($request->observacion === 'no') {
            $query->where(function ($q) {
                $q->whereNull('observacion_matriz')
                    ->orWhere('observacion_matriz', '');
            });
        }

        $resumenQuery = clone $query;

        $resumen = DB::query()
            ->fromSub($resumenQuery, 'r')
            ->select(
                DB::raw('COUNT(*) AS total'),
                DB::raw("SUM(CASE WHEN estado_cruce = 'LISTO' THEN 1 ELSE 0 END) AS listos"),
                DB::raw("SUM(CASE WHEN estado_cruce = 'SOLO MATRIZ' THEN 1 ELSE 0 END) AS solo_matriz"),
                DB::raw("SUM(CASE WHEN estado_cruce = 'SOLO API' THEN 1 ELSE 0 END) AS solo_api"),
                DB::raw("SUM(CASE WHEN estado_cruce = 'ERROR PROGRAMA' THEN 1 ELSE 0 END) AS error_programa"),
                DB::raw("SUM(CASE WHEN estado_cruce = 'YA REGISTRADO' THEN 1 ELSE 0 END) AS ya_registrado")
            )
            ->first();

        $perPage = (int) ($request->per_page ?: 50);

        $datos = $query
            ->orderBy('estado_cruce')
            ->orderBy('programa')
            ->orderBy('estudiante')
            ->paginate($perPage);

        return response()->json([
            'estado' => true,
            'resumen' => [
                'total' => (int) ($resumen->total ?? 0),
                'listos' => (int) ($resumen->listos ?? 0),
                'solo_matriz' => (int) ($resumen->solo_matriz ?? 0),
                'solo_api' => (int) ($resumen->solo_api ?? 0),
                'error_programa' =>
                    (int) ($resumen->error_programa ?? 0),
                'ya_registrado' =>
                    (int) ($resumen->ya_registrado ?? 0)
            ],
            'datos' => $datos
        ]);
    }

}
