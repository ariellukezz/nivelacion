<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PsicopedagogicoController extends Controller
{
    private array $condiciones = [
        'Regular',
        'Segunda matrícula',
        'Riesgo académico con 3ra matrícula',
        'Riesgo académico con 4ta matrícula',
        'Plataforma Intranet',
        'Becario',
    ];

    private array $diagnosticos = [
        'Problemas socioemocionales',
        'Estrés académico',
        'Respuesta ansiosa',
        'Dificultades en habilidades sociales',
        'Dificultades en hábitos de estudio',
        'Reacción mixta de ansiedad y depresión',
        'Dificultades en habilidades socioemocionales',
        'Dificultades en el estilo de vida',
        'Problemas con el consumo de sustancias (alcohol, marihuana, otras sustancias)',
        'Problemas en el ámbito sociofamiliar',
        'Déficit de atención - concentración',
        'Otros problemas psicológicos',
    ];

    private array $problemasAcademicos = [
        'Cruce de horarios',
        'Dificultades con el docente',
        'Problemas con la malla curricular',
        'Retiro de curso o reserva de matrícula',
        'Consultas sobre reglamentos',
        'Otros problemas académicos',
    ];

    public function index()
    {
        $profesionales = DB::table('psicopedagogico_profesional')
            ->where('estado', 1)
            ->select('id', 'nombre')
            ->orderBy('nombre')
            ->get();

        $periodoActivo = DB::table('periodo')
            ->where('estado', 'activo')
            ->select('id_periodo', 'nombre')
            ->first();

        return Inertia::render('Psicopedagogico/Index', [
            'profesionales' => $profesionales,
            'periodoActivo' => $periodoActivo,
            'condiciones' => $this->condiciones,
            'diagnosticos' => $this->diagnosticos,
            'problemasAcademicos' => $this->problemasAcademicos,
        ]);
    }

    /**
     * Busca por código o DNI en padron_estudiantes.
     * Además devuelve el historial de atenciones existentes.
     */
    public function buscarEstudiante(Request $request)
    {
        $data = $request->validate([
            'buscar' => ['required', 'string', 'max:20'],
        ]);

        $buscar = trim((string) $data['buscar']);

        $estudiante = DB::table('padron_estudiantes')
            ->where('codigo', $buscar)
            ->orWhere('dni', $buscar)
            ->select(
                'codigo',
                'dni',
                'nombre_completo',
                'facultad',
                'escuela_profesional',
                'programa',
                'plan',
                'ciclo',
                'creditos',
                'sexo',
                'edad',
                'departamento',
                'provincia',
                'distrito'
            )
            ->first();

        if (!$estudiante) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'No se encontró al estudiante en el padrón.',
                'estudiante' => null,
                'historial' => [],
                'numero_sesion' => 1,
            ], 404);
        }

        $historial = DB::table('psicopedagogico_atencion as a')
            ->join('psicopedagogico_profesional as p', 'p.id', '=', 'a.id_profesional')
            ->where(function ($q) use ($estudiante) {
                $q->where('a.codigo_estudiante', $estudiante->codigo)
                  ->orWhere('a.dni', $estudiante->dni);
            })
            ->select(
                'a.id',
                'a.fecha_atencion',
                'a.semestre_academico',
                'a.numero_sesion',
                'a.tipo_atencion',
                'a.condicion_academica',
                'a.discapacidad',
                'a.presuncion_diagnostica',
                'a.otro_diagnostico',
                'a.problemas_academicos',
                'a.otro_problema_academico',
                'a.observaciones',
                'a.satisfaccion',
                'a.derivacion',
                'a.seguimiento',
                'a.requiere_seguimiento',
                'a.celular',
                'p.nombre as profesional'
            )
            ->orderByDesc('a.numero_sesion')
            ->orderByDesc('a.fecha_atencion')
            ->get()
            ->map(function ($r) {
                $r->condicion_academica = $this->decodeJsonArray($r->condicion_academica);
                $r->presuncion_diagnostica = $this->decodeJsonArray($r->presuncion_diagnostica);
                $r->problemas_academicos = $this->decodeJsonArray($r->problemas_academicos);
                $r->requiere_seguimiento = (bool) $r->requiere_seguimiento;
                return $r;
            });

        $ultimaSesion = $historial->max('numero_sesion') ?: 0;

        return response()->json([
            'estado' => true,
            'estudiante' => $estudiante,
            'historial' => $historial,
            'numero_sesion' => ((int) $ultimaSesion) + 1,
        ]);
    }

    private function decodeJsonArray($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (!$value) {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_profesional' => ['required', 'integer', 'exists:psicopedagogico_profesional,id'],
            'fecha_atencion' => ['required', 'date'],
            'semestre_academico' => ['required', 'string', 'max:20'],

            'facultad' => ['required', 'string', 'max:255'],
            'escuela_profesional' => ['required', 'string', 'max:255'],
            'estudiante' => ['required', 'string', 'max:255'],
            'edad' => ['required', 'integer', 'min:12', 'max:100'],
            'sexo' => ['required', 'string', 'max:30'],
            'codigo_estudiante' => ['required', 'string', 'max:30'],
            'dni' => ['required', 'string', 'max:20'],
            'celular' => ['nullable', 'string', 'max:30'],
            'ciclo' => ['required', 'string', 'max:30'],

            'condicion_academica' => ['nullable', 'array'],
            'condicion_academica.*' => ['string', 'max:180'],

            'discapacidad' => ['required', 'in:NO,SI_CONADIS,SI_SIN_CONADIS'],

            'presuncion_diagnostica' => ['nullable', 'array'],
            'presuncion_diagnostica.*' => ['string', 'max:250'],
            'otro_diagnostico' => ['nullable', 'string', 'max:1000'],

            'problemas_academicos' => ['nullable', 'array'],
            'problemas_academicos.*' => ['string', 'max:250'],
            'otro_problema_academico' => ['nullable', 'string', 'max:1000'],

            'observaciones' => ['nullable', 'string', 'max:3000'],
            'tipo_atencion' => ['required', 'in:PRESENCIAL,VIRTUAL'],
            'satisfaccion' => ['nullable', 'string', 'max:100'],
            'derivacion' => ['nullable', 'string', 'max:2000'],
            'seguimiento' => ['nullable', 'string', 'max:2000'],
            'requiere_seguimiento' => ['nullable', 'boolean'],

            'evidencia' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:8192'],
        ]);

        // Verificar que el código/DNI siga existiendo en el padrón y corresponda a la misma persona.
        $padron = DB::table('padron_estudiantes')
            ->where('codigo', trim((string) $data['codigo_estudiante']))
            ->where('dni', trim((string) $data['dni']))
            ->first();

        if (!$padron) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'El código y DNI no coinciden con un registro del padrón de estudiantes.',
            ], 422);
        }

        $codigo = trim((string) $data['codigo_estudiante']);

        return DB::transaction(function () use ($request, $data, $codigo) {
            $ultimaSesion = DB::table('psicopedagogico_atencion')
                ->where('codigo_estudiante', $codigo)
                ->lockForUpdate()
                ->max('numero_sesion');

            $numeroSesion = ((int) $ultimaSesion) + 1;

            $evidenciaPath = null;
            $evidenciaNombre = null;

            if ($request->hasFile('evidencia')) {
                $archivo = $request->file('evidencia');
                $evidenciaNombre = $archivo->getClientOriginalName();
                $evidenciaPath = $archivo->store('psicopedagogico/evidencias', 'local');
            }

            $id = DB::table('psicopedagogico_atencion')->insertGetId([
                'id_profesional' => (int) $data['id_profesional'],
                'fecha_atencion' => $data['fecha_atencion'],
                'semestre_academico' => trim((string) $data['semestre_academico']),

                'facultad' => trim((string) $data['facultad']),
                'escuela_profesional' => trim((string) $data['escuela_profesional']),
                'estudiante' => trim((string) $data['estudiante']),
                'edad' => (int) $data['edad'],
                'sexo' => trim((string) $data['sexo']),
                'codigo_estudiante' => $codigo,
                'dni' => trim((string) $data['dni']),
                'numero_sesion' => $numeroSesion,
                'celular' => trim((string) ($data['celular'] ?? '')),
                'ciclo' => trim((string) $data['ciclo']),

                'condicion_academica' => json_encode(
                    array_values($data['condicion_academica'] ?? []),
                    JSON_UNESCAPED_UNICODE
                ),
                'discapacidad' => $data['discapacidad'],

                'presuncion_diagnostica' => json_encode(
                    array_values($data['presuncion_diagnostica'] ?? []),
                    JSON_UNESCAPED_UNICODE
                ),
                'otro_diagnostico' => $data['otro_diagnostico'] ?? null,

                'problemas_academicos' => json_encode(
                    array_values($data['problemas_academicos'] ?? []),
                    JSON_UNESCAPED_UNICODE
                ),
                'otro_problema_academico' => $data['otro_problema_academico'] ?? null,

                'observaciones' => $data['observaciones'] ?? null,
                'tipo_atencion' => $data['tipo_atencion'],
                'satisfaccion' => $data['satisfaccion'] ?? null,
                'derivacion' => $data['derivacion'] ?? null,
                'seguimiento' => $data['seguimiento'] ?? null,
                'requiere_seguimiento' => !empty($data['requiere_seguimiento']) ? 1 : 0,

                'evidencia_path' => $evidenciaPath,
                'evidencia_nombre' => $evidenciaNombre,

                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'estado' => true,
                'mensaje' => 'La atención psicopedagógica fue registrada correctamente.',
                'id' => $id,
                'numero_sesion' => $numeroSesion,
            ], 201);
        });
    }
}
