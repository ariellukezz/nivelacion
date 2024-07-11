<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\CursoDetalle;
use App\Models\Alumno;
use App\Models\Documento;
use App\Models\Competencia;
use App\Models\Programa;
use Inertia\Inertia;
use App\Models\Docente;
use App\Models\Escuela;
use App\Models\Pregunta;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;


class SuperadmiController extends Controller
{

    public function getDocumentos(Request $request) {
        // Definir las condiciones iniciales
        $conditions = [];

        if ($request->programa) array_push($conditions,[DB::raw('documento.id_escuela'), '=', $request->programa]);

        // Realizar la consulta de manera más legible
        $query = Documento::select(
            'documento.tipo',
            'escuela.nombre AS escuela',
            'escuela.nombre_corto',
            'documento.fecha_subida',
            'users.nombres AS username',
            'users.apellidos AS userlastname',
            'documento.url'
        )
        ->join('escuela', 'documento.id_escuela', '=', 'escuela.id')
        ->join('users', 'users.id', '=', 'documento.id_usuario')
        ->where(function ($query) use ($request) {
            $query->where('escuela.nombre', 'LIKE', '%' . $request->term . '%')
                ->orWhere('documento.tipo', 'LIKE', '%' . $request->term . '%')
                ->orWhere('users.nombres', 'LIKE', '%' . $request->term . '%');
        });

        // Aplicar condiciones adicionales si las hay
        if (!empty($conditions)) {
            $query->where($conditions);
        }

        // $res = $query->paginate($request->paginashoja);
        $res = $query->paginate(200);

        // Devolver una respuesta JSON
        return response()->json([
            'estado' => true,
            'datos' => $res,
        ], 200);
    }

    public function getAlumnos(Request $request) {
        $competencia = $request->competencia;
        $term = $request->buscar;

        $estudiantes = DB::table('estudiante AS e')
            ->select(
                'e.id',
                'e.dni',
                'e.codigo',
                'e.paterno',
                'e.materno',
                'e.nombres',
                'e.sexo',
                'e.email',
                'e.anio_egreso',
                'e.nombre_colegio',
                'e.telefono',
                'p.programa',
                'c.nombre',
                'd.modalidad',
                'd.semestre',
                'cd.nota',
                'c.id_competencia',
                'm.C' . $competencia . '_R'
            )
            ->join('datos_ingreso AS d', 'e.dni', '=', 'd.dni')
            ->join('matriz AS m', 'e.dni', '=', 'm.dni')
            ->join('programa AS p', 'd.id_programa', '=', 'p.id')
            ->join('curso AS c', function ($join) use ($competencia) {
                $join->on('p.id', '=', 'c.id_programa')
                    ->where('c.id_competencia', $competencia);
            })
            ->join('curso_detalle AS cd', function ($join) {
                $join->on('e.id', '=', 'cd.id_alumno')
                    ->on('cd.id_curso', '=', 'c.id');
            })
            ->where(function ($query) use ($term) {
                $query->orWhere('e.dni', 'LIKE', '%' . $term . '%')
                    ->orWhere('e.codigo', 'LIKE', '%' . $term . '%')
                    ->orWhere('e.nombres', 'LIKE', '%' . $term . '%')
                    ->orWhere('e.paterno', 'LIKE', '%' . $term . '%')
                    ->orWhere('e.materno', 'LIKE', '%' . $term . '%')
                    ->orWhere('p.programa', 'LIKE', '%' . $term . '%');
            })
            ->distinct()
            ->get();


        $this->response['estado'] = true;
        $this->response['datos'] = $estudiantes;
        return response()->json($this->response, 200);
    }

    public function getDocentes(Request $request) {
        $term = $request->buscar;

    $docentes = DB::table('docente as d')
    ->select(
        'd.nro_doc',
        'd.paterno',
        'd.materno',
        'd.nombres',
        'd.sexo',
        'd.telefono',
        'd.email',
        'u.estado',
        'u.nombres as nombre_usuario',
        'p.programa as nombre_escuela')
    ->join('users as u', 'd.usuario_id', '=', 'u.id')
    ->join('programa as p', 'u.programa_id', '=', 'p.id')

    ->where(function ($query) use ($term) {
        $query->orWhere('d.nro_doc', 'LIKE', '%' . $term . '%')
            ->orWhere('d.nombres', 'LIKE', '%' . $term . '%')
            ->orWhere('d.paterno', 'LIKE', '%' . $term . '%')
            ->orWhere('d.materno', 'LIKE', '%' . $term . '%');
    })
    ->get();

    $this->response['estado'] = true;
    $this->response['datos'] = $docentes;
    return response()->json($this->response, 200);
        }

        public function getUsuarios(Request $request) {
            $usuarios = [];
            $term = $request->buscar;

            switch ($request->rol) {
                case 0:
                    $usuarios = Usuario::select('id', 'nombres', 'apellidos', 'email', 'estado_contraseña')
                        ->where('rol', '0')
                        ->where(function ($query) use ($term) {
                            $query->orWhere('nombres', 'LIKE', '%' . $term . '%')
                                  ->orWhere('apellidos', 'LIKE', '%' . $term . '%')
                                  ->orWhere('email', 'LIKE', '%' . $term . '%');
                        })
                        ->get();
                    break;
                case 1:
                    $usuarios = Usuario::select('id', 'nombres', 'apellidos', 'email', 'estado_contraseña')
                        ->where('rol', '1')
                        ->where(function ($query) use ($term) {
                            $query->orWhere('nombres', 'LIKE', '%' . $term . '%')
                                  ->orWhere('apellidos', 'LIKE', '%' . $term . '%')
                                  ->orWhere('email', 'LIKE', '%' . $term . '%');
                        })
                        ->get();
                    break;
                case 2:
                    $usuarios = Usuario::select('id', 'nombres', 'apellidos', 'email', 'estado_contraseña')
                        ->where('rol', '2')
                        ->where(function ($query) use ($term) {
                            $query->orWhere('nombres', 'LIKE', '%' . $term . '%')
                                  ->orWhere('apellidos', 'LIKE', '%' . $term . '%')
                                  ->orWhere('email', 'LIKE', '%' . $term . '%');
                        })
                        ->get();
                    break;
                case 3:
                    $usuarios = Usuario::select('id', 'nombres', 'apellidos', 'email', 'estado_contraseña')
                        ->where('rol', '3')
                        ->where(function ($query) use ($term) {
                            $query->orWhere('nombres', 'LIKE', '%' . $term . '%')
                                  ->orWhere('apellidos', 'LIKE', '%' . $term . '%')
                                  ->orWhere('email', 'LIKE', '%' . $term . '%');
                        })
                        ->get();
                    break;
                case 4:
                    $usuarios = Usuario::select('users.id', 'docente.nombres', 'users.email', 'users.estado_contraseña')
                    ->join('docente', 'users.id', '=', 'docente.usuario_id')
                    ->where('users.rol', '4')
                    ->selectRaw("CONCAT(docente.paterno, ' ', docente.materno) AS apellidos")
                    ->where(function ($query) use ($term) {
                        $query->orWhere('docente.nombres', 'LIKE', '%' . $term . '%')
                              ->orWhere('docente.paterno', 'LIKE', '%' . $term . '%')
                              ->orWhere('docente.materno', 'LIKE', '%' . $term . '%')
                              ->orWhere('users.email', 'LIKE', '%' . $term . '%');
                    })
                    ->get();
                    break;
                case 5:
                    $usuarios = Usuario::select('users.id', 'estudiante.nombres', 'users.email', 'users.estado_contraseña')
    ->join('estudiante', 'users.id', '=', 'estudiante.usuario_id')
    ->where('users.rol', '5')
    ->selectRaw("CONCAT(estudiante.paterno, ' ', estudiante.materno) AS apellidos")
    ->where(function ($query) use ($term) {
        $query->orWhere('estudiante.nombres', 'LIKE', '%' . $term . '%')
              ->orWhere('estudiante.paterno', 'LIKE', '%' . $term . '%')
              ->orWhere('estudiante.materno', 'LIKE', '%' . $term . '%')
              ->orWhere('users.email', 'LIKE', '%' . $term . '%');
    })
    ->get();
                    break;
                case 6:
                    $usuarios = Usuario::select('id', 'nombres', 'apellidos', 'email', 'estado_contraseña')
                        ->where('rol', '6')
                        ->where(function ($query) use ($term) {
                            $query->orWhere('nombres', 'LIKE', '%' . $term . '%')
                                  ->orWhere('apellidos', 'LIKE', '%' . $term . '%')
                                  ->orWhere('email', 'LIKE', '%' . $term . '%');
                        })
                        ->get();
                    break;
                default:
                    return "Rol no reconocido";
            }

            $this->response['estado'] = true;
            $this->response['datos'] = $usuarios;
            return response()->json($this->response, 200);
        }

public function restablecer(Request $request) {
    $usuario = Usuario::find($request->id);
    $usuario['password'] = Hash::make('nivelacion');
    $usuario['estado_contraseña']= 1;
    $usuario->save();
    return $usuario;
}

// aqui esta controlador DataController
public function getProgramas(Request $request){

    $query_where = [];
    $res = Programa::select(
        'id as value', 'programa as label'
    )
    ->where($query_where)
    ->where(function ($query) use ($request) {
        return $query
            ->orWhere('programa.programa', 'LIKE', '%' . $request->term . '%');
    })->orderBy('programa.id', 'ASC')
    ->paginate(50);

    $this->response['estado'] = true;
    $this->response['datos'] = $res;
    return response()->json($this->response, 200);

  }

  public function getRoles(Request $request){

    $query_where = [];
    $res = Rol::select(
        'id as value', 'nombre as label'
    )
    ->where($query_where)
    ->where(function ($query) use ($request) {
        return $query
            ->orWhere('nombre', 'LIKE', '%' . $request->term . '%');
    })->orderBy('rol.id', 'ASC')
    ->paginate(50);

    $this->response['estado'] = true;
    $this->response['datos'] = $res;
    return response()->json($this->response, 200);

  }

  public function getEscuelas(Request $request){
    $res = Escuela::select('escuela.id','escuela.nombre as escuela', 'programa.facultad', 'programa.area')
    ->join('programa','programa.id_escuela','escuela.id')
    ->where(function ($query) use ($request) {
        return $query
            ->orWhere('escuela.nombre', 'LIKE', '%' . $request->term . '%');
    })->distinct()
    ->paginate(100);

    $this->response['estado'] = true;
    $this->response['datos'] = $res;
    return response()->json($this->response, 200);

  }

  public function getCompetencias(Request $request){

    $query_where = [];
    $res = Competencia::select(
        'id as value', 'nombre as label'
    )
    ->where($query_where)
    ->where(function ($query) use ($request) {
        return $query
            ->orWhere('nombre', 'LIKE', '%' . $request->term . '%');
    })->orderBy('id', 'ASC')
    ->paginate(20);

    $this->response['estado'] = true;
    $this->response['datos'] = $res;
    return response()->json($this->response, 200);

  }
//termina aqui

// empieza AsignacionController

public function getCursos(Request $request){

    $query_where = [];

    if ($request->competencia !== null) array_push($query_where, ['curso.id_competencia', '=', $request->competencia]);

    $res = Curso::select(
        'curso.id', 'curso.nombre',
        'docente.id as id_docente', DB::raw("CONCAT( docente.nombres,' ',docente.paterno,' ',docente.materno) as docente"),
        'competencia.id as id_competencia', 'competencia.nombre as competencia',
        'curso.grupo','curso.escuela', 'curso.estado'
    )
    ->leftJoin('docente','docente.id','curso.id_docente')
    ->join('competencia','competencia.id','curso.id_competencia')
    ->where('curso.escuela',"=",$request->escuela)
    ->where($query_where)
    ->where(function ($query) use ($request) {
        return $query
            ->orWhere('curso.nombre', 'LIKE', '%' . $request->term . '%')
            ->orWhere('competencia.nombre', 'LIKE', '%' . $request->term . '%');
    })->orderBy('curso.id', 'DESC')
    ->paginate(100);

    $this->response['estado'] = true;
    $this->response['datos'] = $res;
    return response()->json($this->response, 200);

}

public function getDetalleCurso(Request $request){

    $query_where = [];
    //if ($request->competencia !== null) array_push($query_where, ['curso.id_competencia', '=', $request->competencia]);


    $res = CursoDetalle::select(
        'estudiante.dni', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno',
        'curso.nombre as curso',
        'curso_detalle.nota'
    )
    ->join('curso','curso_detalle.id_curso','curso.id')
    ->join('estudiante','estudiante.id','curso_detalle.id_alumno')
    ->where('curso.id',"=",$request->curso)
    ->where($query_where)
    ->where(function ($query) use ($request) {
        return $query
            ->orWhere('curso.nombre', 'LIKE', '%' . $request->term . '%');
    })->orderBy('curso.id', 'DESC')
    ->paginate(200);

    $registrados = CursoDetalle::select(
        'estudiante.id', 'estudiante.dni', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno')
    ->join('curso','curso_detalle.id_curso','curso.id')
    ->join('estudiante','estudiante.id','curso_detalle.id_alumno')
    ->where('curso.id',"=",$request->curso)
    ->where($query_where)
    ->where(function ($query) use ($request) {
        return $query
            ->orWhere('curso.nombre', 'LIKE', '%' . $request->term . '%');
    })->orderBy('curso.id', 'DESC')
    ->paginate(200);

    $this->response['estado'] = true;
    $this->response['datos'] = $res;
    $this->response['registrados'] = $registrados;

    return response()->json($this->response, 200);

}


public function save(Request $request ) {

    $curso = null;
    if (!$request->id) {
        $curso = Curso::create([
            'nombre' => $request->nombre,
            'id_competencia' => $request->id_competencia,
            'id_docente' => $request->id_docente,
            'grupo' => $request->grupo,
            'escuela' => $request->escuela,
            'estado' => $request->estado,
            'id_programa' => $request->id_programa,
            'id_usuario' => auth()->id(),
        ]);
        $this->response['tipo'] = 'success';
        $this->response['titulo'] = 'REGISTRO NUEVO';
        $this->response['mensaje'] = 'Curso de nivelación '.$curso->nombre.' registrado con exito';
        $this->response['estado'] = true;
        $this->response['datos'] = $curso;
    } else {
        $curso = Curso::find($request->id);
        $curso->nombre= $request->nombre;
        $curso->id_competencia= $request->id_competencia;
        $curso->id_docente = $request->id_docente;
        $curso->grupo = $request->grupo;
        $curso->escuela = $request->escuela;
        $curso->estado = $request->estado;
        $curso->id_programa = $request->id_programa;
        $curso->save();

        $this->response['tipo'] = 'info';
        $this->response['titulo'] = '!REGISTRO MODIFICADO!';
        $this->response['mensaje'] = 'Docente '.$curso->nombre.' acaba de ser modificado.';
        $this->response['estado'] = true;
        $this->response['datos'] = $curso;
    }

    return response()->json($this->response, 200);
}


public function getDocentesXcompetencia(Request $request){

    $competencia = $request->competencia;

    $res = Docente::select(
        'docente.id',
        DB::raw("CONCAT( docente.nombres,' ',docente.paterno,' ',docente.materno) as nombres"),
    )
    ->join('docente_competencia','docente.id','docente_competencia.id_docente')
    ->where('estado','=',1)
    ->where('docente_competencia.id_competencia',"=",$competencia)
#        ->where('docente.id_usuario','=',auth()->id())
    ->where(function ($query) use ($request) {
        return $query
            ->orWhere('docente.nombres', 'LIKE', '%' . $request->term . '%')
            ->orWhere('docente.paterno', 'LIKE', '%' . $request->term . '%')
            ->orWhere('docente.materno', 'LIKE', '%' . $request->term . '%')
            ->orWhere('docente.nro_doc', 'LIKE', '%' . $request->term . '%');
    })->orderBy('docente.id', 'DESC')
    ->paginate(1000);

    $this->response['estado'] = true;
    $this->response['datos'] = $res;
    return response()->json($this->response, 200);

}

public function asignarCursoNivelacion(Request $request){

  try {
      DB::transaction(function () use ($request) {

        foreach ($request->diferencia as $alumno) {
            $this->asignarCurso($request->curso, $alumno['id']);
        }

        foreach ($request->diferencia2 as $alumno) {
            $this->eliminarCurso($request->curso, $alumno['id']);
        }

            $this->response['tipo'] = 'success';
            $this->response['titulo'] = '!REGISTROS NUEVOS!';
            $this->response['mensaje'] = 'ALUMNOS REGISTRADOS CON EXITOS';
            $this->response['estado'] = true;
            // $this->response['datos'] = $curso;
        return response()->json($this->response, 200);
      });
    } catch (\Throwable $e) {
      echo "Error en la transacción: " . $e->getMessage();
  }

}

public function asignarCurso($id_curso, $id_alumno){

    $curso = CursoDetalle::create([
        'id_curso' => $id_curso,
        'id_alumno' => $id_alumno,
    ]);

}

public function eliminarCurso($id_curso, $id_alumno) {
    $cursoDetalle = CursoDetalle::where('id_curso', $id_curso)
                                ->where('id_alumno', $id_alumno)
                                ->first();
    if ($cursoDetalle) {
        $cursoDetalle->delete();
        return response()->json(['message' => 'Registro eliminado con éxito']);
    } else {
        return response()->json(['message' => 'Registro no encontrado'], 404);
    }
}
//--------------------------------------------- prueba de preguntas------------------------------
public function getPregunta(){
    $data = Pregunta::all();
    return response()->json($data);
}


public function savep(Request $request){
    if ($request->id != null) {
        $preg = pregunta::find($request->id);
        $preg['texto'] = $request->texto;
        $preg['aspecto'] = $request->aspecto;
        $preg['id_encuesta'] = $request->id_encuesta;
        $preg->save();
    }else {
        $pregunta = pregunta::create([
            'texto' => $request->texto,
            'aspecto' => $request->aspecto,
            'id_encuesta' => $request->id_encuesta
        ]);
    }
    return $pregunta;
}


public function eliminarp($id){
    $usario = pregunta::find($id);
    $usario->delete();
}

//----------------------- notas perfil
public function getTestResults() {
   // $escuela = DB::select("SELECT nombre from escuela where id = '" .auth()->user()->id_escuela ."';" );

    $competencias = DB::select("SELECT DISTINCT curso.id_competencia
    FROM curso
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
        WHERE curso.id_competencia = :competencia
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
