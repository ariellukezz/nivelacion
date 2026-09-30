<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PsicopedagogicoReporteController extends Controller
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
            ->select('id', 'nombre', 'estado')
            ->orderBy('nombre')
            ->get();

        $semestres = DB::table('psicopedagogico_atencion')
            ->whereNotNull('semestre_academico')
            ->where('semestre_academico', '<>', '')
            ->distinct()
            ->orderByDesc('semestre_academico')
            ->pluck('semestre_academico')
            ->values();

        $facultades = DB::table('psicopedagogico_atencion')
            ->whereNotNull('facultad')
            ->where('facultad', '<>', '')
            ->distinct()
            ->orderBy('facultad')
            ->pluck('facultad')
            ->values();

        $escuelas = DB::table('psicopedagogico_atencion')
            ->whereNotNull('escuela_profesional')
            ->where('escuela_profesional', '<>', '')
            ->distinct()
            ->orderBy('escuela_profesional')
            ->pluck('escuela_profesional')
            ->values();

        return Inertia::render('Supervisor/Psicopedagogico/Reporte', [
            'profesionales' => $profesionales,
            'semestres' => $semestres,
            'facultades' => $facultades,
            'escuelas' => $escuelas,
            'condiciones' => $this->condiciones,
            'diagnosticos' => $this->diagnosticos,
            'problemasAcademicos' => $this->problemasAcademicos,
        ]);
    }

    private function queryBase(Request $request)
    {
        $q = DB::table('psicopedagogico_atencion as a')
            ->join(
                'psicopedagogico_profesional as p',
                'p.id',
                '=',
                'a.id_profesional'
            )
            ->select(
                'a.*',
                'p.nombre as profesional'
            );

        if ($request->filled('semestre')) {
            $q->where('a.semestre_academico', trim((string) $request->semestre));
        }

        if ($request->filled('id_profesional')) {
            $q->where('a.id_profesional', (int) $request->id_profesional);
        }

        if ($request->filled('facultad')) {
            $q->where('a.facultad', trim((string) $request->facultad));
        }

        if ($request->filled('escuela')) {
            $q->where('a.escuela_profesional', trim((string) $request->escuela));
        }

        if ($request->filled('sexo')) {
            $q->where('a.sexo', trim((string) $request->sexo));
        }

        if ($request->filled('ciclo')) {
            $q->where('a.ciclo', trim((string) $request->ciclo));
        }

        if ($request->filled('tipo_atencion')) {
            $q->where('a.tipo_atencion', trim((string) $request->tipo_atencion));
        }

        if ($request->filled('seguimiento')) {
            $q->where(
                'a.requiere_seguimiento',
                $request->seguimiento === 'SI' ? 1 : 0
            );
        }

        if ($request->filled('condicion')) {
            $q->whereJsonContains(
                'a.condicion_academica',
                trim((string) $request->condicion)
            );
        }

        if ($request->filled('diagnostico')) {
            $q->whereJsonContains(
                'a.presuncion_diagnostica',
                trim((string) $request->diagnostico)
            );
        }

        if ($request->filled('problema_academico')) {
            $q->whereJsonContains(
                'a.problemas_academicos',
                trim((string) $request->problema_academico)
            );
        }

        if ($request->filled('buscar')) {
            $term = trim((string) $request->buscar);

            $q->where(function ($x) use ($term) {
                $x->where('a.codigo_estudiante', 'LIKE', '%' . $term . '%')
                    ->orWhere('a.dni', 'LIKE', '%' . $term . '%')
                    ->orWhere('a.estudiante', 'LIKE', '%' . $term . '%');
            });
        }

        return $q;
    }

    public function data(Request $request)
    {
        $q = $this->queryBase($request);

        $resumenBase = clone $q;

        $resumen = [
            'estudiantes' => (clone $resumenBase)
                ->distinct()
                ->count('a.codigo_estudiante'),

            'atenciones' => (clone $resumenBase)->count(),

            'presenciales' => (clone $resumenBase)
                ->where('a.tipo_atencion', 'PRESENCIAL')
                ->count(),

            'virtuales' => (clone $resumenBase)
                ->where('a.tipo_atencion', 'VIRTUAL')
                ->count(),

            'seguimientos' => (clone $resumenBase)
                ->where('a.requiere_seguimiento', 1)
                ->count(),

            'derivaciones' => (clone $resumenBase)
                ->whereNotNull('a.derivacion')
                ->where('a.derivacion', '<>', '')
                ->count(),

            'con_evidencia' => (clone $resumenBase)
                ->whereNotNull('a.evidencia_path')
                ->where('a.evidencia_path', '<>', '')
                ->count(),
        ];

        $perPage = min(
            max((int) $request->input('per_page', 25), 10),
            100
        );

        $datos = $q
            ->orderByDesc('a.fecha_atencion')
            ->orderByDesc('a.id')
            ->paginate($perPage);

        $datos->getCollection()->transform(function ($r) {
            $r->condicion_academica = $this->decodeArray($r->condicion_academica);
            $r->presuncion_diagnostica = $this->decodeArray($r->presuncion_diagnostica);
            $r->problemas_academicos = $this->decodeArray($r->problemas_academicos);
            $r->requiere_seguimiento = (bool) $r->requiere_seguimiento;

            $r->evidencia_extension = $this->extension(
                $r->evidencia_nombre ?: $r->evidencia_path
            );

            $r->tiene_evidencia = !empty($r->evidencia_path);

            return $r;
        });

        return response()->json([
            'estado' => true,
            'resumen' => $resumen,
            'datos' => $datos,
        ]);
    }

    private function decodeArray($value): array
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

    private function extension($nombre): string
    {
        if (!$nombre) {
            return '';
        }

        return strtolower(pathinfo((string) $nombre, PATHINFO_EXTENSION));
    }

    /**
     * Devuelve TODOS los registros que coinciden con los filtros actuales.
     * Se usa exclusivamente para generar el Excel desde el frontend.
     */
    public function exportData(Request $request)
    {
        $q = $this->queryBase($request);

        $registros = $q
            ->orderByDesc('a.fecha_atencion')
            ->orderByDesc('a.id')
            ->get()
            ->map(function ($r) {
                $condiciones = $this->decodeArray($r->condicion_academica);
                $diagnosticos = $this->decodeArray($r->presuncion_diagnostica);
                $problemas = $this->decodeArray($r->problemas_academicos);

                return [
                    'Fecha' => $r->fecha_atencion,
                    'Semestre académico' => $r->semestre_academico,
                    'Profesional responsable' => $r->profesional,
                    'N.º sesión' => $r->numero_sesion,

                    'Código estudiante' => $r->codigo_estudiante,
                    'DNI' => $r->dni,
                    'Estudiante' => $r->estudiante,
                    'Edad' => $r->edad,
                    'Sexo' => $r->sexo,
                    'Celular' => $r->celular,

                    'Facultad' => $r->facultad,
                    'Escuela Profesional' => $r->escuela_profesional,
                    'Ciclo' => $r->ciclo,

                    'Condición académica' => implode(' | ', $condiciones),

                    'Discapacidad' => match ($r->discapacidad) {
                        'SI_CONADIS' => 'Sí, con carnet CONADIS',
                        'SI_SIN_CONADIS' => 'Sí, sin carnet CONADIS',
                        default => 'No',
                    },

                    'Presunción diagnóstica' => implode(' | ', $diagnosticos),
                    'Otro diagnóstico' => $r->otro_diagnostico,

                    'Problemas académicos' => implode(' | ', $problemas),
                    'Otro problema académico' => $r->otro_problema_academico,

                    'Observaciones' => $r->observaciones,
                    'Tipo de atención' => $r->tipo_atencion,
                    'Encuesta de satisfacción' => $r->satisfaccion,
                    'Derivación' => $r->derivacion,

                    'Requiere seguimiento' => $r->requiere_seguimiento ? 'Sí' : 'No',
                    'Seguimiento' => $r->seguimiento,

                    'Tiene evidencia' => !empty($r->evidencia_path) ? 'Sí' : 'No',
                    'Nombre evidencia' => $r->evidencia_nombre,
                ];
            })
            ->values();

        return response()->json([
            'estado' => true,
            'total' => $registros->count(),
            'datos' => $registros,
        ]);
    }

    /**
     * Actualiza una atención desde el rol Supervisor.
     * Los datos de identidad del estudiante NO se modifican aquí:
     * código, DNI, nombres, facultad, escuela, edad, sexo, ciclo y N.º de sesión.
     */
    public function update(Request $request, int $id)
    {
        $registro = DB::table('psicopedagogico_atencion')
            ->where('id', $id)
            ->first();

        if (!$registro) {
            abort(404, 'La atención no existe.');
        }

        $data = $request->validate([
            'id_profesional' => [
                'required',
                'integer',
                'exists:psicopedagogico_profesional,id'
            ],

            'fecha_atencion' => [
                'required',
                'date'
            ],

            'semestre_academico' => [
                'required',
                'string',
                'max:20'
            ],

            'celular' => [
                'nullable',
                'string',
                'max:30'
            ],

            'condicion_academica' => [
                'nullable',
                'array'
            ],

            'condicion_academica.*' => [
                'string',
                'max:180'
            ],

            'discapacidad' => [
                'required',
                'in:NO,SI_CONADIS,SI_SIN_CONADIS'
            ],

            'presuncion_diagnostica' => [
                'nullable',
                'array'
            ],

            'presuncion_diagnostica.*' => [
                'string',
                'max:250'
            ],

            'otro_diagnostico' => [
                'nullable',
                'string',
                'max:1000'
            ],

            'problemas_academicos' => [
                'nullable',
                'array'
            ],

            'problemas_academicos.*' => [
                'string',
                'max:250'
            ],

            'otro_problema_academico' => [
                'nullable',
                'string',
                'max:1000'
            ],

            'observaciones' => [
                'nullable',
                'string',
                'max:3000'
            ],

            'tipo_atencion' => [
                'required',
                'in:PRESENCIAL,VIRTUAL'
            ],

            'satisfaccion' => [
                'nullable',
                'string',
                'max:100'
            ],

            'derivacion' => [
                'nullable',
                'string',
                'max:2000'
            ],

            'seguimiento' => [
                'nullable',
                'string',
                'max:2000'
            ],

            'requiere_seguimiento' => [
                'nullable',
                'boolean'
            ],

            'evidencia' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:8192'
            ],

            'eliminar_evidencia' => [
                'nullable',
                'boolean'
            ],
        ]);

        $disk = Storage::disk('local');

        $rutaAnterior = $registro->evidencia_path;
        $rutaNueva = $rutaAnterior;
        $nombreNuevo = $registro->evidencia_nombre;

        $archivoNuevoGuardado = null;

        try {
            if ($request->hasFile('evidencia')) {
                $archivo = $request->file('evidencia');

                $archivoNuevoGuardado = $archivo->store(
                    'psicopedagogico/evidencias',
                    'local'
                );

                $rutaNueva = $archivoNuevoGuardado;
                $nombreNuevo = $archivo->getClientOriginalName();
            } elseif ($request->boolean('eliminar_evidencia')) {
                $rutaNueva = null;
                $nombreNuevo = null;
            }

            DB::table('psicopedagogico_atencion')
                ->where('id', $id)
                ->update([
                    'id_profesional' => (int) $data['id_profesional'],
                    'fecha_atencion' => $data['fecha_atencion'],
                    'semestre_academico' => trim(
                        (string) $data['semestre_academico']
                    ),

                    'celular' => trim(
                        (string) ($data['celular'] ?? '')
                    ),

                    'condicion_academica' => json_encode(
                        array_values(
                            $data['condicion_academica'] ?? []
                        ),
                        JSON_UNESCAPED_UNICODE
                    ),

                    'discapacidad' => $data['discapacidad'],

                    'presuncion_diagnostica' => json_encode(
                        array_values(
                            $data['presuncion_diagnostica'] ?? []
                        ),
                        JSON_UNESCAPED_UNICODE
                    ),

                    'otro_diagnostico' =>
                        $data['otro_diagnostico'] ?? null,

                    'problemas_academicos' => json_encode(
                        array_values(
                            $data['problemas_academicos'] ?? []
                        ),
                        JSON_UNESCAPED_UNICODE
                    ),

                    'otro_problema_academico' =>
                        $data['otro_problema_academico'] ?? null,

                    'observaciones' =>
                        $data['observaciones'] ?? null,

                    'tipo_atencion' =>
                        $data['tipo_atencion'],

                    'satisfaccion' =>
                        $data['satisfaccion'] ?? null,

                    'derivacion' =>
                        $data['derivacion'] ?? null,

                    'requiere_seguimiento' =>
                        !empty($data['requiere_seguimiento'])
                            ? 1
                            : 0,

                    'seguimiento' =>
                        $data['seguimiento'] ?? null,

                    'evidencia_path' => $rutaNueva,
                    'evidencia_nombre' => $nombreNuevo,

                    'updated_at' => now(),
                ]);

        } catch (\Throwable $e) {
            // Si se almacenó un archivo nuevo pero la BD falló,
            // retirarlo para no dejar archivos huérfanos.
            if (
                $archivoNuevoGuardado &&
                $disk->exists($archivoNuevoGuardado)
            ) {
                $disk->delete($archivoNuevoGuardado);
            }

            throw $e;
        }

        // Si se reemplazó o eliminó una evidencia,
        // retirar el archivo antiguo después de actualizar la BD.
        if (
            $rutaAnterior &&
            $rutaAnterior !== $rutaNueva &&
            $disk->exists($rutaAnterior)
        ) {
            $disk->delete($rutaAnterior);
        }

        return response()->json([
            'estado' => true,
            'mensaje' => 'La atención fue actualizada correctamente.',
        ]);
    }

    /**
     * Elimina definitivamente una atención.
     * Si tiene evidencia, también elimina el archivo privado.
     */
    public function destroy(int $id)
    {
        $registro = DB::table('psicopedagogico_atencion')
            ->where('id', $id)
            ->select(
                'id',
                'evidencia_path'
            )
            ->first();

        if (!$registro) {
            abort(404, 'La atención no existe.');
        }

        DB::table('psicopedagogico_atencion')
            ->where('id', $id)
            ->delete();

        if ($registro->evidencia_path) {
            $disk = Storage::disk('local');

            if ($disk->exists($registro->evidencia_path)) {
                $disk->delete($registro->evidencia_path);
            }
        }

        return response()->json([
            'estado' => true,
            'mensaje' => 'La atención fue eliminada correctamente.',
        ]);
    }

    /**
     * Muestra la foto/PDF en el navegador.
     * La ruta debe permanecer dentro del grupo Supervisor.
     */
    public function evidencia(int $id)
    {
        $registro = DB::table('psicopedagogico_atencion')
            ->where('id', $id)
            ->select(
                'evidencia_path',
                'evidencia_nombre'
            )
            ->first();

        if (!$registro || !$registro->evidencia_path) {
            abort(404, 'El registro no tiene evidencia.');
        }

        $disk = Storage::disk('local');

        if (!$disk->exists($registro->evidencia_path)) {
            abort(404, 'El archivo de evidencia no existe.');
        }

        $path = $disk->path($registro->evidencia_path);

        return response()->file($path, [
            'Content-Disposition' =>
                'inline; filename="' .
                addslashes(
                    $registro->evidencia_nombre
                        ?: basename($registro->evidencia_path)
                ) .
                '"',
        ]);
    }

    public function descargarEvidencia(int $id)
    {
        $registro = DB::table('psicopedagogico_atencion')
            ->where('id', $id)
            ->select(
                'evidencia_path',
                'evidencia_nombre'
            )
            ->first();

        if (!$registro || !$registro->evidencia_path) {
            abort(404);
        }

        $disk = Storage::disk('local');

        if (!$disk->exists($registro->evidencia_path)) {
            abort(404);
        }

        return $disk->download(
            $registro->evidencia_path,
            $registro->evidencia_nombre
                ?: basename($registro->evidencia_path)
        );
    }
}
