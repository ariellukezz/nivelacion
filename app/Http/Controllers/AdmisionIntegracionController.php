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

        $proceso = AdmisionProceso::where('id_admision', $request->id_proceso_admision)->first();
        $idPeriodo = (int) ($proceso->id_periodo ?? 0);

        $programaLocal = DB::table('programa')
            ->where('id_admision', $request->id_programa_admision)
            ->select('id', 'programa')
            ->first();

        $programaFallback = 'Programa Admisión ' . (int) $request->id_programa_admision;
        $response = $api->postulantes(
            (int) $request->id_proceso_admision,
            (int) $request->id_programa_admision
        );
        $items = $response['data'] ?? [];

        $dnis = collect($items)
            ->pluck('DNI')
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->values();

        $codigos = collect($items)
            ->pluck('codigo')
            ->map(fn ($v) => trim((string) $v))
            ->filter()
            ->unique()
            ->values();

        $existentesPeriodo = collect();
        if ($idPeriodo && $dnis->isNotEmpty()) {
            $existentesPeriodo = DB::table('admision_postulante as ap')
                ->join('admision_proceso as pr', 'pr.id_admision', '=', 'ap.id_proceso_admision')
                ->where('pr.id_periodo', $idPeriodo)
                ->whereIn('ap.dni', $dnis)
                ->select('ap.dni', 'ap.codigo', 'ap.id_proceso_admision')
                ->orderByDesc('ap.id')
                ->get()
                ->keyBy(fn ($item) => trim((string) $item->dni));
        }

        // En esta etapa SOLO se valida contra admision_postulante.
        $codigoAdmision = $codigos->isEmpty()
            ? collect()
            : AdmisionPostulante::query()
                ->whereIn('codigo', $codigos)
                ->select('codigo', 'dni', 'id_proceso_admision')
                ->get()
                ->keyBy(fn ($item) => trim((string) $item->codigo));

        $cont = [
            'con_codigo' => 0,
            'sin_codigo' => 0,
            'nuevos_con_codigo' => 0,
            'ya_sincronizados' => 0,
            'codigo_cambiado' => 0,
            'conflicto_codigo' => 0,
        ];

        $pendientes = [];
        $postulantesApi = [];

        foreach ($items as $item) {
            $dni = trim((string) ($item['DNI'] ?? ''));
            $codigo = trim((string) ($item['codigo'] ?? ''));
            if ($dni === '') {
                continue;
            }

            $estado = 'PENDIENTE_CODIGO';
            $codigoGuardado = null;

            if ($codigo === '') {
                $cont['sin_codigo']++;
                $pendientes[] = [
                    'dni' => $dni,
                    'estudiante' => trim(
                        ($item['primer_apellido'] ?? '') . ' ' .
                        ($item['segundo_apellido'] ?? '') . ' ' .
                        ($item['nombres'] ?? '')
                    ),
                    'programa' => $item['programa'] ?? ($programaLocal->programa ?? $programaFallback),
                ];
            } else {
                $cont['con_codigo']++;
                $existente = $existentesPeriodo->get($dni);

                if ($existente) {
                    $codigoGuardado = trim((string) $existente->codigo);
                    if ($codigoGuardado === $codigo) {
                        $estado = 'YA_SINCRONIZADO';
                        $cont['ya_sincronizados']++;
                    } else {
                        $estado = 'CODIGO_CAMBIADO';
                        $cont['codigo_cambiado']++;
                    }
                } else {
                    $owner = $codigoAdmision->get($codigo);

                    // El código es único dentro de admision_postulante.
                    // Si el registro fue borrado, no habrá owner y volverá a ser NUEVO.
                    if ($owner) {
                        $estado = 'CONFLICTO_CODIGO';
                        $cont['conflicto_codigo']++;
                    } else {
                        $estado = 'NUEVO_CON_CODIGO';
                        $cont['nuevos_con_codigo']++;
                    }
                }
            }

            $postulantesApi[] = [
                'dni' => $dni,
                'codigo' => $codigo !== '' ? $codigo : null,
                'codigo_guardado' => $codigoGuardado,
                'con_codigo' => $codigo !== '',
                'estado_sincronizacion' => $estado,
                'estudiante' => trim(
                    ($item['primer_apellido'] ?? '') . ' ' .
                    ($item['segundo_apellido'] ?? '') . ' ' .
                    ($item['nombres'] ?? '')
                ),
                'programa' => $item['programa'] ?? ($programaLocal->programa ?? $programaFallback),
                'id_programa_admision' => $item['id_programa'] ?? (int) $request->id_programa_admision,
                'id_proceso_admision' => (int) $request->id_proceso_admision,
            ];
        }

        return response()->json(array_merge([
            'estado' => true,
            'programa' => $programaLocal->programa ?? $programaFallback,
            'programa_vinculado' => (bool) $programaLocal,
            'id_programa_admision' => (int) $request->id_programa_admision,
            'total_api' => count($items),
            'pendientes_sin_codigo' => $pendientes,
            'postulantes_api' => $postulantesApi,
        ], $cont));
    }

    public function sincronizarNuevosCodigo(Request $request, AdmisionApiService $api)
    {
        $request->validate([
            'id_periodo' => 'required|integer|exists:periodo,id_periodo',
            'id_programa_admision' => 'nullable|integer'
        ]);

        $idPeriodo = (int) $request->id_periodo;
        $idProgramaFiltro = $request->filled('id_programa_admision')
            ? (int) $request->id_programa_admision
            : null;

        $procesos = AdmisionProceso::where('id_periodo', $idPeriodo)->get();
        if ($procesos->isEmpty()) {
            return response()->json([
                'estado' => false,
                'tipo' => 'warn',
                'titulo' => 'SIN PROCESOS',
                'mensaje' => 'No hay procesos de Admisión vinculados al período seleccionado.'
            ], 422);
        }

        $programasApi = collect($api->programas()['data'] ?? []);
        if ($idProgramaFiltro) {
            $programasApi = $programasApi
                ->filter(fn ($item) => (int) ($item['id'] ?? 0) === $idProgramaFiltro)
                ->values();
        }

        $programasLocal = DB::table('programa')
            ->whereNotNull('id_admision')
            ->select('id', 'programa', 'id_admision')
            ->get()
            ->keyBy('id_admision');

        $apiPorDni = [];
        $erroresApi = [];

        foreach ($procesos as $proceso) {
            foreach ($programasApi as $programaApi) {
                $idProgApi = (int) ($programaApi['id'] ?? 0);
                if (!$idProgApi) {
                    continue;
                }

                try {
                    $items = $api->postulantes((int) $proceso->id_admision, $idProgApi)['data'] ?? [];

                    foreach ($items as $item) {
                        $dni = trim((string) ($item['DNI'] ?? ''));
                        if ($dni === '') {
                            continue;
                        }

                        $codigo = trim((string) ($item['codigo'] ?? ''));
                        $nuevo = [
                            'item' => $item,
                            'dni' => $dni,
                            'codigo' => $codigo,
                            'id_proceso_admision' => (int) $proceso->id_admision,
                            'id_programa_admision' => (int) ($item['id_programa'] ?? $idProgApi),
                        ];

                        if (!isset($apiPorDni[$dni]) || ($apiPorDni[$dni]['codigo'] === '' && $codigo !== '')) {
                            $apiPorDni[$dni] = $nuevo;
                        } elseif (
                            $codigo !== '' &&
                            $apiPorDni[$dni]['codigo'] !== '' &&
                            $apiPorDni[$dni]['codigo'] !== $codigo
                        ) {
                            $apiPorDni[$dni]['conflicto_api'] = true;
                        }
                    }
                } catch (\Throwable $e) {
                    $erroresApi[] = $proceso->nombre . ' / ' .
                        ($programaApi['nombre'] ?? $idProgApi) . ': ' . $e->getMessage();
                }
            }
        }

        $res = [
            'insertados' => 0,
            'ya_sincronizados' => 0,
            'sin_codigo' => 0,
            'codigo_cambiado' => 0,
            'conflicto_codigo' => 0,
            'conflicto_api' => 0,
            'error_programa' => 0,
        ];

        DB::transaction(function () use (&$res, $apiPorDni, $idPeriodo, $programasLocal) {
            foreach ($apiPorDni as $r) {
                $dni = $r['dni'];
                $codigo = $r['codigo'];
                $item = $r['item'];

                if (!empty($r['conflicto_api'])) {
                    $res['conflicto_api']++;
                    continue;
                }

                if ($codigo === '') {
                    $res['sin_codigo']++;
                    continue;
                }

                $local = $programasLocal->get($r['id_programa_admision']);
                if (!$local) {
                    $res['error_programa']++;
                    continue;
                }

                $existentePeriodo = DB::table('admision_postulante as ap')
                    ->join('admision_proceso as pr', 'pr.id_admision', '=', 'ap.id_proceso_admision')
                    ->where('pr.id_periodo', $idPeriodo)
                    ->where('ap.dni', $dni)
                    ->select('ap.id', 'ap.codigo')
                    ->first();

                if ($existentePeriodo) {
                    if (trim((string) $existentePeriodo->codigo) === $codigo) {
                        $res['ya_sincronizados']++;
                    } else {
                        $res['codigo_cambiado']++;
                    }
                    continue;
                }

                // SOLO admision_postulante participa en la validación de código.
                // El código es único: si ya existe en esta tabla, no se duplica.
                $ownerCodigo = AdmisionPostulante::query()
                    ->where('codigo', $codigo)
                    ->select('id', 'dni', 'codigo')
                    ->first();

                if ($ownerCodigo) {
                    $res['conflicto_codigo']++;
                    continue;
                }

                // Si no existe el DNI en el período y el código tampoco existe,
                // el registro es recuperable/nuevo y se inserta nuevamente.
                AdmisionPostulante::create([
                    'id_proceso_admision' => $r['id_proceso_admision'],
                    'id_programa_admision' => $r['id_programa_admision'],
                    'id_programa_nivelacion' => $local->id,
                    'codigo' => $codigo,
                    'dni' => $dni,
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
                    'programa_nombre' => $item['programa'] ?? $local->programa,
                    'departamento' => $item['departamento'] ?? null,
                    'provincia' => $item['provincia'] ?? null,
                    'distrito' => $item['distrito'] ?? null,
                    'sincronizado_at' => now(),
                ]);

                $res['insertados']++;
            }
        });

        return response()->json([
            'estado' => true,
            'tipo' => 'success',
            'titulo' => 'SINCRONIZACIÓN INCREMENTAL',
            'mensaje' => "Se insertaron {$res['insertados']} postulantes nuevos con código. No se duplicaron registros ya sincronizados.",
            'datos' => $res,
            'errores_api' => $erroresApi,
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

    /**
     * Devuelve los DNI únicos de la matriz del período seleccionado.
     * Se usa para comparar en el navegador la matriz contra la verificación
     * EN VIVO de la API. No modifica ningún registro.
     */
    public function matrizPeriodo(Request $request)
    {
        $request->validate([
            'id_periodo' => 'required|integer|exists:periodo,id_periodo'
        ]);

        $idPeriodo = (int) $request->id_periodo;

        $datos = AdmisionMatriz::query()
            ->where('id_periodo', $idPeriodo)
            ->whereNotNull('dni')
            ->where('dni', '<>', '')
            ->select('dni', 'observacion')
            ->orderBy('dni')
            ->get()
            ->map(function ($item) {
                return [
                    'dni' => trim((string) $item->dni),
                    'observacion' => $item->observacion
                ];
            })
            ->unique('dni')
            ->values();

        return response()->json([
            'estado' => true,
            'total' => $datos->count(),
            'datos' => $datos
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
            ->join('admision_proceso as pr', 'pr.id_admision', '=', 'ap.id_proceso_admision')
            ->where('pr.id_periodo', $periodo)
            ->max('ap.sincronizado_at');

        return response()->json([
            'estado' => true,
            'datos' => [
                'api' => $totalApi,
                'matriz' => AdmisionMatriz::where('id_periodo', $periodo)->count(),
                'completo' => (int) ($estados['COMPLETO'] ?? 0),
                'solo_matriz' => (int) ($estados['SOLO MATRIZ'] ?? 0),
                'solo_api' => (int) ($estados['SOLO API'] ?? 0),
                'error_programa' => (int) ($estados['ERROR PROGRAMA'] ?? 0),
                'ultima_sincronizacion' => $ultimaSincronizacion,
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

        return DB::query()
            ->fromSub($dniUnion, 'x')
            ->leftJoinSub($this->apiSeleccionPeriodo($periodo), 'api_sel', 'api_sel.dni', '=', 'x.dni')
            ->leftJoin('admision_postulante as a', 'a.id', '=', 'api_sel.api_id')
            ->leftJoin('admision_matriz as m', function ($join) use ($periodo) {
                $join->on('m.dni', '=', 'x.dni')
                    ->where('m.id_periodo', '=', $periodo);
            })
            ->leftJoin('programa as p', 'p.id', '=', 'a.id_programa_nivelacion')
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
                    ELSE 'COMPLETO'
                END AS estado_cruce")
            );
    }


    /**
     * Cobertura EN VIVO del periodo seleccionado.
     *
     * - Consulta todos los procesos de Admision vinculados al periodo.
     * - Consulta todos los programas de la API, incluso si aun no tienen equivalencia local.
     * - No inserta ni modifica postulantes.
     * - Los postulantes sin codigo se muestran como pendientes, pero NO se guardan.
     * - La comparacion API <-> Matriz se realiza por DNI unico dentro del periodo.
     */
    public function reporteCobertura(Request $request, AdmisionApiService $api)
    {
        $request->validate([
            'id_periodo' => 'required|integer|exists:periodo,id_periodo',
            'id_programa_admision' => 'nullable|integer'
        ]);

        $idPeriodo = (int) $request->id_periodo;
        $idProgramaFiltro = $request->filled('id_programa_admision')
            ? (int) $request->id_programa_admision
            : null;

        $procesos = AdmisionProceso::query()
            ->where('id_periodo', $idPeriodo)
            ->select('id_admision', 'nombre')
            ->orderBy('id_admision')
            ->get();

        $matriz = AdmisionMatriz::query()
            ->where('id_periodo', $idPeriodo)
            ->whereNotNull('dni')
            ->where('dni', '<>', '')
            ->select('dni', 'observacion')
            ->get()
            ->map(fn ($item) => [
                'dni' => trim((string) $item->dni),
                'observacion' => $item->observacion,
            ])
            ->filter(fn ($item) => $item['dni'] !== '')
            ->unique('dni')
            ->keyBy('dni');

        $programasApi = collect($api->programas()['data'] ?? []);
        if ($idProgramaFiltro) {
            $programasApi = $programasApi
                ->filter(fn ($item) => (int) ($item['id'] ?? 0) === $idProgramaFiltro)
                ->values();
        }

        $programasLocal = DB::table('programa')
            ->whereNotNull('id_admision')
            ->select('id', 'programa', 'id_admision')
            ->get()
            ->keyBy('id_admision');

        $apiPorDni = [];
        $erroresApi = [];

        foreach ($procesos as $proceso) {
            foreach ($programasApi as $programaApi) {
                $idProgramaApi = (int) ($programaApi['id'] ?? 0);
                if (!$idProgramaApi) {
                    continue;
                }

                try {
                    $response = $api->postulantes((int) $proceso->id_admision, $idProgramaApi);

                    foreach (($response['data'] ?? []) as $item) {
                        $dni = trim((string) ($item['DNI'] ?? ''));
                        if ($dni === '') {
                            continue;
                        }

                        $codigo = trim((string) ($item['codigo'] ?? ''));
                        $idProgramaItem = (int) ($item['id_programa'] ?? $idProgramaApi);

                        $registro = [
                            'dni' => $dni,
                            'codigo' => $codigo !== '' ? $codigo : null,
                            'estudiante' => trim(
                                ($item['primer_apellido'] ?? '') . ' ' .
                                ($item['segundo_apellido'] ?? '') . ' ' .
                                ($item['nombres'] ?? '')
                            ),
                            'programa' => $item['programa'] ?? ($programaApi['nombre'] ?? ('Programa ' . $idProgramaItem)),
                            'id_programa_admision' => $idProgramaItem,
                            'id_proceso_admision' => (int) $proceso->id_admision,
                            'proceso_nombre' => $item['proceso'] ?? $proceso->nombre,
                            'conflicto_api' => false,
                            'programas_api_periodo' => [$idProgramaItem],
                        ];

                        if (!isset($apiPorDni[$dni])) {
                            $apiPorDni[$dni] = $registro;
                            continue;
                        }

                        $actual = $apiPorDni[$dni];
                        $codActual = trim((string) ($actual['codigo'] ?? ''));

                        if (!in_array($idProgramaItem, $actual['programas_api_periodo'], true)) {
                            $actual['programas_api_periodo'][] = $idProgramaItem;
                        }

                        if ($codActual === '' && $codigo !== '') {
                            $registro['programas_api_periodo'] = $actual['programas_api_periodo'];
                            $apiPorDni[$dni] = $registro;
                            continue;
                        }

                        if ($codActual !== '' && $codigo !== '' && $codActual !== $codigo) {
                            $actual['conflicto_api'] = true;
                        }

                        if (count($actual['programas_api_periodo']) > 1) {
                            $actual['conflicto_api'] = true;
                        }

                        $apiPorDni[$dni] = $actual;
                    }
                } catch (\Throwable $e) {
                    $erroresApi[] = [
                        'proceso' => $proceso->nombre,
                        'programa' => $programaApi['nombre'] ?? $idProgramaApi,
                        'mensaje' => $e->getMessage(),
                    ];
                }
            }
        }

        $apiActual = collect($apiPorDni)->keyBy('dni');
        $dnisApi = $apiActual->keys();
        $dnisMatriz = $matriz->keys();

        $dnisConCodigo = $apiActual->filter(fn ($item) => !empty($item['codigo']))->keys();
        $dnisSinCodigo = $apiActual->filter(fn ($item) => empty($item['codigo']))->keys();
        $dnisCoinciden = $dnisApi->intersect($dnisMatriz)->values();
        $dnisCoincidenConCodigo = $dnisCoinciden->intersect($dnisConCodigo)->values();
        $dnisCoincidenSinCodigo = $dnisCoinciden->intersect($dnisSinCodigo)->values();
        $dnisSoloMatriz = $dnisMatriz->diff($dnisApi)->values();
        $dnisSoloApi = $dnisApi->diff($dnisMatriz)->values();

        $existentesPeriodo = DB::table('admision_postulante as ap')
            ->join('admision_proceso as pr', 'pr.id_admision', '=', 'ap.id_proceso_admision')
            ->where('pr.id_periodo', $idPeriodo)
            ->select('ap.dni', 'ap.codigo', 'ap.id_programa_admision', 'ap.id_proceso_admision')
            ->orderByDesc('ap.id')
            ->get()
            ->keyBy(fn ($item) => trim((string) $item->dni));

        $codigosApi = $apiActual->pluck('codigo')->filter()->unique()->values();
        $codigoAdmision = $codigosApi->isEmpty()
            ? collect()
            : AdmisionPostulante::query()
                ->whereIn('codigo', $codigosApi)
                ->select('codigo', 'dni', 'id_proceso_admision')
                ->get()
                ->keyBy(fn ($item) => trim((string) $item->codigo));

        $listas = [
            'matriz_total' => [],
            'api_total' => [],
            'api_con_codigo' => [],
            'api_sin_codigo' => [],
            'coinciden' => [],
            'coinciden_con_codigo' => [],
            'coinciden_sin_codigo' => [],
            'no_coinciden' => [],
            'solo_matriz' => [],
            'solo_api' => [],
            'nuevos_con_codigo' => [],
            'ya_sincronizados' => [],
            'codigo_cambiado' => [],
            'conflicto_codigo' => [],
            'conflicto_api' => [],
            'error_programa' => [],
        ];

        foreach ($matriz as $dni => $item) {
            $listas['matriz_total'][] = [
                'dni' => $dni,
                'codigo' => null,
                'estudiante' => null,
                'programa' => null,
                'proceso_nombre' => null,
                'observacion' => $item['observacion'],
                'situacion' => 'MATRIZ',
            ];
        }

        $registros = collect();

        foreach ($apiActual as $dni => $item) {
            $fila = [
                'dni' => $dni,
                'codigo' => $item['codigo'],
                'codigo_guardado' => null,
                'estudiante' => $item['estudiante'],
                'programa' => $item['programa'],
                'id_programa_admision' => $item['id_programa_admision'],
                'id_proceso_admision' => $item['id_proceso_admision'],
                'proceso_nombre' => $item['proceso_nombre'],
                'observacion' => $matriz->get($dni)['observacion'] ?? null,
                'situacion' => null,
            ];

            $listas['api_total'][] = $fila;
            if (!empty($item['codigo'])) {
                $listas['api_con_codigo'][] = $fila;
            } else {
                $tmp = $fila;
                $tmp['situacion'] = 'API SIN CÓDIGO';
                $listas['api_sin_codigo'][] = $tmp;
            }

            if ($dnisCoinciden->contains($dni)) {
                $tmp = $fila;
                $tmp['situacion'] = !empty($item['codigo']) ? 'COINCIDE CON CÓDIGO' : 'COINCIDE SIN CÓDIGO';
                $listas['coinciden'][] = $tmp;
                if (!empty($item['codigo'])) {
                    $listas['coinciden_con_codigo'][] = $tmp;
                } else {
                    $listas['coinciden_sin_codigo'][] = $tmp;
                }
            }

            if ($dnisSoloApi->contains($dni)) {
                $tmp = $fila;
                $tmp['situacion'] = 'SOLO API';
                $listas['solo_api'][] = $tmp;
                $listas['no_coinciden'][] = $tmp;
            }

            $incidencia = null;
            $sincronizado = false;
            $existente = $existentesPeriodo->get($dni);

            if (empty($item['codigo'])) {
                // Aún no puede sincronizarse.
            } elseif (!empty($item['conflicto_api'])) {
                $incidencia = 'CONFLICTO API MISMO PERÍODO';
                $tmp = $fila;
                $tmp['situacion'] = $incidencia;
                $listas['conflicto_api'][] = $tmp;
            } elseif (!$programasLocal->get($item['id_programa_admision'])) {
                $incidencia = 'PROGRAMA SIN EQUIVALENCIA';
                $tmp = $fila;
                $tmp['situacion'] = $incidencia;
                $listas['error_programa'][] = $tmp;
            } elseif ($existente) {
                $fila['codigo_guardado'] = $existente->codigo;
                if (trim((string) $existente->codigo) === trim((string) $item['codigo'])) {
                    $sincronizado = true;
                    $tmp = $fila;
                    $tmp['situacion'] = 'YA SINCRONIZADO';
                    $listas['ya_sincronizados'][] = $tmp;
                } else {
                    $incidencia = 'CÓDIGO CAMBIADO';
                    $tmp = $fila;
                    $tmp['situacion'] = $incidencia;
                    $listas['codigo_cambiado'][] = $tmp;
                }
            } else {
                $codigo = trim((string) $item['codigo']);
                $owner = $codigoAdmision->get($codigo);

                if ($owner) {
                    $incidencia = 'CONFLICTO DE CÓDIGO';
                    $tmp = $fila;
                    $tmp['situacion'] = $incidencia;
                    $listas['conflicto_codigo'][] = $tmp;
                } else {
                    $tmp = $fila;
                    $tmp['situacion'] = 'NUEVO CON CÓDIGO';
                    $listas['nuevos_con_codigo'][] = $tmp;
                }
            }

            $tieneMatriz = $matriz->has($dni);
            $estadoPrincipal = $incidencia
                ? 'INCIDENCIA'
                : ($tieneMatriz ? 'COMPLETO' : 'SOLO_API');

            $registros->push([
                'dni' => $dni,
                'codigo' => $item['codigo'],
                'estudiante' => $item['estudiante'],
                'programa' => $item['programa'],
                'id_programa_admision' => $item['id_programa_admision'],
                'proceso_nombre' => $item['proceso_nombre'],
                'matriz' => $tieneMatriz,
                'api' => true,
                'con_codigo' => !empty($item['codigo']),
                'sincronizado' => $sincronizado,
                'estado_principal' => $estadoPrincipal,
                'incidencia' => $incidencia,
                'observacion' => $matriz->get($dni)['observacion'] ?? null,
            ]);
        }

        foreach ($dnisSoloMatriz as $dni) {
            $item = $matriz->get($dni);
            $fila = [
                'dni' => $dni,
                'codigo' => null,
                'estudiante' => null,
                'programa' => null,
                'proceso_nombre' => null,
                'observacion' => $item['observacion'] ?? null,
                'situacion' => 'SOLO MATRIZ',
            ];
            $listas['solo_matriz'][] = $fila;
            $listas['no_coinciden'][] = $fila;

            $registros->push([
                'dni' => $dni,
                'codigo' => null,
                'estudiante' => null,
                'programa' => null,
                'id_programa_admision' => null,
                'proceso_nombre' => null,
                'matriz' => true,
                'api' => false,
                'con_codigo' => false,
                'sincronizado' => false,
                'estado_principal' => 'SOLO_MATRIZ',
                'incidencia' => null,
                'observacion' => $item['observacion'] ?? null,
            ]);
        }

        foreach ($listas as $clave => $items) {
            $listas[$clave] = collect($items)
                ->sortBy(fn ($item) => ($item['estudiante'] ?? '') . ($item['dni'] ?? ''))
                ->values();
        }

        $registros = $registros
            ->sortBy(fn ($item) => ($item['programa'] ?? 'ZZZ') . '|' . ($item['estudiante'] ?? '') . '|' . $item['dni'])
            ->values();

        $programasReporte = $registros
            ->filter(fn ($item) => !empty($item['id_programa_admision']) && !empty($item['programa']))
            ->map(fn ($item) => [
                'id' => (int) $item['id_programa_admision'],
                'label' => $item['programa'],
            ])
            ->unique('id')
            ->sortBy('label')
            ->values();

        return response()->json([
            'estado' => true,
            'id_periodo' => $idPeriodo,
            'periodo' => DB::table('periodo')->where('id_periodo', $idPeriodo)->value('nombre'),
            'resumen' => [
                'matriz_total' => $matriz->count(),
                'api_total' => $apiActual->count(),
                'api_con_codigo' => $dnisConCodigo->count(),
                'api_sin_codigo' => $dnisSinCodigo->count(),
                'coinciden' => $dnisCoinciden->count(),
                'coinciden_con_codigo' => $dnisCoincidenConCodigo->count(),
                'coinciden_sin_codigo' => $dnisCoincidenSinCodigo->count(),
                'no_coinciden' => $dnisSoloMatriz->count() + $dnisSoloApi->count(),
                'solo_matriz' => $dnisSoloMatriz->count(),
                'solo_api' => $dnisSoloApi->count(),
                'nuevos_con_codigo' => count($listas['nuevos_con_codigo']),
                'ya_sincronizados' => count($listas['ya_sincronizados']),
                'codigo_cambiado' => count($listas['codigo_cambiado']),
                'conflicto_codigo' => count($listas['conflicto_codigo']),
                'conflicto_api' => count($listas['conflicto_api']),
                'error_programa' => count($listas['error_programa']),
            ],
            'listas' => $listas,
            'registros' => $registros,
            'programas_reporte' => $programasReporte,
            'filtro_programa' => $idProgramaFiltro,
            'errores_api' => $erroresApi,
        ]);
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
            'id_periodo' => 'required|integer|exists:periodo,id_periodo'
        ]);

        $idPeriodo = (int) $request->id_periodo;

        $matriz = AdmisionMatriz::query()
            ->where('id_periodo', $idPeriodo)
            ->whereNotNull('dni')
            ->where('dni', '<>', '')
            ->select('dni', 'observacion', 'importado_at')
            ->get()
            ->map(fn ($item) => [
                'dni' => trim((string) $item->dni),
                'observacion' => $item->observacion,
                'importado_at' => $item->importado_at,
            ])
            ->filter(fn ($item) => $item['dni'] !== '')
            ->unique('dni')
            ->keyBy('dni');

        $apiRows = DB::table('admision_postulante as ap')
            ->join('admision_proceso as pr', 'pr.id_admision', '=', 'ap.id_proceso_admision')
            ->leftJoin('programa as p', 'p.id', '=', 'ap.id_programa_nivelacion')
            ->where('pr.id_periodo', $idPeriodo)
            ->whereNotNull('ap.dni')
            ->where('ap.dni', '<>', '')
            ->select(
                'ap.id',
                'ap.dni',
                'ap.codigo',
                'ap.paterno',
                'ap.materno',
                'ap.nombres',
                'ap.id_programa_admision',
                'ap.id_programa_nivelacion',
                'ap.programa_nombre',
                'ap.proceso_nombre',
                'ap.sincronizado_at',
                'p.programa as programa_nivelacion'
            )
            ->orderByDesc('ap.id')
            ->get();

        $apiAgrupado = $apiRows->groupBy(fn ($item) => trim((string) $item->dni));
        $apiPorDni = collect();
        $incidencias = collect();

        foreach ($apiAgrupado as $dni => $grupo) {
            if ($dni === '') {
                continue;
            }

            $principal = $grupo->first();
            $codigos = $grupo->pluck('codigo')
                ->map(fn ($v) => trim((string) $v))
                ->filter()
                ->unique()
                ->values();

            $programas = $grupo->pluck('id_programa_admision')
                ->filter(fn ($v) => $v !== null && $v !== '')
                ->map(fn ($v) => (int) $v)
                ->unique()
                ->values();

            $incidencia = null;
            if ($codigos->count() > 1) {
                $incidencia = 'MÁS DE UN CÓDIGO EN EL MISMO PERÍODO';
            } elseif ($programas->count() > 1) {
                $incidencia = 'MÁS DE UN PROGRAMA EN EL MISMO PERÍODO';
            } elseif (empty($principal->id_programa_nivelacion)) {
                $incidencia = 'PROGRAMA SIN EQUIVALENCIA';
            }

            $apiPorDni->put($dni, $principal);
            if ($incidencia) {
                $incidencias->put($dni, $incidencia);
            }
        }

        $dnis = $matriz->keys()->merge($apiPorDni->keys())->unique()->values();
        $registros = collect();

        foreach ($dnis as $dni) {
            $matrizItem = $matriz->get($dni);
            $apiItem = $apiPorDni->get($dni);

            $tieneMatriz = (bool) $matrizItem;
            $tieneApi = (bool) $apiItem;
            $codigo = $apiItem ? trim((string) ($apiItem->codigo ?? '')) : '';
            $tieneCodigo = $codigo !== '';
            $sincronizado = $tieneApi && $tieneCodigo;
            $incidencia = $incidencias->get($dni);

            if ($incidencia) {
                $estadoPrincipal = 'INCIDENCIA';
            } elseif ($tieneMatriz && $tieneApi) {
                $estadoPrincipal = 'COMPLETO';
            } elseif ($tieneMatriz) {
                $estadoPrincipal = 'SOLO_MATRIZ';
            } else {
                $estadoPrincipal = 'SOLO_API';
            }

            $registros->push([
                'dni' => $dni,
                'codigo' => $codigo !== '' ? $codigo : null,
                'estudiante' => $apiItem
                    ? trim(implode(' ', array_filter([
                        $apiItem->paterno ?? null,
                        $apiItem->materno ?? null,
                        $apiItem->nombres ?? null,
                    ])))
                    : null,
                'programa' => $apiItem
                    ? ($apiItem->programa_nivelacion ?: $apiItem->programa_nombre)
                    : null,
                'id_programa_admision' => $apiItem
                    ? ($apiItem->id_programa_admision !== null
                        ? (int) $apiItem->id_programa_admision
                        : null)
                    : null,
                'proceso_nombre' => $apiItem->proceso_nombre ?? null,
                'matriz' => $tieneMatriz,
                'api' => $tieneApi,
                'con_codigo' => $tieneCodigo,
                'sincronizado' => $sincronizado,
                'estado_principal' => $estadoPrincipal,
                'incidencia' => $incidencia,
                'observacion' => $matrizItem['observacion'] ?? null,
                'sincronizado_at' => $apiItem->sincronizado_at ?? null,
                'importado_at' => $matrizItem['importado_at'] ?? null,
            ]);
        }

        $registros = $registros
            ->sortBy(fn ($item) =>
                ($item['programa'] ?? 'ZZZ') . '|' .
                ($item['estudiante'] ?? '') . '|' .
                $item['dni']
            )
            ->values();

        $resumen = [
            'total' => $registros->count(),
            'matriz_total' => $matriz->count(),
            'api_sincronizados' => $apiPorDni->count(),
            'completo' => $registros->where('estado_principal', 'COMPLETO')->count(),
            'solo_matriz' => $registros->where('estado_principal', 'SOLO_MATRIZ')->count(),
            'solo_api' => $registros->where('estado_principal', 'SOLO_API')->count(),
            'incidencias' => $registros->where('estado_principal', 'INCIDENCIA')->count(),
            'con_codigo' => $registros->where('con_codigo', true)->count(),
            'sincronizados' => $registros->where('sincronizado', true)->count(),
            'ultima_sincronizacion' => $apiRows->max('sincronizado_at'),
            'ultima_matriz' => $matriz->pluck('importado_at')->filter()->max(),
        ];

        $programas = $registros
            ->filter(fn ($item) => !empty($item['id_programa_admision']) && !empty($item['programa']))
            ->map(fn ($item) => [
                'value' => (int) $item['id_programa_admision'],
                'label' => $item['programa'],
            ])
            ->unique('value')
            ->sortBy('label')
            ->values();

        return response()->json([
            'estado' => true,
            'resumen' => $resumen,
            'registros' => $registros,
            'programas' => $programas,
        ]);
    }

}
