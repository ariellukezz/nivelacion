<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Curso;
use App\Models\CursoDetalle;
use App\Models\Periodo;

class NotaCoordinadorController extends Controller
{
public function getCompetencias(Request $request)
{
    $idEscuela = auth()->user()->id_escuela;

    $periodoActivo = Periodo::where('estado', 'activo')
        ->first();

    if (!$periodoActivo) {
        return response()->json([
            'estado' => false,
            'datos' => [],
            'mensaje' => 'No existe un periodo activo.'
        ]);
    }

    $competencias = DB::table('curso')
        ->join(
            'competencia',
            'competencia.id',
            '=',
            'curso.id_competencia'
        )
        ->join(
            'programa',
            'programa.id',
            '=',
            'curso.id_programa'
        )
        ->where(
            'programa.id_escuela',
            $idEscuela
        )
        ->where(
            'curso.id_periodo',
            $periodoActivo->id_periodo
        )
        ->where(
            'curso.estado',
            1
        )
        ->select(
            'competencia.id as value',
            'competencia.nombre as label'
        )
        ->distinct()
        ->orderBy(
            'competencia.nombre'
        )
        ->get();

    return response()->json([
        'estado' => true,
        'datos' => $competencias
    ]);
}

public function getCursos(Request $request)
{
    $request->validate([
        'competencia' => 'required|integer',
    ]);

    $idEscuela = auth()->user()->id_escuela;

    $periodoActivo = Periodo::where(
        'estado',
        'activo'
    )->first();

    if (!$periodoActivo) {
        return response()->json([
            'estado' => false,
            'datos' => []
        ]);
    }

    $cursos = DB::table('curso')
        ->join(
            'competencia',
            'competencia.id',
            '=',
            'curso.id_competencia'
        )
        ->join(
            'programa',
            'programa.id',
            '=',
            'curso.id_programa'
        )
        ->leftJoin(
            'docente',
            'docente.id',
            '=',
            'curso.id_docente'
        )
        ->where(
            'programa.id_escuela',
            $idEscuela
        )
        ->where(
            'curso.id_competencia',
            $request->competencia
        )
        ->where(
            'curso.id_periodo',
            $periodoActivo->id_periodo
        )
        ->where(
            'curso.estado',
            1
        )
        ->select(
            'curso.id',
            'curso.nombre',
            'curso.grupo',

            'competencia.id as id_competencia',
            'competencia.nombre as competencia',

            'programa.id as id_programa',
            'programa.programa',

            'docente.id as id_docente',

            DB::raw("
                TRIM(
                    CONCAT(
                        COALESCE(docente.nombres, ''),
                        ' ',
                        COALESCE(docente.paterno, ''),
                        ' ',
                        COALESCE(docente.materno, '')
                    )
                ) as docente
            ")
        )
        ->orderBy('programa.programa')
        ->orderBy('curso.grupo')
        ->get();

    return response()->json([
        'estado' => true,
        'datos' => $cursos
    ]);
}
public function getAlumnos(Request $request)
{
    $request->validate([
        'curso' => 'required|integer',
    ]);

    $idEscuela = auth()->user()->id_escuela;

    $periodoActivo = Periodo::where(
        'estado',
        'activo'
    )->firstOrFail();

    $curso = DB::table('curso')
        ->join(
            'programa',
            'programa.id',
            '=',
            'curso.id_programa'
        )
        ->where(
            'curso.id',
            $request->curso
        )
        ->where(
            'programa.id_escuela',
            $idEscuela
        )
        ->where(
            'curso.id_periodo',
            $periodoActivo->id_periodo
        )
        ->select(
            'curso.*',
            'programa.programa'
        )
        ->first();

    if (!$curso) {
        return response()->json([
            'estado' => false,
            'mensaje' => 'Curso no autorizado.'
        ], 403);
    }

    $campoMatriz =
        'C' . intval($curso->id_competencia) . '_R';

    $alumnos = DB::table('curso_detalle as cd')
        ->join(
            'estudiante as e',
            'e.id',
            '=',
            'cd.id_alumno'
        )
        ->leftJoin(
            'matriz as m',
            'm.codigo_est',
            '=',
            'e.codigo_est'
        )
        ->where(
            'cd.id_curso',
            $curso->id
        )
        ->select(
            'cd.id as id_detalle',
            'cd.id_curso',
            'cd.id_alumno',

            'e.codigo_est',
            'e.nombres',
            'e.paterno',
            'e.materno',

            DB::raw("'" . addslashes($curso->programa) . "' as programa"),

            DB::raw(
                "m.$campoMatriz as nota_matriz"
            ),

            'cd.nota as nota_actual',
            'cd.edicion_nota',
            'cd.condicion',
            'cd.fecha'
        )
        ->orderBy('e.paterno')
        ->orderBy('e.materno')
        ->get();

    return response()->json([
        'estado' => true,
        'curso' => $curso,
        'datos' => $alumnos
    ]);
}

public function updateNota(Request $request)
{
    $request->validate([
        'id_detalle' => 'required|integer',
        'nota' => 'required|numeric|min:0|max:20',
        'observacion' => 'required|string|max:255',
    ]);

    $idEscuela = auth()->user()->id_escuela;

    $periodoActivo = Periodo::where(
        'estado',
        'activo'
    )->firstOrFail();

    $registro = DB::table('curso_detalle as cd')
        ->join(
            'curso as c',
            'c.id',
            '=',
            'cd.id_curso'
        )
        ->join(
            'programa as p',
            'p.id',
            '=',
            'c.id_programa'
        )
        ->where(
            'cd.id',
            $request->id_detalle
        )
        ->where(
            'p.id_escuela',
            $idEscuela
        )
        ->where(
            'c.id_periodo',
            $periodoActivo->id_periodo
        )
        ->select(
            'cd.id'
        )
        ->first();

    if (!$registro) {
        return response()->json([
            'estado' => false,
            'tipo' => 'error',
            'titulo' => 'No autorizado',
            'mensaje' => 'No puede modificar la nota de este estudiante.'
        ], 403);
    }

    $detalle = CursoDetalle::findOrFail(
        $registro->id
    );

    $detalle->nota =
        $request->nota;

    $detalle->condicion =
        floatval($request->nota) >= 10.50
            ? 1
            : 0;

    $detalle->edicion_nota =
        trim($request->observacion);

    $detalle->fecha =
        now()->toDateString();

    $detalle->save();

    return response()->json([
        'estado' => true,
        'tipo' => 'success',
        'titulo' => 'Nota actualizada',
        'mensaje' => 'La nota fue registrada correctamente.'
    ]);
}
}
