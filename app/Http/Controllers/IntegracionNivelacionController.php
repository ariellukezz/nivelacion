<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use Throwable;

class IntegracionNivelacionController extends Controller
{
    private function asegurarSuperadmin(): void
    {
        if ((int) (auth()->user()->rol ?? -1) !== 0) {
            abort(403, 'Solo Super Admin puede ejecutar la integración a Nivelación.');
        }
    }

    public function index()
    {
        $this->asegurarSuperadmin();

        $periodos = DB::table('periodo')
            ->select('id_periodo', 'nombre', 'estado', 'fecha_inicio', 'fecha_fin')
            ->orderByDesc('id_periodo')
            ->get();

        $escuelas = DB::table('escuela')
            ->whereNotIn('id', [97, 98])
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get();

        $programas = DB::table('programa as p')
            ->leftJoin('escuela as e', 'e.id', '=', 'p.id_escuela')
            ->whereNotNull('p.id_escuela')
            ->select(
                'p.id',
                'p.programa',
                'p.id_escuela',
                'e.nombre as escuela'
            )
            ->orderBy('e.nombre')
            ->orderBy('p.programa')
            ->get();

        return Inertia::render('Superadmi/IntegracionNivelacion/Index', [
            'periodos' => $periodos,
            'escuelas' => $escuelas,
            'programas' => $programas,
        ]);
    }

    private function validarScope(Request $request): array
    {
        $this->asegurarSuperadmin();

        $data = $request->validate([
            'id_periodo' => ['required', 'integer', 'exists:periodo,id_periodo'],
            'ambito' => ['nullable', 'in:todas,escuela,programa'],
            'id_escuela' => ['nullable', 'integer', 'exists:escuela,id'],
            'id_programa' => ['nullable', 'integer', 'exists:programa,id'],
        ]);

        $data['ambito'] = $data['ambito'] ?? 'todas';

        if ($data['ambito'] === 'escuela' && empty($data['id_escuela'])) {
            abort(422, 'Debe seleccionar una escuela.');
        }

        if ($data['ambito'] === 'programa') {
            if (empty($data['id_programa'])) {
                abort(422, 'Debe seleccionar un programa.');
            }

            $programa = DB::table('programa')
                ->where('id', (int) $data['id_programa'])
                ->select('id', 'id_escuela')
                ->first();

            if (!$programa) {
                abort(422, 'El programa seleccionado no existe.');
            }

            $data['id_escuela'] = (int) $programa->id_escuela;
        }

        return $data;
    }

    /**
     * Selecciona un solo registro API por DNI dentro del período.
     * Si el DNI apareció en más de un proceso, conserva el último id y además
     * entrega indicadores para detectar códigos/programas distintos.
     */
    private function apiSeleccionPeriodo(int $idPeriodo)
    {
        return DB::table('admision_postulante as ap')
            ->join('admision_proceso as pr', 'pr.id_admision', '=', 'ap.id_proceso_admision')
            ->where('pr.id_periodo', $idPeriodo)
            ->whereNotNull('ap.codigo')
            ->where('ap.codigo', '<>', '')
            ->select(
                'ap.dni',
                DB::raw('MAX(ap.id) AS api_id'),
                DB::raw('COUNT(*) AS registros_api'),
                DB::raw('COUNT(DISTINCT ap.codigo) AS codigos_distintos'),
                DB::raw('COUNT(DISTINCT COALESCE(ap.id_programa_nivelacion, 0)) AS programas_distintos')
            )
            ->groupBy('ap.dni');
    }

    /** Registros completos de Admisión aptos para ser evaluados para Nivelación. */
    private function fuenteCompletaQuery(int $idPeriodo)
    {
        return DB::query()
            ->fromSub($this->apiSeleccionPeriodo($idPeriodo), 'sel')
            ->join('admision_postulante as ap', 'ap.id', '=', 'sel.api_id')
            ->join('admision_proceso as pr', 'pr.id_admision', '=', 'ap.id_proceso_admision')
            ->join('admision_matriz as am', function ($join) use ($idPeriodo) {
                $join->on('am.dni', '=', 'ap.dni')
                    ->where('am.id_periodo', '=', $idPeriodo);
            })
            ->join('programa as p', 'p.id', '=', 'ap.id_programa_nivelacion')
            ->leftJoin('escuela as e', 'e.id', '=', 'p.id_escuela')
            ->where('pr.id_periodo', $idPeriodo)
            ->whereNotNull('ap.id_programa_nivelacion')
            ->select(
                'ap.id as id_admision_postulante',
                'ap.dni',
                'ap.codigo',
                'ap.paterno',
                'ap.materno',
                'ap.nombres',
                'ap.sexo',
                'ap.email',
                'ap.f_nacimiento',
                'ap.ubigeo_nacimiento',
                'ap.estado_civil',
                'ap.anio_egreso',
                'ap.tipo_colegio',
                'ap.nombre_colegio',
                'ap.ubigeo_colegio',
                'ap.direccion',
                'ap.telefono',
                'ap.f_examen',
                'ap.modalidad',
                'ap.puntaje',
                'ap.proceso_nombre',
                'ap.programa_nombre as programa_admision',
                'ap.id_programa_nivelacion as id_programa',
                'p.programa',
                'p.id_escuela',
                'e.nombre as escuela',
                'pr.anio as anio_proceso',
                'sel.registros_api',
                'sel.codigos_distintos',
                'sel.programas_distintos',
                'am.observacion as observacion_matriz',
                'am.C1', 'am.C2', 'am.C3', 'am.C4', 'am.C5', 'am.C6',
                'am.C7', 'am.C8', 'am.C9', 'am.C10', 'am.C11',
                'am.C1_R', 'am.C2_R', 'am.C3_R', 'am.C4_R', 'am.C5_R', 'am.C6_R',
                'am.C7_R', 'am.C8_R', 'am.C9_R', 'am.C10_R', 'am.C11_R',
                'am.nivelar', 'am.no_nivelar'
            );
    }

    private function aplicarScope($query, array $scope)
    {
        if ($scope['ambito'] === 'escuela') {
            $query->where('p.id_escuela', (int) $scope['id_escuela']);
        } elseif ($scope['ambito'] === 'programa') {
            $query->where('ap.id_programa_nivelacion', (int) $scope['id_programa']);
        }

        return $query;
    }

    private function mapasDestino($fuente, string $periodoNombre): array
    {
        $codigos = $fuente->pluck('codigo')->map(fn ($v) => trim((string) $v))->filter()->unique()->values();
        $dnis = $fuente->pluck('dni')->map(fn ($v) => trim((string) $v))->filter()->unique()->values();

        $users = $codigos->isEmpty() ? collect() : DB::table('users')
            ->whereIn('email', $codigos)
            ->select('id', 'email', 'rol', 'estado', 'programa_id', 'id_escuela')
            ->get()
            ->keyBy(fn ($r) => trim((string) $r->email));

        $estudiantes = $codigos->isEmpty() ? collect() : DB::table('estudiante')
            ->whereIn('codigo_est', $codigos)
            ->select('id', 'codigo', 'codigo_est', 'dni', 'usuario_id')
            ->get()
            ->keyBy(fn ($r) => trim((string) $r->codigo_est));

        $matrices = $codigos->isEmpty() ? collect() : DB::table('matriz')
            ->whereIn('codigo_est', $codigos)
            ->select('id', 'codigo_est', 'dni')
            ->get()
            ->keyBy(fn ($r) => trim((string) $r->codigo_est));

        $ingresos = $codigos->isEmpty() ? collect() : DB::table('datos_ingreso')
            ->whereIn('codigo_est', $codigos)
            ->select('id', 'codigo_est', 'dni', 'semestre', 'id_programa')
            ->get()
            ->keyBy(fn ($r) => trim((string) $r->codigo_est));

        $mismoDniPeriodo = $dnis->isEmpty() ? collect() : DB::table('datos_ingreso')
            ->whereIn('dni', $dnis)
            ->where('semestre', $periodoNombre)
            ->select('dni', 'codigo_est', 'id_programa')
            ->get()
            ->groupBy(fn ($r) => trim((string) $r->dni));

        return compact('users', 'estudiantes', 'matrices', 'ingresos', 'mismoDniPeriodo');
    }

    private function evaluarRegistro($r, array $mapas, string $periodoNombre): array
    {
        $codigo = trim((string) $r->codigo);
        $dni = trim((string) $r->dni);

        $user = $mapas['users']->get($codigo);
        $est = $mapas['estudiantes']->get($codigo);
        $mat = $mapas['matrices']->get($codigo);
        $ing = $mapas['ingresos']->get($codigo);

        $flags = [
            'user' => (bool) $user,
            'estudiante' => (bool) $est,
            'matriz' => (bool) $mat,
            'datos_ingreso' => (bool) $ing,
        ];

        $problemas = [];

        if ((int) $r->codigos_distintos > 1 || (int) $r->programas_distintos > 1) {
            $problemas[] = 'El DNI aparece con más de un código o programa en Admisión dentro del mismo período.';
        }
        if ($codigo === '') {
            $problemas[] = 'El registro no tiene código universitario.';
        }
        if ($dni === '') {
            $problemas[] = 'El registro no tiene DNI.';
        }
        if (empty($r->paterno)) {
            $problemas[] = 'Falta apellido paterno requerido por la tabla estudiante.';
        }
        if (empty($r->id_programa)) {
            $problemas[] = 'No existe programa de Nivelación asociado.';
        }
        if (empty($r->id_escuela)) {
            $problemas[] = 'El programa no tiene Escuela Profesional asociada.';
        }

        if ($user) {
            if ((int) $user->rol !== 5) {
                $problemas[] = 'El código ya existe como usuario con un rol diferente a estudiante.';
            }
            if ((int) ($user->programa_id ?? 0) !== (int) $r->id_programa) {
                $problemas[] = 'El usuario existente pertenece a otro programa.';
            }
            if ((int) ($user->id_escuela ?? 0) !== (int) $r->id_escuela) {
                $problemas[] = 'El usuario existente pertenece a otra escuela.';
            }
        }
        if ($est && trim((string) $est->dni) !== $dni) {
            $problemas[] = 'El código ya pertenece a otro DNI en estudiante.';
        }
        if ($mat && trim((string) $mat->dni) !== $dni) {
            $problemas[] = 'El código ya pertenece a otro DNI en matriz.';
        }
        if ($ing) {
            if (trim((string) $ing->dni) !== $dni) {
                $problemas[] = 'El código ya pertenece a otro DNI en datos_ingreso.';
            }
            if ((int) ($ing->id_programa ?? 0) !== (int) $r->id_programa) {
                $problemas[] = 'El código ya está asociado a otro programa en datos_ingreso.';
            }
            if ((string) ($ing->semestre ?? '') !== $periodoNombre) {
                $problemas[] = 'El código ya existe en datos_ingreso con otro período.';
            }
        }
        if ($user && $est && (int) ($est->usuario_id ?? 0) !== (int) $user->id) {
            $problemas[] = 'El estudiante existente no está relacionado con el usuario de ese código.';
        }

        foreach ($mapas['mismoDniPeriodo']->get($dni, collect()) as $otro) {
            if (trim((string) $otro->codigo_est) !== $codigo) {
                $problemas[] = 'El mismo DNI ya está registrado con otro código en este período.';
                break;
            }
        }

        $presentes = collect($flags)->filter()->count();

        if ($problemas) {
            $estado = 'CONFLICTO';
        } elseif ($presentes === 0) {
            $estado = 'PENDIENTE';
        } elseif ($presentes === 4) {
            $estado = 'INTEGRADO';
        } else {
            $estado = 'PARCIAL';
        }

        $faltantes = [];
        foreach ($flags as $tabla => $existe) {
            if (!$existe) $faltantes[] = $tabla;
        }

        return [
            'estado' => $estado,
            'flags' => $flags,
            'problemas' => $problemas,
            'faltantes' => $faltantes,
        ];
    }

    public function datos(Request $request)
    {
        $scope = $this->validarScope($request);
        $periodo = DB::table('periodo')->where('id_periodo', $scope['id_periodo'])->first();
        abort_unless($periodo, 404, 'Período no encontrado.');

        $query = $this->fuenteCompletaQuery((int) $scope['id_periodo']);
        $this->aplicarScope($query, $scope);
        $fuente = $query->orderBy('e.nombre')->orderBy('p.programa')->orderBy('ap.paterno')->get();

        $mapas = $this->mapasDestino($fuente, (string) $periodo->nombre);

        $registros = $fuente->map(function ($r) use ($mapas, $periodo) {
            $ev = $this->evaluarRegistro($r, $mapas, (string) $periodo->nombre);
            return [
                'id_admision_postulante' => (int) $r->id_admision_postulante,
                'codigo' => $r->codigo,
                'dni' => $r->dni,
                'estudiante' => trim(implode(' ', array_filter([$r->paterno, $r->materno, $r->nombres]))),
                'id_escuela' => $r->id_escuela ? (int) $r->id_escuela : null,
                'escuela' => $r->escuela,
                'id_programa' => (int) $r->id_programa,
                'programa' => $r->programa,
                'proceso' => $r->proceso_nombre,
                'modalidad' => $r->modalidad,
                'estado' => $ev['estado'],
                'user' => $ev['flags']['user'],
                't_estudiante' => $ev['flags']['estudiante'],
                't_matriz' => $ev['flags']['matriz'],
                't_datos_ingreso' => $ev['flags']['datos_ingreso'],
                'faltantes' => $ev['faltantes'],
                'problemas' => $ev['problemas'],
            ];
        })->values();

        $resumen = [
            'completos_admision' => $registros->count(),
            'pendientes' => $registros->where('estado', 'PENDIENTE')->count(),
            'integrados' => $registros->where('estado', 'INTEGRADO')->count(),
            'parciales' => $registros->where('estado', 'PARCIAL')->count(),
            'conflictos' => $registros->where('estado', 'CONFLICTO')->count(),
        ];

        $programasPendientes = $registros
            ->where('estado', 'PENDIENTE')
            ->groupBy('id_programa')
            ->map(function ($items, $idPrograma) {
                $primero = $items->first();
                return [
                    'id_programa' => (int) $idPrograma,
                    'programa' => $primero['programa'],
                    'escuela' => $primero['escuela'],
                    'pendientes' => $items->count(),
                ];
            })
            ->values();

        return response()->json([
            'estado' => true,
            'periodo' => [
                'id_periodo' => (int) $periodo->id_periodo,
                'nombre' => $periodo->nombre,
                'estado' => $periodo->estado,
            ],
            'resumen' => $resumen,
            'registros' => $registros,
            'programas_pendientes' => $programasPendientes,
        ]);
    }

    private function fuentePrograma(int $idPeriodo, int $idPrograma)
    {
        return $this->fuenteCompletaQuery($idPeriodo)
            ->where('ap.id_programa_nivelacion', $idPrograma)
            ->orderBy('ap.paterno')
            ->get();
    }

    private function insertarEstudiante($r, $periodo): void
    {
        $codigo = trim((string) $r->codigo);
        $dni = trim((string) $r->dni);

        DB::transaction(function () use ($r, $periodo, $codigo, $dni) {
            // Revalidación con bloqueos antes de insertar.
            $userExistente = DB::table('users')->where('email', $codigo)->lockForUpdate()->first();
            $estExistente = DB::table('estudiante')->where('codigo_est', $codigo)->lockForUpdate()->first();
            $matExistente = DB::table('matriz')->where('codigo_est', $codigo)->lockForUpdate()->first();
            $ingExistente = DB::table('datos_ingreso')->where('codigo_est', $codigo)->lockForUpdate()->first();
            $dniPeriodo = DB::table('datos_ingreso')
                ->where('dni', $dni)
                ->where('semestre', (string) $periodo->nombre)
                ->where('codigo_est', '<>', $codigo)
                ->lockForUpdate()
                ->exists();

            if ($userExistente || $estExistente || $matExistente || $ingExistente || $dniPeriodo) {
                throw new \RuntimeException('El registro dejó de estar pendiente; recargue la vista para revisar su estado.');
            }
            if (empty($r->paterno)) {
                throw new \RuntimeException('Falta apellido paterno.');
            }
            if (empty($r->id_programa) || empty($r->id_escuela)) {
                throw new \RuntimeException('Falta programa o escuela de Nivelación.');
            }

            $ahora = now();
            $usuarioId = DB::table('users')->insertGetId([
                'email' => $codigo,
                'password' => Hash::make($dni),
                'estado' => 1,
                'programa_id' => (int) $r->id_programa,
                'rol' => 5,
                'id_escuela' => (int) $r->id_escuela,
                'estado_contraseña' => 1,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]);

            $datosEstudiante = [
                'codigo' => $codigo,
                'codigo_est' => $codigo,
                'dni' => $dni,
                'paterno' => $r->paterno,
                'materno' => $r->materno,
                'nombres' => $r->nombres,
                'sexo' => $r->sexo,
                'email' => $r->email,
                'f_nacimiento' => $r->f_nacimiento,
                'ubigeo_nacimiento' => $r->ubigeo_nacimiento,
                'estado_civil' => $r->estado_civil,
                'anio_egreso' => $r->anio_egreso,
                'tipo_colegio' => $r->tipo_colegio,
                'nombre_colegio' => $r->nombre_colegio,
                'ubigeo_colegio' => $r->ubigeo_colegio,
                'apto' => null,
                'direccion' => $r->direccion,
                'telefono' => $r->telefono,
                'usuario_id' => $usuarioId,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];
            if (Schema::hasColumn('estudiante', 'estado_nivelacion')) {
                $datosEstudiante['estado_nivelacion'] = 1;
            }
            DB::table('estudiante')->insert($datosEstudiante);

            DB::table('matriz')->insert([
                'dni' => $dni,
                'codigo_est' => $codigo,
                'C1' => $r->C1, 'C2' => $r->C2, 'C3' => $r->C3, 'C4' => $r->C4,
                'C5' => $r->C5, 'C6' => $r->C6, 'C7' => $r->C7, 'C8' => $r->C8,
                'C9' => $r->C9, 'C10' => $r->C10, 'C11' => $r->C11,
                'C1_R' => $r->C1_R, 'C2_R' => $r->C2_R, 'C3_R' => $r->C3_R,
                'C4_R' => $r->C4_R, 'C5_R' => $r->C5_R, 'C6_R' => $r->C6_R,
                'C7_R' => $r->C7_R, 'C8_R' => $r->C8_R, 'C9_R' => $r->C9_R,
                'C10_R' => $r->C10_R, 'C11_R' => $r->C11_R,
                'nivelar' => $r->nivelar,
                'no_nivelar' => $r->no_nivelar,
            ]);

            $anio = $r->anio_proceso ?: substr((string) $periodo->nombre, 0, 4);
            $tipoExamen = trim((string) ($r->proceso_nombre ?: 'Ordinario'));
            $tipoExamen = mb_substr($tipoExamen, 0, 30);
            $observacion = mb_substr((string) ($r->observacion_matriz ?? ''), 0, 150);

            DB::table('datos_ingreso')->insert([
                'dni' => $dni,
                'codigo_est' => $codigo,
                'anio' => $anio,
                'semestre' => (string) $periodo->nombre,
                'f_examen' => $r->f_examen,
                't_examen' => $tipoExamen,
                'puntaje_examen' => $r->puntaje !== null ? (string) $r->puntaje : '0',
                'modalidad' => $r->modalidad ?: 'Regular',
                'id_programa' => (int) $r->id_programa,
                'observacion' => $observacion,
            ]);
        });
    }

    public function integrar(Request $request)
    {
        $this->asegurarSuperadmin();
        $data = $request->validate([
            'id_periodo' => ['required', 'integer', 'exists:periodo,id_periodo'],
            'id_programa' => ['required', 'integer', 'exists:programa,id'],
        ]);

        $periodo = DB::table('periodo')->where('id_periodo', (int) $data['id_periodo'])->first();
        $fuente = $this->fuentePrograma((int) $data['id_periodo'], (int) $data['id_programa']);
        $mapas = $this->mapasDestino($fuente, (string) $periodo->nombre);

        $resultado = [
            'total_fuente' => $fuente->count(),
            'integrados' => 0,
            'ya_integrados' => 0,
            'parciales' => 0,
            'conflictos' => 0,
            'errores' => [],
        ];

        foreach ($fuente as $r) {
            $ev = $this->evaluarRegistro($r, $mapas, (string) $periodo->nombre);

            if ($ev['estado'] === 'INTEGRADO') {
                $resultado['ya_integrados']++;
                continue;
            }
            if ($ev['estado'] === 'PARCIAL') {
                $resultado['parciales']++;
                continue;
            }
            if ($ev['estado'] === 'CONFLICTO') {
                $resultado['conflictos']++;
                continue;
            }

            try {
                $this->insertarEstudiante($r, $periodo);
                $resultado['integrados']++;
            } catch (Throwable $e) {
                $resultado['errores'][] = [
                    'dni' => $r->dni,
                    'codigo' => $r->codigo,
                    'estudiante' => trim(implode(' ', array_filter([$r->paterno, $r->materno, $r->nombres]))),
                    'mensaje' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'estado' => true,
            'tipo' => empty($resultado['errores']) ? 'success' : 'warn',
            'titulo' => 'INTEGRACIÓN A NIVELACIÓN',
            'mensaje' => "Se integraron {$resultado['integrados']} estudiante(s) en el programa seleccionado.",
            'datos' => $resultado,
        ]);
    }
}
