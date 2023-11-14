<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\CursoDetalle;
use App\Models\Alumno;
use App\Models\Documento;
use Inertia\Inertia;
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
                    ->orWhere('e.materno', 'LIKE', '%' . $term . '%');
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
        'e.nombre as nombre_escuela')
    ->join('users as u', 'd.id_usuario', '=', 'u.id')
    ->join('escuela as e', 'u.id_escuela', '=', 'e.id')

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
        $term = $request->buscar;
    
        $usuarios = DB::table('users as u')
        ->leftJoin('estudiante as e', 'u.id', '=', 'e.usuario_id')
        ->leftJoin('docente as d', 'u.id', '=', 'd.usuario_id')
        ->select(
            'u.nombres',
            'u.apellidos', 
            'u.email', 
            'e.dni', 
            'e.paterno',
            'e.materno', 
            'e.nombres as e_nombres', 
            'e.email as e_email', 
            'd.nro_doc',
            'd.paterno as d_paterno', 
            'd.materno as d_materno', 
            'd.nombres as d_nombres',
            'd.email as d_email')
        ->where(function ($query) use ($term) {
            $query->orWhere('u.nombres', 'LIKE', '%' . $term . '%')
                ->orWhere('u.apellidos', 'LIKE', '%' . $term . '%')
                ->orWhere('u.email', 'LIKE', '%' . $term . '%')
                ->orWhere('e.dni', 'LIKE', '%' . $term . '%')
                ->orWhere('e.paterno', 'LIKE', '%' . $term . '%')
                ->orWhere('e.materno', 'LIKE', '%' . $term . '%')
                ->orWhere('e.nombres', 'LIKE', '%' . $term . '%')
                ->orWhere('e.email', 'LIKE', '%' . $term . '%')
                ->orWhere('d.nro_doc', 'LIKE', '%' . $term . '%')
                ->orWhere('d.paterno', 'LIKE', '%' . $term . '%')
                ->orWhere('d.materno', 'LIKE', '%' . $term . '%')
                ->orWhere('d.nombres', 'LIKE', '%' . $term . '%')
                ->orWhere('d.email', 'LIKE', '%' . $term . '%');
        })
    ->get();
    
    $this->response['estado'] = true;
    $this->response['datos'] = $usuarios;
    return response()->json($this->response, 200);
        }








        public function getCursos(Request $request){

            $query_where = [];
    
            if ($request->competencia !== null) array_push($query_where, ['curso.id_competencia', '=', $request->competencia]);
            
            $res = Curso::select(
                'curso.id', 'curso.nombre',
                'docente.id as id_docente', DB::raw("CONCAT( docente.nombres,' ',docente.paterno,' ',docente.materno) as docente"),
                'competencia.id as id_competencia', 'competencia.nombre as competencia',
                'curso.grupo','curso.escuela', 'curso.estado'
            )
            ->join('docente','docente.id','curso.id_docente')
            ->join('competencia','competencia.id','curso.id_competencia')
            ->where('curso.escuela',"=",$request->escuela)
            #->where('curso.id_usuario',"=",auth()->id())
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
    
      public function getEscuelas(Request $request){
        $res = Escuela::select('escuela.id','escuela.nombre as escuela', 'programa.facultad', 'programa.area')
        ->join('programa','programa.id_escuela','escuela.id')
    #    ->where('escuela.id','=',auth()->user()->id_escuela)
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('escuela.nombre', 'LIKE', '%' . $request->term . '%');
        })->distinct()
        ->paginate(100);
    
        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
      
      }
}


