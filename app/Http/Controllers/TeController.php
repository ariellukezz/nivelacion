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
    $escuela = DB::select("
        SELECT nombre FROM escuela WHERE id = ? LIMIT 1
    ", [auth()->user()->id_escuela]);

    if (empty($escuela)) {
        return response()->json(['estado' => false, 'mensaje' => 'No se encontró la escuela.'], 404);
    }

    // ✅ Obtener el periodo ACTIVO
    $periodoActivo = DB::select("
        SELECT id_periodo, nombre FROM periodo
        WHERE estado = 'activo'
        LIMIT 1
    ");

    if (empty($periodoActivo)) {
        return response()->json(['estado' => false, 'mensaje' => 'No hay periodo activo.'], 404);
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
            d.i_C1_R,  d.i_C2_R,  d.i_C3_R,
            d.i_C4_R,  d.i_C5_R,  d.i_C6_R,
            d.i_C7_R,  d.i_C8_R,  d.i_C9_R,
            d.i_C10_R, d.i_C11_R
        FROM data_ingresante d
        INNER JOIN programa p ON d.id_niv = p.id
        INNER JOIN escuela e ON p.id_escuela = e.id
        WHERE e.nombre = ?
          AND d.id_periodo = ?
        ORDER BY d.primer_apellido ASC, d.segundo_apellido ASC
    ", [$escuela[0]->nombre, $periodoActivo[0]->id_periodo]);

    return response()->json([
        'estado'         => true,
        'datos'          => $ingresantes,
        'periodo_actual' => $periodoActivo[0]->nombre  // ej: "2026-I"
    ], 200);
}



public function getReprobadosNivelacion()
{
    $escuela = DB::select("SELECT nombre FROM escuela WHERE id = '" . auth()->user()->id_escuela . "';");

    $competencias = DB::select("
        SELECT DISTINCT curso.id_competencia
        FROM curso
        WHERE curso.escuela = '".$escuela[0]->nombre."'
        ORDER BY curso.id_competencia ASC
    ");

    $alumnos = [];

    foreach ($competencias as $competencia) {
        $res = DB::select("SELECT
                estudiante.id AS estudiante,
                estudiante.codigo_est,
                estudiante.nombres,
                curso_detalle.nota,
                estudiante.paterno,
                estudiante.materno,
                estudiante.email,
                estudiante.telefono,
                programa.programa,
                datos_ingreso.semestre AS semestre,
                escuela.filial
            FROM curso
            JOIN curso_detalle ON curso.id = curso_detalle.id_curso
            JOIN estudiante ON estudiante.id = curso_detalle.id_alumno
            JOIN datos_ingreso ON estudiante.codigo_est = datos_ingreso.codigo_est
            JOIN programa ON datos_ingreso.id_programa = programa.id
            JOIN escuela ON programa.id_escuela = escuela.id AND escuela.nombre = curso.escuela
            JOIN periodo ON curso.id_periodo = periodo.id_periodo
            WHERE escuela.id = ".auth()->user()->id_escuela."
              AND curso.id_competencia = :competencia
              AND periodo.id_periodo = (
                    SELECT MAX(p2.id_periodo) - 1
                    FROM periodo p2
                )
            ORDER BY programa.programa ASC, estudiante.paterno ASC;
        ", ['competencia' => $competencia->id_competencia]);

        foreach ($res as $row) {
            $id   = $row->estudiante;
            $nota = $row->nota;

            if (!isset($alumnos[$id])) {
                $alumnos[$id] = [
                    'id_estudiante' => $row->estudiante,
                    'nombre'        => $row->nombres,
                    'programa'      => $row->programa,
                    'semestre'      => $row->semestre,
                    'filial'        => $row->filial,
                    'codigo_est'    => $row->codigo_est,
                    'paterno'       => $row->paterno,
                    'materno'       => $row->materno,
                    'email'         => $row->email,
                    'telefono'      => $row->telefono,
                    'notas'         => [],
                ];
            }

            $alumnos[$id]['notas'][] = [
                'nota'        => $nota,
                'competencia' => (int)$competencia->id_competencia,
            ];
        }
    }

    $alumnos = array_values(array_filter($alumnos, function ($a) {
        foreach ($a['notas'] as $n) {
            $v = $n['nota'];
            if ($v === null || $v === '') return true;
            if (is_numeric($v) && floatval($v) <= 10) return true;
        }
        return false;
    }));

    // ✅ NUEVO: obtener los dos últimos periodos
    $periodos = DB::select("
        SELECT nombre, id_periodo
        FROM periodo
        ORDER BY id_periodo DESC
        LIMIT 2
    ");

    // ✅ NUEVO: asignar al response
    $this->response['estado']           = true;
    $this->response['datos']            = $alumnos;
    $this->response['competencias']     = $competencias;
    $this->response['periodo_anterior'] = $periodos[1]->nombre ?? '';
    $this->response['periodo_actual']   = $periodos[0]->nombre ?? '';

    return response()->json($this->response, 200);
}





}
