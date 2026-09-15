<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Docente;
use App\Models\CursoDetalle;
use App\Models\Curso;
use App\Models\Periodo;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AsignacionController extends Controller
{
    public function index()
    {
        return Inertia::render('Asignacion/index');
    }

    /**
     * Un superadministrador puede trabajar con cualquier programa.
     * Los demás usuarios solo pueden trabajar con programas de su escuela.
     */
    private function esSuperadmin(): bool
    {
        return (int) (auth()->user()->rol ?? -1) === 0;
    }

    private function programaPermitido(int $idPrograma)
    {
        $query = DB::table('programa')
            ->join('escuela', 'escuela.id', '=', 'programa.id_escuela')
            ->where('programa.id', $idPrograma)
            ->select(
                'programa.id',
                'programa.programa',
                'programa.id_escuela',
                'escuela.nombre as escuela'
            );

        if (!$this->esSuperadmin()) {
            $query->where('programa.id_escuela', auth()->user()->id_escuela);
        }

        return $query->first();
    }

    private function cursoPermitido(int $idCurso)
    {
        $query = DB::table('curso')
            ->join('programa', 'programa.id', '=', 'curso.id_programa')
            ->where('curso.id', $idCurso)
            ->select(
                'curso.*',
                'programa.id_escuela',
                'programa.programa'
            );

        if (!$this->esSuperadmin()) {
            $query->where('programa.id_escuela', auth()->user()->id_escuela);
        }

        return $query->first();
    }

    public function getCursos(Request $request)
    {
        $idPrograma = (int) $request->input('programa');

        if (!$idPrograma) {
            return response()->json([
                'estado' => true,
                'datos' => ['data' => []],
                'periodo_activo' => Periodo::activoId(),
                'periodos' => Periodo::select('id_periodo as value', 'nombre as label', 'estado')
                    ->orderByDesc('id_periodo')
                    ->get(),
            ], 200);
        }

        $programa = $this->programaPermitido($idPrograma);
        if (!$programa) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'El programa seleccionado no pertenece a su escuela profesional.'
            ], 403);
        }

        $idPeriodo = (int) ($request->input('periodo') ?: Periodo::activoId());
        if (!$idPeriodo) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'No existe un período activo configurado.'
            ], 422);
        }

        $res = Curso::select(
            'curso.id',
            'curso.nombre',
            'docente.id as id_docente',
            DB::raw("TRIM(CONCAT(COALESCE(docente.nombres,''),' ',COALESCE(docente.paterno,''),' ',COALESCE(docente.materno,''))) as docente"),
            'competencia.id as id_competencia',
            'competencia.nombre as competencia',
            'curso.grupo',
            'curso.id_programa',
            'programa.programa',
            'escuela.nombre as escuela',
            'curso.estado',
            'periodo.id_periodo',
            'periodo.nombre as periodo'
        )
            ->leftJoin('docente', 'docente.id', '=', 'curso.id_docente')
            ->join('competencia', 'competencia.id', '=', 'curso.id_competencia')
            ->join('programa', 'programa.id', '=', 'curso.id_programa')
            ->join('escuela', 'escuela.id', '=', 'programa.id_escuela')
            ->join('periodo', 'periodo.id_periodo', '=', 'curso.id_periodo')
            // El curso pertenece al programa, no al usuario que lo creó.
            ->where('curso.id_programa', $idPrograma)
            ->where('programa.id_escuela', $programa->id_escuela)
            ->where('curso.id_periodo', $idPeriodo)
            ->when($request->filled('competencia'), function ($query) use ($request) {
                $query->where('curso.id_competencia', (int) $request->competencia);
            })
            ->when($request->filled('term'), function ($query) use ($request) {
                $term = trim((string) $request->term);
                $query->where(function ($q) use ($term) {
                    $q->where('curso.nombre', 'LIKE', '%' . $term . '%')
                        ->orWhere('competencia.nombre', 'LIKE', '%' . $term . '%')
                        ->orWhere('docente.nombres', 'LIKE', '%' . $term . '%')
                        ->orWhere('docente.paterno', 'LIKE', '%' . $term . '%');
                });
            })
            ->orderByDesc('curso.id')
            ->paginate(100);

        return response()->json([
            'estado' => true,
            'datos' => $res,
            'periodo_activo' => Periodo::activoId(),
            'periodos' => Periodo::select('id_periodo as value', 'nombre as label', 'estado')
                ->orderByDesc('id_periodo')
                ->get(),
        ], 200);
    }

    public function getDetalleCurso(Request $request)
    {
        $cursoSeleccionado = $this->cursoPermitido((int) $request->curso);

        if (!$cursoSeleccionado) {
            return response()->json([
                'estado' => false,
                'datos' => [],
                'registrados' => [],
                'mensaje' => 'Curso no encontrado o fuera de su escuela profesional.'
            ], 404);
        }

        $columnasCompetencia = [
            1 => 'C1_R', 2 => 'C2_R', 3 => 'C3_R', 4 => 'C4_R',
            5 => 'C5_R', 6 => 'C6_R', 7 => 'C7_R', 8 => 'C8_R',
            9 => 'C9_R', 10 => 'C10_R', 11 => 'C11_R',
        ];

        $columnaNotaActual = $columnasCompetencia[(int) $cursoSeleccionado->id_competencia] ?? null;

        if (!$columnaNotaActual) {
            return response()->json([
                'estado' => false,
                'datos' => [],
                'registrados' => [],
                'mensaje' => 'La competencia del curso no tiene una columna de nota configurada.'
            ], 422);
        }

        $base = CursoDetalle::query()
            ->join('curso', 'curso.id', '=', 'curso_detalle.id_curso')
            ->join('estudiante', 'estudiante.id', '=', 'curso_detalle.id_alumno')
            ->join('datos_ingreso', 'datos_ingreso.codigo_est', '=', 'estudiante.codigo_est')
            ->join('programa', 'programa.id', '=', 'datos_ingreso.id_programa')
            ->join('matriz', 'matriz.codigo_est', '=', 'estudiante.codigo_est')
            ->where('curso.id', (int) $request->curso)
            ->when($request->filled('term'), function ($query) use ($request) {
                $term = trim((string) $request->term);
                $query->where(function ($q) use ($term) {
                    $q->where('estudiante.codigo_est', 'LIKE', '%' . $term . '%')
                        ->orWhere('estudiante.nombres', 'LIKE', '%' . $term . '%')
                        ->orWhere('estudiante.paterno', 'LIKE', '%' . $term . '%')
                        ->orWhere('estudiante.materno', 'LIKE', '%' . $term . '%');
                });
            });

        $res = (clone $base)
            ->select(
                'estudiante.codigo_est',
                'datos_ingreso.semestre',
                'programa.programa',
                'estudiante.nombres',
                'estudiante.paterno',
                'estudiante.materno',
                'curso.nombre as curso',
                DB::raw('matriz.' . $columnaNotaActual . ' as nota_actual'),
                'curso_detalle.nota'
            )
            ->orderBy('estudiante.paterno')
            ->paginate(200);

        $registrados = (clone $base)
            ->select(
                'estudiante.id',
                'estudiante.codigo_est',
                'programa.programa',
                DB::raw('matriz.' . $columnaNotaActual . ' as nota_actual'),
                'estudiante.nombres',
                'estudiante.paterno',
                'estudiante.materno'
            )
            ->orderBy('estudiante.paterno')
            ->paginate(200);

        return response()->json([
            'estado' => true,
            'datos' => $res,
            'registrados' => $registrados,
        ], 200);
    }

    public function save(Request $request)
    {
        $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'id_competencia' => ['required', 'integer'],
            'id_docente' => ['nullable', 'integer'],
            'grupo' => ['required', 'string', 'max:1'],
            'id_programa' => ['required', 'integer'],
        ]);

        $programa = $this->programaPermitido((int) $request->id_programa);
        if (!$programa) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'El programa seleccionado no pertenece a su escuela profesional.'
            ], 403);
        }

        $competenciaValida = DB::table('competencia_programa')
            ->where('id_programa', $programa->id)
            ->where('id_competencia', (int) $request->id_competencia)
            ->where('estado', 1)
            ->exists();

        if (!$competenciaValida) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'La competencia seleccionada no está habilitada para este programa.'
            ], 422);
        }

        if (!$request->id) {
            $idPeriodo = Periodo::activoId();
            if (!$idPeriodo) {
                return response()->json([
                    'estado' => false,
                    'mensaje' => 'No existe un período activo configurado.'
                ], 422);
            }

            $curso = Curso::create([
                'nombre' => $request->nombre,
                'id_competencia' => $request->id_competencia,
                'id_docente' => $request->id_docente ?: null,
                'grupo' => $request->grupo,
                // Campo heredado: se mantiene sincronizado, pero ya no controla el acceso.
                'escuela' => $programa->escuela,
                'estado' => $request->boolean('estado'),
                'id_programa' => $programa->id,
                'id_usuario' => auth()->id(), // auditoría: quién lo creó
                'id_periodo' => $idPeriodo,
            ]);

            return response()->json([
                'tipo' => 'success',
                'titulo' => 'REGISTRO NUEVO',
                'mensaje' => 'Curso de nivelación ' . $curso->nombre . ' registrado con éxito',
                'estado' => true,
                'datos' => $curso,
            ], 200);
        }

        $permitido = $this->cursoPermitido((int) $request->id);
        if (!$permitido) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'No tiene permiso para modificar este curso.'
            ], 403);
        }

        $curso = Curso::findOrFail((int) $request->id);

        if (!$this->esSuperadmin() && (int) $curso->id_periodo !== (int) Periodo::activoId()) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'Los cursos de períodos anteriores son solo de consulta.',
            ], 422);
        }

        $curso->nombre = $request->nombre;
        $curso->id_competencia = $request->id_competencia;
        $curso->id_docente = $request->id_docente ?: null;
        $curso->grupo = $request->grupo;
        $curso->escuela = $programa->escuela;
        $curso->estado = $request->boolean('estado');
        $curso->id_programa = $programa->id;
        $curso->save();

        return response()->json([
            'tipo' => 'info',
            'titulo' => 'REGISTRO MODIFICADO',
            'mensaje' => 'Curso ' . $curso->nombre . ' modificado correctamente.',
            'estado' => true,
            'datos' => $curso,
        ], 200);
    }

    public function getDocentesXcompetencia(Request $request)
    {
        $competencia = $request->competencia;

        $res = Docente::select(
            'docente.id',
            DB::raw("TRIM(CONCAT(COALESCE(docente.nombres,''),' ',COALESCE(docente.paterno,''),' ',COALESCE(docente.materno,''))) as nombres")
        )
            ->join('docente_competencia', 'docente.id', '=', 'docente_competencia.id_docente')
            ->where('docente.estado', 1)
            ->where('docente_competencia.id_competencia', $competencia)
            ->when($request->filled('term'), function ($query) use ($request) {
                $term = trim((string) $request->term);
                $query->where(function ($q) use ($term) {
                    $q->where('docente.nombres', 'LIKE', '%' . $term . '%')
                        ->orWhere('docente.paterno', 'LIKE', '%' . $term . '%')
                        ->orWhere('docente.materno', 'LIKE', '%' . $term . '%')
                        ->orWhere('docente.nro_doc', 'LIKE', '%' . $term . '%');
                });
            })
            ->orderBy('docente.paterno')
            ->paginate(1000);

        return response()->json([
            'estado' => true,
            'datos' => $res,
        ], 200);
    }

    public function asignarCursoNivelacion(Request $request)
    {
        $curso = $this->cursoPermitido((int) $request->curso);
        if (!$curso) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'No tiene permiso para modificar este curso.'
            ], 403);
        }

        if ((int) $curso->id_periodo !== (int) Periodo::activoId()) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'Solo se pueden modificar alumnos del período activo.'
            ], 422);
        }

        try {
            DB::transaction(function () use ($request, $curso) {
                foreach ((array) $request->diferencia as $alumno) {
                    $idAlumno = (int) ($alumno['id'] ?? 0);
                    if (!$idAlumno) {
                        continue;
                    }

                    $pertenece = DB::table('estudiante')
                        ->join('datos_ingreso', 'datos_ingreso.codigo_est', '=', 'estudiante.codigo_est')
                        ->where('estudiante.id', $idAlumno)
                        ->where('datos_ingreso.id_programa', $curso->id_programa)
                        ->exists();

                    if (!$pertenece) {
                        throw new \RuntimeException('Se intentó asignar un alumno que no pertenece al programa del curso.');
                    }

                    CursoDetalle::firstOrCreate([
                        'id_curso' => $curso->id,
                        'id_alumno' => $idAlumno,
                    ]);
                }

                foreach ((array) $request->diferencia2 as $alumno) {
                    $idAlumno = (int) ($alumno['id'] ?? 0);
                    if ($idAlumno) {
                        CursoDetalle::where('id_curso', $curso->id)
                            ->where('id_alumno', $idAlumno)
                            ->delete();
                    }
                }
            });
        } catch (\Throwable $e) {
            return response()->json([
                'estado' => false,
                'mensaje' => $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'tipo' => 'success',
            'titulo' => 'REGISTROS ACTUALIZADOS',
            'mensaje' => 'Alumnos asignados correctamente.',
            'estado' => true,
        ], 200);
    }

    // Se conservan por compatibilidad con otras llamadas del sistema.
    public function asignarCurso($id_curso, $id_alumno)
    {
        $curso = $this->cursoPermitido((int) $id_curso);
        if (!$curso) {
            abort(403, 'No tiene permiso para modificar este curso.');
        }

        if ((int) $curso->id_periodo !== (int) Periodo::activoId()) {
            abort(403, 'No se puede matricular en un curso que no pertenece al período activo.');
        }

        CursoDetalle::firstOrCreate([
            'id_curso' => $id_curso,
            'id_alumno' => $id_alumno,
        ]);
    }

    public function eliminarCurso($id_curso, $id_alumno)
    {
        $curso = $this->cursoPermitido((int) $id_curso);
        if (!$curso) {
            abort(403, 'No tiene permiso para modificar este curso.');
        }

        $cursoDetalle = CursoDetalle::where('id_curso', $id_curso)
            ->where('id_alumno', $id_alumno)
            ->first();

        if (!$cursoDetalle) {
            return response()->json(['message' => 'Registro no encontrado'], 404);
        }

        $cursoDetalle->delete();
        return response()->json(['message' => 'Registro eliminado con éxito']);
    }

    public function pdf($curso)
    {
        $permitido = $this->cursoPermitido((int) $curso);
        if (!$permitido) {
            abort(403, 'No tiene permiso para consultar este curso.');
        }

        $res = Curso::select(
            'curso.id AS id_curso',
            'curso.nombre AS curso',
            'periodo.nombre AS ciclo',
            'competencia.id AS id_competencia',
            'competencia.nombre AS competencia',
            'docente.nro_doc AS dni_docente',
            'docente.nombres AS nombre',
            'docente.paterno as paterno',
            'docente.materno as materno',
            'curso.grupo',
            'escuela.nombre as escuela',
            'programa.programa',
            'escuela.filial'
        )
            ->join('competencia', 'competencia.id', '=', 'curso.id_competencia')
            ->leftJoin('docente', 'curso.id_docente', '=', 'docente.id')
            ->join('programa', 'curso.id_programa', '=', 'programa.id')
            ->join('escuela', 'programa.id_escuela', '=', 'escuela.id')
            ->join('periodo', 'periodo.id_periodo', '=', 'curso.id_periodo')
            ->where('curso.id', '=', $curso)
            ->get();

        if ($res->isEmpty()) {
            abort(404, 'Curso no encontrado.');
        }

        $estudiantes = CursoDetalle::select(
            'estudiante.codigo_est',
            'datos_ingreso.semestre',
            'estudiante.nombres',
            'estudiante.paterno',
            'estudiante.materno',
            'curso_detalle.nota',
            'curso_detalle.condicion'
        )
            ->join('estudiante', 'curso_detalle.id_alumno', '=', 'estudiante.id')
            ->join('datos_ingreso', 'datos_ingreso.codigo_est', '=', 'estudiante.codigo_est')
            ->where('curso_detalle.id_curso', '=', $curso)
            ->get();

        $data = $res[0];
        $pdf = Pdf::loadView('RepCursoPDF/indexcursos', compact('data', 'estudiantes'));

        return $pdf->download('Reporte.pdf');
    }
}
