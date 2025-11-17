<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\CursoDetalle;
use App\Models\Alumno;
use App\Models\Matriz;
use App\Models\DatoIngreso;
use App\Models\Documento;
use App\Models\Competencia;
use App\Models\Programa;
use Inertia\Inertia;
use App\Models\Docente;
use App\Models\Escuela;
use App\Models\Pregunta;
use App\Models\Usuario;
use App\Models\Periodo;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;


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
                'e.codigo_est',
                // bdhh 'e.dni',
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
            ->join('datos_ingreso AS d', 'e.codigo_est', '=', 'd.codigo_est')
           //bdhh ->join('datos_ingreso AS d', 'e.dni', '=', 'd.dni')
            ->join('matriz AS m', 'e.codigo_est', '=', 'm.codigo_est')
           //bdhh ->join('matriz AS m', 'e.dni', '=', 'm.dni')
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
                $query->orWhere('e.codigo_est', 'LIKE', '%' . $term . '%')
               //bdhh $query->orWhere('e.dni', 'LIKE', '%' . $term . '%')
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
            ->orWhere('d.materno', 'LIKE', '%' . $term . '%')
            ->orWhere('d.email', 'LIKE', '%' . $term . '%');
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
    $res = Escuela::select('escuela.id','escuela.nombre as escuela', 'programa.facultad', 'escuela.filial', 'programa.area')
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
        'estudiante.codigo_est', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno',
       //bdhh 'estudiante.dni', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno',
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
        'estudiante.id', 'estudiante.codigo_est', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno')
       //bdhh 'estudiante.id', 'estudiante.dni', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno')
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

public function getTestResults(Request $request)
{
    // 1. Periodo (por parámetro o activo)
    $idPeriodo = $request->input('periodo');

    if (!$idPeriodo) {
        $periodoActivo = DB::table('periodo')->where('estado', 'activo')->first();

        if (!$periodoActivo) {
            return response()->json([
                'estado'  => false,
                'mensaje' => 'No existe un periodo activo',
            ], 400);
        }

        $idPeriodo = $periodoActivo->id_periodo;
    }

    // 2. Consulta pivot con AP / S/N / nota
    $sql = "
        SELECT
            t.id_estudiante,
            t.codigo_est,
            t.paterno,
            t.materno,
            t.nombres AS nombre,
            t.programa,
            t.semestre,
            t.filial,

            CASE
                WHEN t.C1_has = 1 AND t.C1_nota IS NULL THEN 'S/N'
                WHEN t.C1_has = 1 AND t.C1_nota IS NOT NULL THEN CAST(t.C1_nota AS CHAR)
                ELSE 'AP'
            END AS C1,

            CASE
                WHEN t.C2_has = 1 AND t.C2_nota IS NULL THEN 'S/N'
                WHEN t.C2_has = 1 AND t.C2_nota IS NOT NULL THEN CAST(t.C2_nota AS CHAR)
                ELSE 'AP'
            END AS C2,

            CASE
                WHEN t.C3_has = 1 AND t.C3_nota IS NULL THEN 'S/N'
                WHEN t.C3_has = 1 AND t.C3_nota IS NOT NULL THEN CAST(t.C3_nota AS CHAR)
                ELSE 'AP'
            END AS C3,

            CASE
                WHEN t.C4_has = 1 AND t.C4_nota IS NULL THEN 'S/N'
                WHEN t.C4_has = 1 AND t.C4_nota IS NOT NULL THEN CAST(t.C4_nota AS CHAR)
                ELSE 'AP'
            END AS C4,

            CASE
                WHEN t.C5_has = 1 AND t.C5_nota IS NULL THEN 'S/N'
                WHEN t.C5_has = 1 AND t.C5_nota IS NOT NULL THEN CAST(t.C5_nota AS CHAR)
                ELSE 'AP'
            END AS C5,

            CASE
                WHEN t.C6_has = 1 AND t.C6_nota IS NULL THEN 'S/N'
                WHEN t.C6_has = 1 AND t.C6_nota IS NOT NULL THEN CAST(t.C6_nota AS CHAR)
                ELSE 'AP'
            END AS C6,

            CASE
                WHEN t.C7_has = 1 AND t.C7_nota IS NULL THEN 'S/N'
                WHEN t.C7_has = 1 AND t.C7_nota IS NOT NULL THEN CAST(t.C7_nota AS CHAR)
                ELSE 'AP'
            END AS C7,

            CASE
                WHEN t.C8_has = 1 AND t.C8_nota IS NULL THEN 'S/N'
                WHEN t.C8_has = 1 AND t.C8_nota IS NOT NULL THEN CAST(t.C8_nota AS CHAR)
                ELSE 'AP'
            END AS C8,

            CASE
                WHEN t.C9_has = 1 AND t.C9_nota IS NULL THEN 'S/N'
                WHEN t.C9_has = 1 AND t.C9_nota IS NOT NULL THEN CAST(t.C9_nota AS CHAR)
                ELSE 'AP'
            END AS C9,

            CASE
                WHEN t.C10_has = 1 AND t.C10_nota IS NULL THEN 'S/N'
                WHEN t.C10_has = 1 AND t.C10_nota IS NOT NULL THEN CAST(t.C10_nota AS CHAR)
                ELSE 'AP'
            END AS C10,

            CASE
                WHEN t.C11_has = 1 AND t.C11_nota IS NULL THEN 'S/N'
                WHEN t.C11_has = 1 AND t.C11_nota IS NOT NULL THEN CAST(t.C11_nota AS CHAR)
                ELSE 'AP'
            END AS C11

        FROM (
            SELECT
                est.id AS id_estudiante,
                est.codigo_est,
                est.paterno,
                est.materno,
                est.nombres,
                prog.programa,
                di.semestre,
                esc.filial,

                MAX(CASE WHEN curso.id_competencia = 1  THEN cd.nota END) AS C1_nota,
                MAX(CASE WHEN curso.id_competencia = 2  THEN cd.nota END) AS C2_nota,
                MAX(CASE WHEN curso.id_competencia = 3  THEN cd.nota END) AS C3_nota,
                MAX(CASE WHEN curso.id_competencia = 4  THEN cd.nota END) AS C4_nota,
                MAX(CASE WHEN curso.id_competencia = 5  THEN cd.nota END) AS C5_nota,
                MAX(CASE WHEN curso.id_competencia = 6  THEN cd.nota END) AS C6_nota,
                MAX(CASE WHEN curso.id_competencia = 7  THEN cd.nota END) AS C7_nota,
                MAX(CASE WHEN curso.id_competencia = 8  THEN cd.nota END) AS C8_nota,
                MAX(CASE WHEN curso.id_competencia = 9  THEN cd.nota END) AS C9_nota,
                MAX(CASE WHEN curso.id_competencia = 10 THEN cd.nota END) AS C10_nota,
                MAX(CASE WHEN curso.id_competencia = 11 THEN cd.nota END) AS C11_nota,

                MAX(CASE WHEN curso.id_competencia = 1  THEN 1 END) AS C1_has,
                MAX(CASE WHEN curso.id_competencia = 2  THEN 1 END) AS C2_has,
                MAX(CASE WHEN curso.id_competencia = 3  THEN 1 END) AS C3_has,
                MAX(CASE WHEN curso.id_competencia = 4  THEN 1 END) AS C4_has,
                MAX(CASE WHEN curso.id_competencia = 5  THEN 1 END) AS C5_has,
                MAX(CASE WHEN curso.id_competencia = 6  THEN 1 END) AS C6_has,
                MAX(CASE WHEN curso.id_competencia = 7  THEN 1 END) AS C7_has,
                MAX(CASE WHEN curso.id_competencia = 8  THEN 1 END) AS C8_has,
                MAX(CASE WHEN curso.id_competencia = 9  THEN 1 END) AS C9_has,
                MAX(CASE WHEN curso.id_competencia = 10 THEN 1 END) AS C10_has,
                MAX(CASE WHEN curso.id_competencia = 11 THEN 1 END) AS C11_has

            FROM curso
            JOIN curso_detalle cd ON curso.id = cd.id_curso
            JOIN estudiante est ON est.id = cd.id_alumno
            JOIN datos_ingreso di ON est.codigo_est = di.codigo_est
            JOIN programa prog ON di.id_programa = prog.id
            JOIN escuela esc ON prog.id_escuela = esc.id
            JOIN periodo p ON curso.id_periodo = p.id_periodo

            WHERE p.id_periodo = :periodo

            GROUP BY
                est.id,
                est.codigo_est,
                est.paterno,
                est.materno,
                est.nombres,
                prog.programa,
                di.semestre,
                esc.filial
        ) AS t

        ORDER BY
            t.programa ASC,
            t.paterno ASC
    ";

    $alumnos = DB::select($sql, ['periodo' => $idPeriodo]);

    return response()->json([
        'estado'           => true,
        'datos'            => $alumnos,
        'competencias'     => [],          // ya no las usas en el Vue
        'periodo_filtrado' => $idPeriodo,
    ], 200);
}


public function getEstudiantes(Request $request) {
    //$term = $request->input('buscar');  // Término de búsqueda desde la solicitud
    $term = $request->buscar;

    // Consulta original sin modificaciones
    $estudiantes = DB::table('estudiante')
        ->select(
            'estudiante.codigo_est',
           // 'estudiante.dni',
            'estudiante.nombres',
            'estudiante.paterno',
            'estudiante.materno',
            'estudiante.sexo',
            'estudiante.telefono',
            'estudiante.email',
            'estudiante.direccion',
            'datos_ingreso.semestre',
            'datos_ingreso.modalidad',
            'datos_ingreso.anio',
            'programa.programa AS nombre_programa',
            'programa.escuela AS nombre_escuela',
            'matriz.c1_R AS competencia_1',
            'matriz.c2_R AS competencia_2',
            'matriz.c3_R AS competencia_3',
            'matriz.c4_R AS competencia_4',
            'matriz.c5_R AS competencia_5',
            'matriz.c6_R AS competencia_6',
            'matriz.c7_R AS competencia_7',
            'matriz.c8_R AS competencia_8',
            'matriz.c9_R AS competencia_9',
            'matriz.c10_R AS competencia_10',
            'matriz.c11_R AS competencia_11'
        )
        ->join('datos_ingreso', 'estudiante.codigo_est', '=', 'datos_ingreso.codigo_est')
      //bdhh ->join('datos_ingreso', 'estudiante.dni', '=', 'datos_ingreso.dni')
        ->join('programa', 'datos_ingreso.id_programa', '=', 'programa.id')
        ->join('matriz', 'datos_ingreso.codigo_est', '=', 'matriz.codigo_est')
      //bdhh ->join('matriz', 'datos_ingreso.dni', '=', 'matriz.dni')
        ->where(function ($query) use ($term) {
            $query->orWhere('estudiante.codigo_est', 'LIKE', '%' . $term . '%')
           //bdhh $query->orWhere('estudiante.dni', 'LIKE', '%' . $term . '%')
                ->orWhere(DB::raw("CONCAT(estudiante.nombres, ' ', estudiante.paterno, ' ', estudiante.materno)"), 'LIKE', '%' . $term . '%');
        })
        ->get();

    // Devolvemos la respuesta en formato JSON
    return response()->json([
        'estado' => true,
        'datos' => $estudiantes,
    ], 200);
}

// En tu SuperadmiController
public function getPeriodos()
    {
        $rows = DB::table('periodo')
            ->orderByDesc('id_periodo') // ordena 5, 4, 3...
            ->get(['id_periodo', 'nombre', 'estado']);

        $options = $rows->map(fn($r) => [
            'value' => $r->nombre,
            'label' => $r->nombre
        ]);

        return response()->json([
            'estado'  => true,
            'raw'     => $rows,
            'options' => $options,
        ], 200);
    }

    /**
     * Guarda o actualiza un periodo
     */
    public function savePeriodo(Request $request)
{
    $data = $request->validate([
        'nombre' => 'required|string|max:255',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        'estado' => 'required|in:activo,inactivo'
    ]);

    if ($request->id_periodo) {
        $periodo = Periodo::find($request->id_periodo);
        $periodo->update($data);
    } else {
        // Generar un ID único si no es autoincremental
        $data['id_periodo'] = Periodo::max('id_periodo') + 1;
        $periodo = Periodo::create($data);
    }

    return $periodo;
}

    /**
     * Elimina un periodo
     */
    public function eliminarPeriodo($id)
{
    try {
        $periodo = Periodo::find($id);

        if (!$periodo) {
            return response()->json([
                'success' => false,
                'message' => 'Periodo no encontrado'
            ], 404);
        }

        // Verificar si tiene relaciones antes de eliminar
        if ($periodo->tieneRelaciones()) { // Debes implementar este método en tu modelo
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar el periodo porque tiene datos relacionados'
            ], 409); // 409 Conflict
        }

        $periodo->delete();

        return response()->json([
            'success' => true,
            'message' => 'Periodo eliminado exitosamente'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error al eliminar el periodo: ' . $e->getMessage()
        ], 500);
    }
}

// aqui actulizar estudiantes
public function previewUpdate(Request $request)
    {
        $validated = $this->validateRequest($request);

        list($query, $params) = $this->buildBaseQuery(
            $validated['id_escuela'],
            $validated['id_periodo'],
            $validated['nota_minima'],
            $validated['nota_maxima']
        );

        // Solo seleccionar datos para previsualización
        $previewData = DB::select(
            "SELECT n.codigo_est, n.C1, n.C2, n.C3, n.C4, n.C5, n.C6, n.C7, n.C8, n.C9, n.C10, n.C11,
                    m.C1_R as actual_C1, m.C2_R as actual_C2, m.C3_R as actual_C3,
                    m.C4_R as actual_C4, m.C5_R as actual_C5, m.C6_R as actual_C6,
                    m.C7_R as actual_C7, m.C8_R as actual_C8, m.C9_R as actual_C9,
                    m.C10_R as actual_C10, m.C11_R as actual_C11
             FROM ($query) AS n
             JOIN matriz m ON n.codigo_est = m.codigo_est",
            $params
        );

        return response()->json([
            'success' => true,
            'preview' => true,
            'data' => $previewData,
            'filters' => [
                'id_escuela' => $validated['id_escuela'],
                'id_periodo' => $validated['id_periodo'],
                'nota_minima' => $validated['nota_minima'],
                'nota_maxima' => $validated['nota_maxima'],
                'total_registros' => count($previewData)
            ]
        ]);
    }

    // Método para ejecutar la actualización
    public function executeUpdate(Request $request)
    {
        $validated = $this->validateRequest($request);

        list($query, $params) = $this->buildBaseQuery(
            $validated['id_escuela'],
            $validated['id_periodo'],
            $validated['nota_minima'],
            $validated['nota_maxima']
        );

        $affectedRows = DB::update(
            "UPDATE matriz m
             JOIN ($query) AS n ON n.codigo_est = m.codigo_est
             SET
                m.C1_R = COALESCE(n.C1, m.C1_R),
                m.C2_R = COALESCE(n.C2, m.C2_R),
                m.C3_R = COALESCE(n.C3, m.C3_R),
                m.C4_R = COALESCE(n.C4, m.C4_R),
                m.C5_R = COALESCE(n.C5, m.C5_R),
                m.C6_R = COALESCE(n.C6, m.C6_R),
                m.C7_R = COALESCE(n.C7, m.C7_R),
                m.C8_R = COALESCE(n.C8, m.C8_R),
                m.C9_R = COALESCE(n.C9, m.C9_R),
                m.C10_R = COALESCE(n.C10, m.C10_R),
                m.C11_R = COALESCE(n.C11, m.C11_R)",
            $params
        );

        return response()->json([
            'success' => true,
            'updated' => true,
            'affected_rows' => $affectedRows,
            'filters' => [
                'id_escuela' => $validated['id_escuela'],
                'id_periodo' => $validated['id_periodo'],
                'nota_minima' => $validated['nota_minima'],
                'nota_maxima' => $validated['nota_maxima']
            ]
        ]);
    }

    // Métodos auxiliares
    private function validateRequest(Request $request)
    {
        return $request->validate([
            'id_escuela' => 'nullable|integer',
            'id_periodo' => 'nullable|integer',
            'nota_minima' => 'nullable|integer|min:0|max:20',
            'nota_maxima' => 'nullable|integer|min:0|max:20'
        ]);
    }

    private function buildBaseQuery($idEscuela, $idPeriodo, $notaMinima, $notaMaxima)
    {
        $query = "
            SELECT
                e.codigo_est,
                MAX(IF(c.id_competencia = 1, cd.nota, NULL)) AS C1,
                MAX(IF(c.id_competencia = 2, cd.nota, NULL)) AS C2,
                MAX(IF(c.id_competencia = 3, cd.nota, NULL)) AS C3,
                MAX(IF(c.id_competencia = 4, cd.nota, NULL)) AS C4,
                MAX(IF(c.id_competencia = 5, cd.nota, NULL)) AS C5,
                MAX(IF(c.id_competencia = 6, cd.nota, NULL)) AS C6,
                MAX(IF(c.id_competencia = 7, cd.nota, NULL)) AS C7,
                MAX(IF(c.id_competencia = 8, cd.nota, NULL)) AS C8,
                MAX(IF(c.id_competencia = 9, cd.nota, NULL)) AS C9,
                MAX(IF(c.id_competencia = 10, cd.nota, NULL)) AS C10,
                MAX(IF(c.id_competencia = 11, cd.nota, NULL)) AS C11
            FROM estudiante e
            JOIN curso_detalle cd ON cd.id_alumno = e.id
            JOIN curso c ON c.id = cd.id_curso
            JOIN periodo per ON per.id_periodo = c.id_periodo
            JOIN datos_ingreso di ON di.codigo_est = e.codigo_est
            JOIN programa p ON p.id = di.id_programa
            JOIN escuela esc ON esc.id = p.id_escuela
            WHERE cd.nota BETWEEN ? AND ?
        ";

        $params = [$notaMinima, $notaMaxima];

        if ($idPeriodo) {
            $query .= " AND per.id_periodo = ?";
            $params[] = $idPeriodo;
        } else {
            $query .= " AND per.estado = 'activo'";
        }

        if ($idEscuela) {
            $query .= " AND esc.id = ?";
            $params[] = $idEscuela;
        }

        $query .= " GROUP BY e.codigo_est";

        return [$query, $params];
    }
public function obtenerListadoEscuelas()
    {
        $escuelas = DB::table('escuela')
                    ->select('id', 'nombre')
                    ->orderBy('nombre')
                    ->get();

        return response()->json($escuelas);
    }

    // Obtener listado de periodos académicos
public function obtenerListadoPeriodos()
{
    $periodos = DB::table('periodo')
                ->select('id_periodo', 'nombre')
                ->orderBy('id_periodo', 'desc')
                ->get();

    return response()->json($periodos);
}



public function importarEstudiante(Request $request)
    {
        // Leer parámetro para ignorar duplicados (enviado desde el frontend)
        $ignorar_duplicados = $request->input('ignorar_duplicados', false);

        // Inicializar variables para el reporte
        $reporte = [
            'total_registros' => count($request->datos),
            'registros_exitosos' => 0,
            'duplicados' => [],
            'errores' => [],
            'detalle_errores' => []
        ];

        DB::beginTransaction();

        try {
            foreach ($request->datos as $index => $item) {
                try {
                    // Validar si el DNI ya existe en alguna tabla
                    $dni = $item['dni'];
                    $existeEnAlumnos = Alumno::where('dni', $dni)->exists();
                    $existeEnMatriz = Matriz::where('dni', $dni)->exists();
                    $existeEnDatosIngreso = DatoIngreso::where('dni', $dni)->exists();

                    if ($existeEnAlumnos || $existeEnMatriz || $existeEnDatosIngreso) {
                        $reporte['duplicados'][] = [
                            'linea' => $index + 1,
                            'dni' => $dni,
                            'codigo_est' => $item['codigo_est'] ?? '',
                            'nombre' => ($item['nombres'] ?? '') . ' ' . ($item['paterno'] ?? '') . ' ' . ($item['materno'] ?? ''),
                            'tablas' => [
                                'alumnos' => $existeEnAlumnos,
                                'matriz' => $existeEnMatriz,
                                'datos_ingreso' => $existeEnDatosIngreso
                            ]
                        ];

                        // Si el usuario NO indicó que se ignoren, no procesar este registro
                        if (!$ignorar_duplicados) {
                            continue;
                        }
                    }

                    // Crear usuario
                    $usuario = Usuario::create([
                        'email' => $item['codigo'],
                        'password' => Hash::make($item['dni']),
                        'rol' => 5,
                        'estado' => 1,
                        'estado_contraseña' => 1,
                        'programa_id' => $item['id_programa'],
                        'id_escuela' => $item['id_escuela']
                    ]);

                    // Crear alumno
                    $alumno = new Alumno([
                        'codigo' => $item['codigo'],
                        'dni' => $dni,
                        'codigo_est' => $item['codigo_est'],
                        'paterno' => $item['paterno'],
                        'materno' => $item['materno'],
                        'nombres' => $item['nombres'],
                        'sexo' => $item['sexo'],
                        'email' => $item['email'],
                        'f_nacimiento' => $item['f_nacimiento'],
                        'ubigeo_nacimiento' => $item['ubigeo_nacimiento'],
                        'estado_civil' => $item['estado_civil'],
                        'anio_egreso' => $item['anio_egreso'],
                        'tipo_colegio' => $item['tipo_colegio'],
                        'nombre_colegio' => $item['nombre_colegio'],
                        'ubigeo_colegio' => $item['ubigeo_colegio'],
                        'apto' => $item['apto'],
                        'direccion' => $item['direccion'],
                        'telefono' => $item['telefono'],
                        'usuario_id' => $usuario->id
                    ]);
                    $alumno->save();

                    // Crear registro en matriz
                    Matriz::create([
                        'dni' => $dni,
                        'codigo_est' => $item['codigo_est'],
                        'C1' => $item['C1'] ?? 0,
                        'C2' => $item['C2'] ?? 0,
                        'C3' => $item['C3'] ?? 0,
                        'C4' => $item['C4'] ?? 0,
                        'C5' => $item['C5'] ?? 0,
                        'C6' => $item['C6'] ?? 0,
                        'C7' => $item['C7'] ?? 0,
                        'C8' => $item['C8'] ?? 0,
                        'C9' => $item['C9'] ?? 0,
                        'C10' => $item['C10'] ?? 0,
                        'C11' => $item['C11'] ?? 0,
                        'C1_R' => $item['C1_R'] ?? 0,
                        'C2_R' => $item['C2_R'] ?? 0,
                        'C3_R' => $item['C3_R'] ?? 0,
                        'C4_R' => $item['C4_R'] ?? 0,
                        'C5_R' => $item['C5_R'] ?? 0,
                        'C6_R' => $item['C6_R'] ?? 0,
                        'C7_R' => $item['C7_R'] ?? 0,
                        'C8_R' => $item['C8_R'] ?? 0,
                        'C9_R' => $item['C9_R'] ?? 0,
                        'C10_R' => $item['C10_R'] ?? 0,
                        'C11_R' => $item['C11_R'] ?? 0,
                        'nivelar' => $item['nivelar'] ?? 0,
                        'no_nivelar' => $item['no_nivelar'] ?? 0
                    ]);

                    // Crear registro en datos_ingreso
                    DatoIngreso::create([
                        'dni' => $dni,
                        'codigo_est' => $item['codigo_est'],
                        'anio' => $item['anio'] ?? date('Y'),
                        'semestre' => $item['semestre'] ?? '-',
                        'f_examen' => $item['f_examen'] ?? now(),
                        't_examen' => $item['t_examen'] ?? 'Ordinario',
                        'puntaje_examen' => $item['puntaje_examen'] ?? '0',
                        'modalidad' => $item['modalidad'] ?? 'Regular',
                        'id_programa' => $item['id_programa'],
                        'observacion' => $item['observacion'] ?? ''
                    ]);

                    $reporte['registros_exitosos']++;

                } catch (\Exception $e) {
                    $reporte['errores'][] = $index + 1;
                    $reporte['detalle_errores'][] = [
                        'linea' => $index + 1,
                        'dni' => $dni ?? 'No identificado',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ];
                    Log::error("Error al importar estudiante en línea " . ($index + 1) . ": " . $e->getMessage());
                }
            }

            DB::commit();

            return response()->json([
                'estado' => true,
                'mensaje' => 'Importación completada con el siguiente reporte',
                'data' => $reporte
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'estado' => false,
                'mensaje' => 'Error general al importar: ' . $e->getMessage(),
                'reporte' => $reporte
            ], 500);
        }
    }

public function show(Request $request, string $dni)
    {
        // 1) Validación
        if (!preg_match('/^\d{8}$/', $dni)) {
            throw ValidationException::withMessages([
                'dni' => 'El DNI debe tener 8 dígitos.',
            ]);
        }

        // 2) (Opcional) Cachear 10 minutos para no golpear el externo
        $cacheKey = "dni:$dni";
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($dni) {

            // 3) Parámetros base / headers
            $base = rtrim(config('services.upeu.base_url', env('UPEU_BASE_URL', 'https://api-lamb-academic.upeu.edu.pe')), '/');

            $headers = [
                'accept'     => 'application/json, text/plain, */*',
                'origin'     => config('services.upeu.origin', env('UPEU_ORIGIN', 'https://lamb-unap.edu.pe')),
                'referer'    => config('services.upeu.referer', env('UPEU_REFERER', 'https://lamb-unap.edu.pe/')),
                'user-agent' => 'Mozilla/5.0',
            ];

            $client = Http::withHeaders($headers)->timeout(12);

            // 4) Endpoints (los 2 que pasaste en tu cURL)
            $res1 = $client->get($base . '/resources/api/resources/person-lamb/buscar-persona', [
                'num_documento' => $dni
            ]);

            $res2 = $client->get($base . '/resources/api/resources/searchdocument/dni', [
                'dni' => $dni,
                'url' => 'https://lamb-admission.upeu.edu.pe/public/pages/form',
            ]);

            // 5) Si ambos fallan, propagamos error "Bad Gateway"
            if ($res1->failed() && $res2->failed()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'No se pudo consultar el servicio externo',
                    'errors' => [
                        'endpoint1' => $res1->status(),
                        'endpoint2' => $res2->status(),
                    ],
                ], 502);
            }

            // 6) Respuesta combinada
            return response()->json([
                'ok' => true,
                'dni' => $dni,
                'fuente' => 'api-lamb-unap.edu.pe',
                'person' => $res1->ok() ? $res1->json() : null,
                'document' => $res2->ok() ? $res2->json() : null,
            ]);
        });
    }

}
