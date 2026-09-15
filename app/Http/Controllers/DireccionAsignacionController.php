<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DireccionAsignacionController extends Controller
{
    private function asegurarSuperadmin(): void
    {
        if ((int) (auth()->user()->rol ?? -1) !== 0) {
            abort(403, 'Solo Super Admin puede ejecutar operaciones masivas.');
        }
    }

    private function validarScope(Request $request): array
    {
        $this->asegurarSuperadmin();

        $data = $request->validate([
            'ambito' => ['required', 'in:todas,escuela,programa'],
            'id_escuela' => ['nullable', 'integer'],
            'id_programa' => ['nullable', 'integer'],
            'grupo' => ['nullable', 'in:A,B,C,D,E'],
        ]);

        if ($data['ambito'] === 'escuela') {
            if (empty($data['id_escuela']) || !DB::table('escuela')->where('id', $data['id_escuela'])->exists()) {
                abort(422, 'Debe seleccionar una Escuela Profesional válida.');
            }
        }

        if ($data['ambito'] === 'programa') {
            if (empty($data['id_programa'])) {
                abort(422, 'Debe seleccionar un programa de estudio.');
            }

            $programa = DB::table('programa')->where('id', $data['id_programa'])->first();
            if (!$programa) {
                abort(422, 'El programa seleccionado no existe.');
            }

            if (!empty($data['id_escuela']) && (int) $programa->id_escuela !== (int) $data['id_escuela']) {
                abort(422, 'El programa seleccionado no pertenece a la escuela indicada.');
            }

            $data['id_escuela'] = (int) $programa->id_escuela;
        }

        $data['grupo'] = $data['grupo'] ?? 'A';
        return $data;
    }

    private function programasQuery(array $data)
    {
        $query = DB::table('programa')
            ->join('escuela', 'escuela.id', '=', 'programa.id_escuela')
            ->whereNotNull('programa.id_escuela')
            ->select(
                'programa.id',
                'programa.programa',
                'programa.id_escuela',
                'escuela.nombre as escuela'
            );

        if ($data['ambito'] === 'escuela') {
            $query->where('programa.id_escuela', (int) $data['id_escuela']);
        } elseif ($data['ambito'] === 'programa') {
            $query->where('programa.id', (int) $data['id_programa']);
        }

        return $query;
    }

    private function filasCompetencias(array $data): Collection
    {
        $idsProgramas = $this->programasQuery($data)->pluck('programa.id');

        if ($idsProgramas->isEmpty()) {
            return collect();
        }

        return DB::table('competencia_programa as cp')
            ->join('programa as p', 'p.id', '=', 'cp.id_programa')
            ->join('escuela as e', 'e.id', '=', 'p.id_escuela')
            ->join('competencia as c', 'c.id', '=', 'cp.id_competencia')
            ->whereIn('cp.id_programa', $idsProgramas)
            ->where('cp.estado', 1)
            ->select(
                'p.id as id_programa',
                'p.programa',
                'e.nombre as escuela',
                'c.id as id_competencia',
                'c.nombre as competencia'
            )
            ->distinct()
            ->orderBy('p.id')
            ->orderBy('c.id')
            ->get();
    }

    private function columnasCompetencia(): array
    {
        return [
            1 => 'C1_R', 2 => 'C2_R', 3 => 'C3_R', 4 => 'C4_R', 5 => 'C5_R',
            6 => 'C6_R', 7 => 'C7_R', 8 => 'C8_R', 9 => 'C9_R', 10 => 'C10_R', 11 => 'C11_R',
        ];
    }

    /**
     * Devuelve cuántos estudiantes ACTIVOS siguen pendientes de matricular
     * para cada Programa + Competencia en el período activo.
     *
     * Un estudiante cuenta como demanda pendiente cuando:
     * - pertenece al programa;
     * - estado_nivelacion = 1;
     * - tiene Cx_R <= 10.49;
     * - todavía NO está matriculado en esa competencia, en ningún grupo,
     *   durante el período activo.
     *
     * La clave del arreglo es: "idPrograma-idCompetencia".
     */
    private function mapaDemandaPendiente(Collection $idsProgramas, int $idPeriodo): array
    {
        if ($idsProgramas->isEmpty()) {
            return [];
        }

        $mapa = [];

        foreach ($this->columnasCompetencia() as $idCompetencia => $columna) {
            $filas = DB::table('estudiante as e')
                ->join('datos_ingreso as di', 'di.codigo_est', '=', 'e.codigo_est')
                ->join('matriz as m', 'm.codigo_est', '=', 'e.codigo_est')
                ->where('e.estado_nivelacion', 1)
                ->whereIn('di.id_programa', $idsProgramas)
                ->whereNotNull('m.' . $columna)
                ->where('m.' . $columna, '<=', 10.49)
                ->whereNotExists(function ($sub) use ($idCompetencia, $idPeriodo) {
                    $sub->select(DB::raw(1))
                        ->from('curso_detalle as cd2')
                        ->join('curso as c2', 'c2.id', '=', 'cd2.id_curso')
                        ->whereColumn('cd2.id_alumno', 'e.id')
                        ->whereColumn('c2.id_programa', 'di.id_programa')
                        ->where('c2.id_periodo', $idPeriodo)
                        ->where('c2.id_competencia', $idCompetencia);
                })
                ->groupBy('di.id_programa')
                ->select('di.id_programa', DB::raw('COUNT(DISTINCT e.id) as pendientes'))
                ->get();

            foreach ($filas as $fila) {
                $mapa[(int) $fila->id_programa . '-' . $idCompetencia] = (int) $fila->pendientes;
            }
        }

        return $mapa;
    }

    private function idsAlumnosPendientes(int $idPrograma, int $idCompetencia, int $idPeriodo): Collection
    {
        $columna = $this->columnasCompetencia()[$idCompetencia] ?? null;
        if (!$columna) {
            return collect();
        }

        return DB::table('estudiante as e')
            ->join('datos_ingreso as di', 'di.codigo_est', '=', 'e.codigo_est')
            ->join('matriz as m', 'm.codigo_est', '=', 'e.codigo_est')
            ->where('e.estado_nivelacion', 1)
            ->where('di.id_programa', $idPrograma)
            ->whereNotNull('m.' . $columna)
            ->where('m.' . $columna, '<=', 10.49)
            ->whereNotExists(function ($sub) use ($idPrograma, $idCompetencia, $idPeriodo) {
                $sub->select(DB::raw(1))
                    ->from('curso_detalle as cd2')
                    ->join('curso as c2', 'c2.id', '=', 'cd2.id_curso')
                    ->whereColumn('cd2.id_alumno', 'e.id')
                    ->where('c2.id_programa', $idPrograma)
                    ->where('c2.id_periodo', $idPeriodo)
                    ->where('c2.id_competencia', $idCompetencia);
            })
            ->distinct()
            ->pluck('e.id');
    }

    /**
     * Solo conserva las competencias que realmente tienen demanda pendiente.
     */
    private function filasConDemanda(Collection $filas, array $mapaDemanda): Collection
    {
        return $filas
            ->map(function ($fila) use ($mapaDemanda) {
                $clave = (int) $fila->id_programa . '-' . (int) $fila->id_competencia;
                $fila->pendientes = (int) ($mapaDemanda[$clave] ?? 0);
                return $fila;
            })
            ->filter(fn ($fila) => (int) $fila->pendientes > 0)
            ->values();
    }

    public function preview(Request $request)
    {
        $data = $this->validarScope($request);
        $idPeriodo = Periodo::activoId();

        if (!$idPeriodo) {
            return response()->json(['estado' => false, 'mensaje' => 'No existe un período activo.'], 422);
        }

        $programas = $this->programasQuery($data)->get();
        $idsProgramas = $programas->pluck('id');
        $filas = $this->filasCompetencias($data);
        $grupo = $data['grupo'];

        if ($programas->isEmpty() || $filas->isEmpty()) {
            return response()->json([
                'estado' => true,
                'datos' => [
                    'periodo' => DB::table('periodo')->where('id_periodo', $idPeriodo)->value('nombre'),
                    'id_periodo' => $idPeriodo,
                    'grupo' => $grupo,
                    'programas' => $programas->count(),
                    'competencias_programa' => $filas->count(),
                    'competencias_con_demanda' => 0,
                    'competencias_sin_demanda' => $filas->count(),
                    'cursos_necesarios' => 0,
                    'cursos_existentes' => 0,
                    'cursos_faltantes_estimados' => 0,
                    'cursos_ambiguos' => 0,
                    'cursos_inactivos' => 0,
                    'alumnos_activos' => 0,
                    'matriculas_potenciales' => 0,
                    'matriculas_ejecutables' => 0,
                    'detalle_demanda' => [],
                ],
            ]);
        }

        $mapaDemanda = $this->mapaDemandaPendiente($idsProgramas, $idPeriodo);
        $filasDemanda = $this->filasConDemanda($filas, $mapaDemanda);

        // Solo nos interesan los cursos del grupo elegido para las competencias con demanda.
        $cursosScope = DB::table('curso')
            ->where('id_periodo', $idPeriodo)
            ->where('grupo', $grupo)
            ->whereIn('id_programa', $idsProgramas)
            ->select('id', 'id_programa', 'id_competencia', 'estado')
            ->get()
            ->groupBy(fn ($c) => $c->id_programa . '-' . $c->id_competencia);

        $existentes = 0;
        $faltantes = 0;
        $ambiguos = 0;
        $inactivos = 0;
        $matriculasPotenciales = 0;
        $matriculasEjecutables = 0;
        $detalle = [];

        foreach ($filasDemanda as $fila) {
            $clave = $fila->id_programa . '-' . $fila->id_competencia;
            $pendientes = (int) $fila->pendientes;
            $matriculasPotenciales += $pendientes;
            $coincidencias = $cursosScope->get($clave, collect());

            $estadoCurso = 'faltante';
            $idCurso = null;

            if ($coincidencias->isEmpty()) {
                $faltantes++;
            } elseif ($coincidencias->count() > 1) {
                $ambiguos++;
                $estadoCurso = 'ambiguo';
            } else {
                $curso = $coincidencias->first();
                $idCurso = (int) $curso->id;
                $existentes++;

                if ((int) $curso->estado !== 1) {
                    $inactivos++;
                    $estadoCurso = 'inactivo';
                } else {
                    $estadoCurso = 'activo';
                    $matriculasEjecutables += $pendientes;
                }
            }

            $detalle[] = [
                'escuela' => $fila->escuela,
                'id_programa' => (int) $fila->id_programa,
                'programa' => $fila->programa,
                'id_competencia' => (int) $fila->id_competencia,
                'competencia' => $fila->competencia,
                'pendientes' => $pendientes,
                'estado_curso' => $estadoCurso,
                'id_curso' => $idCurso,
            ];
        }

        $alumnosActivos = DB::table('estudiante as e')
            ->join('datos_ingreso as di', 'di.codigo_est', '=', 'e.codigo_est')
            ->where('e.estado_nivelacion', 1)
            ->whereIn('di.id_programa', $idsProgramas)
            ->distinct('e.id')
            ->count('e.id');

        return response()->json([
            'estado' => true,
            'datos' => [
                'periodo' => DB::table('periodo')->where('id_periodo', $idPeriodo)->value('nombre'),
                'id_periodo' => $idPeriodo,
                'grupo' => $grupo,
                'programas' => $programas->count(),
                'competencias_programa' => $filas->count(),
                'competencias_con_demanda' => $filasDemanda->count(),
                'competencias_sin_demanda' => max(0, $filas->count() - $filasDemanda->count()),
                'cursos_necesarios' => $filasDemanda->count(),
                'cursos_existentes' => $existentes,
                'cursos_faltantes_estimados' => $faltantes,
                'cursos_ambiguos' => $ambiguos,
                'cursos_inactivos' => $inactivos,
                'alumnos_activos' => $alumnosActivos,
                'matriculas_potenciales' => $matriculasPotenciales,
                'matriculas_ejecutables' => $matriculasEjecutables,
                'detalle_demanda' => $detalle,
            ],
        ]);
    }

    public function crearCursos(Request $request)
    {
        $data = $this->validarScope($request);
        $idPeriodo = Periodo::activoId();

        if (!$idPeriodo) {
            return response()->json(['estado' => false, 'mensaje' => 'No existe un período activo.'], 422);
        }

        $programas = $this->programasQuery($data)->get();
        $filas = $this->filasCompetencias($data);

        if ($programas->isEmpty() || $filas->isEmpty()) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'No existen programas o competencias habilitadas para el ámbito seleccionado.',
            ], 422);
        }

        $mapaDemanda = $this->mapaDemandaPendiente($programas->pluck('id'), $idPeriodo);
        $filasDemanda = $this->filasConDemanda($filas, $mapaDemanda);

        if ($filasDemanda->isEmpty()) {
            return response()->json([
                'estado' => true,
                'tipo' => 'info',
                'titulo' => 'NO HAY CURSOS NECESARIOS',
                'mensaje' => 'No existen estudiantes activos pendientes de nivelar en el ámbito seleccionado. No se creó ningún curso.',
                'datos' => [
                    'creados' => 0,
                    'existentes' => 0,
                    'ambiguos' => 0,
                    'inactivos' => 0,
                    'competencias_con_demanda' => 0,
                ],
            ]);
        }

        $creados = 0;
        $existentes = 0;
        $ambiguos = 0;
        $inactivos = 0;
        $grupo = $data['grupo'];

        DB::transaction(function () use (
            $filasDemanda,
            $idPeriodo,
            $grupo,
            &$creados,
            &$existentes,
            &$ambiguos,
            &$inactivos
        ) {
            foreach ($filasDemanda as $fila) {
                $coincidencias = Curso::query()
                    ->where('id_programa', $fila->id_programa)
                    ->where('id_periodo', $idPeriodo)
                    ->where('id_competencia', $fila->id_competencia)
                    ->where('grupo', $grupo)
                    ->get();

                if ($coincidencias->count() > 1) {
                    $ambiguos++;
                    continue;
                }

                if ($coincidencias->count() === 1) {
                    $existentes++;
                    if ((int) $coincidencias->first()->estado !== 1) {
                        $inactivos++;
                    }
                    continue;
                }

                Curso::create([
                    'id_programa' => $fila->id_programa,
                    'id_periodo' => $idPeriodo,
                    'id_competencia' => $fila->id_competencia,
                    'grupo' => $grupo,
                    'nombre' => $fila->competencia,
                    'id_docente' => null,
                    'escuela' => $fila->escuela,
                    'estado' => 1,
                    'id_usuario' => auth()->id(),
                ]);

                $creados++;
            }
        });

        return response()->json([
            'estado' => true,
            'tipo' => ($ambiguos > 0 || $inactivos > 0) ? 'warn' : 'success',
            'titulo' => 'CREACIÓN MASIVA COMPLETADA',
            'mensaje' => "Cursos necesarios creados: {$creados}. Ya existentes: {$existentes}. Ambiguos: {$ambiguos}. Inactivos: {$inactivos}.",
            'datos' => [
                'creados' => $creados,
                'existentes' => $existentes,
                'ambiguos' => $ambiguos,
                'inactivos' => $inactivos,
                'competencias_con_demanda' => $filasDemanda->count(),
            ],
        ]);
    }

    public function matricular(Request $request)
    {
        $data = $this->validarScope($request);
        $idPeriodo = Periodo::activoId();

        if (!$idPeriodo) {
            return response()->json(['estado' => false, 'mensaje' => 'No existe un período activo.'], 422);
        }

        $programas = $this->programasQuery($data)->get();
        $filas = $this->filasCompetencias($data);

        if ($programas->isEmpty() || $filas->isEmpty()) {
            return response()->json(['estado' => false, 'mensaje' => 'No existen programas o competencias para el ámbito seleccionado.'], 422);
        }

        // MUY IMPORTANTE: la matrícula masiva solo exige cursos para las
        // competencias donde existe demanda pendiente real.
        $mapaDemanda = $this->mapaDemandaPendiente($programas->pluck('id'), $idPeriodo);
        $filasDemanda = $this->filasConDemanda($filas, $mapaDemanda);

        if ($filasDemanda->isEmpty()) {
            return response()->json([
                'estado' => true,
                'tipo' => 'info',
                'titulo' => 'SIN MATRÍCULAS PENDIENTES',
                'mensaje' => 'No existen estudiantes activos pendientes de matrícula para las competencias del ámbito seleccionado.',
                'datos' => [
                    'matriculados' => 0,
                    'procesados' => 0,
                    'sinCurso' => 0,
                    'ambiguos' => 0,
                    'inactivos' => 0,
                ],
            ]);
        }

        $cursosPorClave = DB::table('curso')
            ->where('id_periodo', $idPeriodo)
            ->where('grupo', $data['grupo'])
            ->whereIn('id_programa', $programas->pluck('id'))
            ->select('id', 'id_programa', 'id_competencia', 'estado')
            ->get()
            ->groupBy(fn ($c) => $c->id_programa . '-' . $c->id_competencia);

        $matriculados = 0;
        $sinCurso = 0;
        $ambiguos = 0;
        $inactivos = 0;
        $procesados = 0;

        // Validamos SOLO las competencias que tienen estudiantes pendientes.
        // Si una competencia tiene demanda = 0, no necesita curso y no bloquea.
        foreach ($filasDemanda as $fila) {
            $clave = $fila->id_programa . '-' . $fila->id_competencia;
            $coincidencias = $cursosPorClave->get($clave, collect());

            if ($coincidencias->isEmpty()) {
                $sinCurso++;
                continue;
            }

            if ($coincidencias->count() > 1) {
                $ambiguos++;
                continue;
            }

            if ((int) $coincidencias->first()->estado !== 1) {
                $inactivos++;
            }
        }

        if ($sinCurso > 0 || $ambiguos > 0 || $inactivos > 0) {
            return response()->json([
                'estado' => false,
                'mensaje' => "No se ejecutó ninguna matrícula. Solo se revisaron competencias con demanda. Corrija primero: {$sinCurso} cursos necesarios faltantes, {$ambiguos} ambiguos y {$inactivos} inactivos.",
                'datos' => compact('sinCurso', 'ambiguos', 'inactivos'),
            ], 422);
        }

        // Todo el ámbito con demanda está consistente: recién matriculamos.
        DB::transaction(function () use (
            $filasDemanda,
            $cursosPorClave,
            $idPeriodo,
            &$matriculados,
            &$procesados
        ) {
            foreach ($filasDemanda as $fila) {
                $clave = $fila->id_programa . '-' . $fila->id_competencia;
                $curso = $cursosPorClave->get($clave)->first();

                if ((int) $curso->estado !== 1) {
                    throw new \RuntimeException('Se detectó un curso inactivo durante la matrícula masiva. No se realizaron cambios.');
                }

                // Se vuelve a consultar justo antes de insertar para evitar usar
                // una lista antigua si otro usuario matriculó mientras tanto.
                $ids = $this->idsAlumnosPendientes(
                    (int) $fila->id_programa,
                    (int) $fila->id_competencia,
                    (int) $idPeriodo
                );

                foreach ($ids->chunk(500) as $chunk) {
                    $ahora = now();
                    $filasInsert = $chunk->map(fn ($idAlumno) => [
                        'id_curso' => $curso->id,
                        'id_alumno' => $idAlumno,
                        'created_at' => $ahora,
                        'updated_at' => $ahora,
                    ])->all();

                    if ($filasInsert) {
                        $matriculados += DB::table('curso_detalle')->insertOrIgnore($filasInsert);
                    }
                }

                $procesados++;
            }
        });

        return response()->json([
            'estado' => true,
            'tipo' => 'success',
            'titulo' => 'MATRÍCULA MASIVA COMPLETADA',
            'mensaje' => "Nuevas matrículas: {$matriculados}. Cursos con demanda procesados: {$procesados}.",
            'datos' => compact('matriculados', 'procesados', 'sinCurso', 'ambiguos', 'inactivos'),
        ]);
    }
}
