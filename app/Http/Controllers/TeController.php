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

        $alumnos = [];

        foreach ($competencias as $competencia) {
        $res = DB::select("SELECT estudiante.id as estudiante, estudiante.dni, estudiante.nombres, curso_detalle.nota,
        estudiante.paterno, estudiante.materno, programa.programa, datos_ingreso.semestre AS semestre
        FROM curso
        JOIN curso_detalle ON curso.id = curso_detalle.id_curso
        JOIN estudiante ON estudiante.id = curso_detalle.id_alumno
        JOIN datos_ingreso ON estudiante.dni = datos_ingreso.dni
        JOIN programa ON datos_ingreso.id_programa=programa.id
        WHERE curso.escuela = '".$escuela[0]->nombre."'
        AND curso.id_competencia = :competencia
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
              'dni' => $row->dni,
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

}
