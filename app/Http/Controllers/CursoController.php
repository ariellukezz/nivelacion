<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\CursoDetalle;
use App\Models\Alumno;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;


class CursoController extends Controller
{



    //DOCENTE 
    public function cursoDocente()
    {
        return Inertia::render('Docente/Cursos/cursos');
    }


    public function getCursos(Request $request){

        $res = Curso::select('curso.id', 'curso.nombre','curso.grupo', 'curso.escuela', 'curso.estado', 'curso.editable')
        ->join('docente','curso.id_docente','docente.id')
        ->where('curso.estado',"=",1)
        ->where('docente.usuario_id',"=", auth()->user()->id)
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('curso.nombre', 'LIKE', '%' . $request->term . '%')
                ->orWhere('curso.grupo', 'LIKE', '%' . $request->term . '%')
                ->orWhere('curso.escuela', 'LIKE', '%' . $request->term . '%');
        })->orderBy('curso.id', 'DESC')
        ->paginate(10);
    
        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
      
    }

    public function getAlumnosXCurso(Request $request){

        $comp = Curso::find($request->curso);

        $res = CursoDetalle::select(
            'curso_detalle.id','curso_detalle.nota', 'curso_detalle.condicion', 'curso_detalle.editable',
            'estudiante.dni','estudiante.nombres', 'estudiante.paterno', 'estudiante.materno', 'matriz.C'.$comp->id_competencia.'_R as ant'
            )
        ->join('estudiante','estudiante.id','curso_detalle.id_alumno')
        ->join('matriz','matriz.dni','estudiante.dni')
        ->join('curso','curso.id','curso_detalle.id_curso')
        ->where('curso_detalle.id_curso',"=", $request->curso)
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('estudiante.dni', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.nombres', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.paterno', 'LIKE', '%' . $request->term . '%');
        })->orderBy('estudiante.paterno', 'ASC')
        ->paginate(200);
    
        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
      
    }


    public function updateNota(Request $request ) {

        $curso_detalle = CursoDetalle::find($request->id_detallecurso);
        $curso_detalle->nota = $request->nota;
        if( $request->nota >= 10.5){
            $curso_detalle->condicion = 1;
        }else {
            $curso_detalle->condicion = 0;
        }

        $curso_detalle->save();
        $this->response['tipo'] = 'info';
        $this->response['titulo'] = '!REGISTRO MODIFICADO!';
        $this->response['mensaje'] = 'La nota acaba de ser modificado.';
        $this->response['estado'] = true;
        $this->response['datos'] = $curso_detalle;

        return response()->json($this->response, 200);
    }

    //END DOCENTE
    


    //ALUMNO 

    public function getNotasByAlumno(Request $request ) {

        $res = Alumno::select('estudiante.id', 'curso.nombre','curso.grupo', 'curso.escuela', 'curso_detalle.nota')
        ->join('curso_detalle','curso_detalle.id_alumno','estudiante.id')
        ->join('curso','curso.id','curso_detalle.id_curso')
        ->join('users','users.id','estudiante.usuario_id')
        ->where('curso.estado',"=",1)
        ->where('estudiante.usuario_id',"=", auth()->user()->id)
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('curso.nombre', 'LIKE', '%' . $request->term . '%');
        })->orderBy('curso.id', 'DESC')
        ->paginate(10);
    
        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
    }


    // SELECT curso_detalle.encuesta, curso.id, curso.nombre FROM curso
    // JOIN curso_detalle ON curso_detalle.id_curso = curso.id
    // WHERE curso_detalle.id_alumno = 1000
    

    public function getCursosEncuesta() {

        $alumno = DB::select('SELECT id from estudiante where usuario_id = '.auth()->user()->id );

        $res = Curso::select('curso_detalle.encuesta', 'curso.id', 'curso.nombre')
        ->join('curso_detalle','curso_detalle.id_curso','curso.id')
        ->where('curso.estado',"=",1)
        ->where('curso_detalle.id_alumno',"=", $alumno[0]->id)
        ->get();
    
        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
    }


    
    //






}
