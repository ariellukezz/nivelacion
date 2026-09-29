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
