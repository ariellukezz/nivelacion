<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use DB;


class TeController extends Controller {

    public function getTest(){

      $escuela = DB::select("SELECT nombre from escuela where id = '" .auth()->user()->id_escuela ."';" );

      $competencias = DB::select("SELECT DISTINCT curso.id_competencia
      FROM curso
      WHERE curso.escuela = '".$escuela[0]->nombre."'
      ORDER BY curso.id_competencia ASC");


    //   SELECT estudiante.id as estudiante, estudiante.dni, estudiante.nombres, curso_detalle.nota,
    //     estudiante.paterno, estudiante.materno, programa.programa, datos_ingreso.semestre AS semestre
    //     FROM curso
    //     JOIN curso_detalle ON curso.id = curso_detalle.id_curso
    //     JOIN estudiante ON estudiante.id = curso_detalle.id_alumno
    //     JOIN datos_ingreso ON estudiante.dni = datos_ingreso.dni
    //     JOIN programa ON datos_ingreso.id_programa=programa.id
    //     WHERE curso.escuela = '".$escuela[0]->nombre

        $alumnos = [];


        foreach ($competencias as $competencia) {
        //bdhh $res = DB::select("SELECT estudiante.id as estudiante, estudiante.dni, estudiante.nombres, curso_detalle.nota,
        $res = DB::select("SELECT estudiante.id as estudiante, estudiante.codigo_est, estudiante.nombres, curso_detalle.nota,
        estudiante.paterno, estudiante.materno, programa.programa, datos_ingreso.semestre AS semestre, escuela.filial
        FROM curso
        JOIN curso_detalle ON curso.id = curso_detalle.id_curso
        JOIN estudiante ON estudiante.id = curso_detalle.id_alumno
        JOIN datos_ingreso ON estudiante.codigo_est = datos_ingreso.codigo_est
        JOIN programa ON datos_ingreso.id_programa=programa.id
        JOIN escuela ON programa.id_escuela = escuela.id AND escuela.nombre = curso.escuela
        JOIN periodo ON curso.id_periodo = periodo.id_periodo
        WHERE escuela.id = ".auth()->user()->id_escuela.
        " AND curso.id_competencia = :competencia
        AND periodo.estado = 'activo'
        ORDER BY programa.programa ASC, estudiante.paterno ASC;", ['competencia' => $competencia->id_competencia]);

        foreach ($res as $row) {
          $id = $row->estudiante;
          $nota = $row->nota;

          if (!isset($alumnos[$id])) {
              $alumnos[$id] = [
              'id_estudiante' => $row->estudiante,
              'nombre' => $row->nombres,
              'programa' => $row->programa,
              'semestre' => $row->semestre,
              'filial' => $row->filial,
              'codigo_est' => $row->codigo_est,
             //bdhh 'dni' => $row->dni,
              'paterno' => $row->paterno,
              'materno' => $row->materno,
              'notas' => [],
              ];
          }
          $alumnos[$id]['notas'][] = [
            'nota' => $nota,
            'competencia' => $competencia->id_competencia,
            ];
          }
        }

        $alumnos = array_values($alumnos);

        $this->response['estado'] = true;
        $this->response['datos'] = $alumnos;
        $this->response['competencias'] = $competencias;

      return response()->json($this->response, 200);

    }

public function getIngresantes()
{
    // ID de la escuela del usuario autenticado
    $idEscuela = auth()->user()->id_escuela;

    // Obtener el periodo ACTIVO
    $periodoActivo = DB::select("
        SELECT id_periodo, nombre
        FROM periodo
        WHERE estado = 'activo'
        ORDER BY id_periodo DESC
        LIMIT 1
    ");

    if (empty($periodoActivo)) {
        return response()->json([
            'estado' => false,
            'mensaje' => 'No hay periodo activo.'
        ], 404);
    }

    $ingresantes = DB::select("
        SELECT
            d.dni_ingr,
            d.primer_apellido,
            d.segundo_apellido,
            d.nombres_ingr,
            d.sexo,
            d.email,
            d.celular_ingre,
            p.programa,
            p.id AS id_programa,
            d.mod_ingr,
            d.id_periodo,
            d.i_C1_R,
            d.i_C2_R,
            d.i_C3_R,
            d.i_C4_R,
            d.i_C5_R,
            d.i_C6_R,
            d.i_C7_R,
            d.i_C8_R,
            d.i_C9_R,
            d.i_C10_R,
            d.i_C11_R
        FROM data_ingresante d

        INNER JOIN programa p
            ON d.id_niv = p.id
        INNER JOIN escuela e
            ON p.id_escuela = e.id
        WHERE e.id = ?
          AND d.id_periodo = ?
        ORDER BY
            d.primer_apellido ASC,
            d.segundo_apellido ASC", [
        $idEscuela,
        $periodoActivo[0]->id_periodo
    ]);

    return response()->json([
        'estado'         => true,
        'datos'          => $ingresantes,
        'periodo_actual' => $periodoActivo[0]->nombre
    ], 200);
}



public function getReprobadosNivelacion()
{
    $idEscuela = (int) auth()->user()->id_escuela;

    /*
    |--------------------------------------------------------------------------
    | Periodo actual y periodo anterior
    |--------------------------------------------------------------------------
    | Se toma como actual el periodo marcado ACTIVO. El anterior es el último
    | periodo con id menor al actual. De esta forma no dependemos de id - 1.
    */
    $periodoActual = DB::table('periodo')
        ->where('estado', 'activo')
        ->orderByDesc('id_periodo')
        ->first();

    if (!$periodoActual) {
        return response()->json([
            'estado'  => false,
            'mensaje' => 'No existe un periodo activo.',
            'datos'   => [],
        ], 422);
    }

    $periodoAnterior = DB::table('periodo')
        ->where('id_periodo', '<', $periodoActual->id_periodo)
        ->orderByDesc('id_periodo')
        ->first();

    if (!$periodoAnterior) {
        return response()->json([
            'estado'  => false,
            'mensaje' => 'No existe un periodo anterior para realizar la comparación.',
            'datos'   => [],
        ], 422);
    }

    $escuela = DB::table('escuela')
        ->where('id', $idEscuela)
        ->first();

    if (!$escuela) {
        return response()->json([
            'estado'  => false,
            'mensaje' => 'No se encontró la escuela del coordinador.',
            'datos'   => [],
        ], 404);
    }

    /*
    |--------------------------------------------------------------------------
    | Competencias que corresponden a cada programa de la escuela
    |--------------------------------------------------------------------------
    | Es la misma relación usada en otros módulos (competencia_programa).
    */
    $relacionesCompetenciaPrograma = DB::table('competencia_programa as cp')
        ->join('programa as p', 'p.id', '=', 'cp.id_programa')
        ->where('p.id_escuela', $idEscuela)
        ->select('cp.id_programa', 'cp.id_competencia')
        ->distinct()
        ->orderBy('cp.id_competencia')
        ->get();

    $competenciasPorPrograma = [];
    $competenciasGlobales = [];

    foreach ($relacionesCompetenciaPrograma as $relacion) {
        $idPrograma = (int) $relacion->id_programa;
        $idCompetencia = (int) $relacion->id_competencia;

        if ($idCompetencia < 1 || $idCompetencia > 11) {
            continue;
        }

        $competenciasPorPrograma[$idPrograma][] = $idCompetencia;
        $competenciasGlobales[$idCompetencia] = $idCompetencia;
    }

    sort($competenciasGlobales);

    /*
    |--------------------------------------------------------------------------
    | 1) Alumnos que tienen registro en MATRIZ
    |--------------------------------------------------------------------------
    | IMPORTANTE: esta consulta NO depende de curso_detalle. Por eso un alumno
    | con nota solamente en matriz también será considerado.
    */
    $alumnosMatriz = DB::table('matriz as m')
        ->join('estudiante as e', 'e.codigo_est', '=', 'm.codigo_est')
        ->join('datos_ingreso as di', 'di.codigo_est', '=', 'e.codigo_est')
        ->join('programa as p', 'p.id', '=', 'di.id_programa')
        ->join('escuela as esc', 'esc.id', '=', 'p.id_escuela')
        ->where('esc.id', $idEscuela)
        ->select(
            'e.id as estudiante',
            'e.codigo_est',
            'e.nombres',
            'e.paterno',
            'e.materno',
            'e.email',
            'e.telefono',
            'p.id as id_programa',
            'p.programa',
            'di.semestre',
            'esc.filial',
            'm.C1_R',
            'm.C2_R',
            'm.C3_R',
            'm.C4_R',
            'm.C5_R',
            'm.C6_R',
            'm.C7_R',
            'm.C8_R',
            'm.C9_R',
            'm.C10_R',
            'm.C11_R'
        )
        ->distinct()
        ->get();

    /*
    |--------------------------------------------------------------------------
    | 2) Notas de NIVELACIÓN en curso_detalle del periodo anterior
    |--------------------------------------------------------------------------
    | No hacemos INNER JOIN desde curso_detalle para armar todo el reporte.
    | Solo lo usamos como segunda fuente de nota.
    */
    $detallesNivelacion = DB::table('curso_detalle as cd')
        ->join('curso as c', 'c.id', '=', 'cd.id_curso')
        ->join('estudiante as e', 'e.id', '=', 'cd.id_alumno')
        ->join('datos_ingreso as di', 'di.codigo_est', '=', 'e.codigo_est')
        ->join('programa as p', 'p.id', '=', 'di.id_programa')
        ->join('escuela as esc', 'esc.id', '=', 'p.id_escuela')
        ->where('esc.id', $idEscuela)
        ->where('c.id_periodo', $periodoAnterior->id_periodo)
        ->where('c.escuela', $escuela->nombre)
        ->select(
            'e.id as estudiante',
            'e.codigo_est',
            'e.nombres',
            'e.paterno',
            'e.materno',
            'e.email',
            'e.telefono',
            'p.id as id_programa',
            'p.programa',
            'di.semestre',
            'esc.filial',
            'c.id_competencia',
            'cd.nota as nota_nivelacion'
        )
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Construir un único mapa de alumnos tomando las DOS fuentes
    |--------------------------------------------------------------------------
    */
    $alumnos = [];

    // Primero: alumnos que existen en matriz.
    foreach ($alumnosMatriz as $row) {
        $id = (int) $row->estudiante;

        if (!isset($alumnos[$id])) {
            $alumnos[$id] = [
                'id_estudiante' => $id,
                'nombre'        => $row->nombres,
                'programa'      => $row->programa,
                'id_programa'   => (int) $row->id_programa,
                'semestre'      => $row->semestre,
                'filial'        => $row->filial,
                'codigo_est'    => $row->codigo_est,
                'paterno'       => $row->paterno,
                'materno'       => $row->materno,
                'email'         => $row->email,
                'telefono'      => $row->telefono,
                '_matriz'       => [],
                '_nivelacion'   => [],
                'notas'         => [],
            ];
        }

        for ($i = 1; $i <= 11; $i++) {
            $campo = 'C' . $i . '_R';
            $valor = $row->{$campo};

            if ($valor !== null && $valor !== '' && is_numeric($valor)) {
                $nota = (float) $valor;

                // Si por datos duplicados apareciera más de una fila de matriz,
                // también aquí conservamos la nota mayor.
                if (
                    !isset($alumnos[$id]['_matriz'][$i]) ||
                    $nota > $alumnos[$id]['_matriz'][$i]
                ) {
                    $alumnos[$id]['_matriz'][$i] = $nota;
                }
            }
        }
    }

    // Segundo: alumnos/notas que existen en curso_detalle.
    foreach ($detallesNivelacion as $row) {
        $id = (int) $row->estudiante;
        $idCompetencia = (int) $row->id_competencia;

        if ($idCompetencia < 1 || $idCompetencia > 11) {
            continue;
        }

        // Si no estaba en matriz, igual debe existir en el reporte porque
        // curso_detalle es la otra fuente válida.
        if (!isset($alumnos[$id])) {
            $alumnos[$id] = [
                'id_estudiante' => $id,
                'nombre'        => $row->nombres,
                'programa'      => $row->programa,
                'id_programa'   => (int) $row->id_programa,
                'semestre'      => $row->semestre,
                'filial'        => $row->filial,
                'codigo_est'    => $row->codigo_est,
                'paterno'       => $row->paterno,
                'materno'       => $row->materno,
                'email'         => $row->email,
                'telefono'      => $row->telefono,
                '_matriz'       => [],
                '_nivelacion'   => [],
                'notas'         => [],
            ];
        }

        $valor = $row->nota_nivelacion;

        if ($valor !== null && $valor !== '' && is_numeric($valor)) {
            $nota = (float) $valor;

            if (
                !isset($alumnos[$id]['_nivelacion'][$idCompetencia]) ||
                $nota > $alumnos[$id]['_nivelacion'][$idCompetencia]
            ) {
                $alumnos[$id]['_nivelacion'][$idCompetencia] = $nota;
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Comparar MATRIZ vs CURSO_DETALLE y conservar SIEMPRE la nota mayor
    |--------------------------------------------------------------------------
    */
    foreach ($alumnos as $id => &$alumno) {
        $idPrograma = (int) $alumno['id_programa'];
        $competenciasAlumno = $competenciasPorPrograma[$idPrograma] ?? [];

        // Si por algún dato histórico no existe competencia_programa,
        // no perdemos una nota real: usamos las competencias encontradas
        // en cualquiera de las dos fuentes para ese alumno.
        if (empty($competenciasAlumno)) {
            $competenciasAlumno = array_values(array_unique(array_merge(
                array_keys($alumno['_matriz']),
                array_keys($alumno['_nivelacion'])
            )));
            sort($competenciasAlumno);
        }

        foreach ($competenciasAlumno as $idCompetencia) {
            $idCompetencia = (int) $idCompetencia;

            $notaMatriz = $alumno['_matriz'][$idCompetencia] ?? null;
            $notaNivelacion = $alumno['_nivelacion'][$idCompetencia] ?? null;

            // Si no hay ninguna nota numérica en ninguna de las dos tablas,
            // simplemente queda -- en la pantalla; no lo consideramos
            // desaprobado por ausencia de datos.
            if ($notaMatriz === null && $notaNivelacion === null) {
                continue;
            }

            if ($notaMatriz === null) {
                $notaFinal = $notaNivelacion;
            } elseif ($notaNivelacion === null) {
                $notaFinal = $notaMatriz;
            } else {
                $notaFinal = max($notaMatriz, $notaNivelacion);
            }

            $alumno['notas'][] = [
                'competencia'     => $idCompetencia,
                'nota'            => $notaFinal,
                'nota_matriz'     => $notaMatriz,
                'nota_nivelacion' => $notaNivelacion,
            ];
        }

        unset($alumno['_matriz'], $alumno['_nivelacion']);
    }
    unset($alumno);

    /*
    |--------------------------------------------------------------------------
    | Mostrar solamente los que aún tengan al menos una competencia desaprobada
    |--------------------------------------------------------------------------
    | Regla del sistema: 10.50 o más = aprobado.
    */
    $alumnos = array_values(array_filter($alumnos, function ($alumno) {
        foreach ($alumno['notas'] as $nota) {
            if (is_numeric($nota['nota']) && (float) $nota['nota'] < 10.50) {
                return true;
            }
        }

        return false;
    }));

    // Ordenar para mantener el reporte legible.
    usort($alumnos, function ($a, $b) {
        $programa = strcmp((string) $a['programa'], (string) $b['programa']);
        if ($programa !== 0) {
            return $programa;
        }

        return strcmp((string) $a['paterno'], (string) $b['paterno']);
    });

    $competenciasResponse = array_map(function ($idCompetencia) {
        return (object) ['id_competencia' => (int) $idCompetencia];
    }, $competenciasGlobales);

    $this->response['estado']           = true;
    $this->response['datos']            = $alumnos;
    $this->response['competencias']     = $competenciasResponse;
    $this->response['periodo_anterior'] = $periodoAnterior->nombre;
    $this->response['periodo_actual']   = $periodoActual->nombre;

    return response()->json($this->response, 200);
}


}
