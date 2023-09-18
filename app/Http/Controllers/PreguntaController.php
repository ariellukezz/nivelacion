<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use App\Models\Pregunta;
use App\Models\Alumno;
use App\Models\Docente;
use App\Models\Curso;
use App\Models\Respuesta;
use App\Models\CursoDetalle;
use DB;

class PreguntaController extends Controller {

    public function getPreguntas($encuesta){
        $res = Pregunta::select('id', 'texto','aspecto', 'id_encuesta')
        ->where('id_encuesta',"=",$encuesta)->get();

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
    }

    public function saveRepuestasPostulante(Request $request){

        $alumno = Alumno::where('usuario_id', auth()->id())
             ->first();

        foreach ($request->preguntas as $index => $pregunta) {
            $curso = Respuesta::create([
                'id_pregunta' => $pregunta['id'],
                'id_alumno' => $alumno->id,
                'id_curso' => $request->curso,
                'respuesta' => $request->respuestas[$index]
            ]);
            if(count($request->respuestas) == $index + 1){
                $curso = Respuesta::create([
                    'id_pregunta' => 8,
                    'id_alumno' => $alumno->id,
                    'id_curso' => $request->curso,
                    'respuesta' => $request->sugerencia
                ]);
            }
        }
        
        $curso = CursoDetalle::where('id_curso','=', $request->curso)
            ->where('id_alumno','=', $alumno->id)
            ->first();

        $curso->encuesta = 0;
        $curso->save();   


        $this->response['tipo'] = 'success';
        $this->response['titulo'] = '!ENCUESTA GUARDADA!';
        $this->response['mensaje'] = 'Todas las preguntas se registraron con exito.';
        $this->response['estado'] = true;
        return response()->json($this->response, 200);

    }

    public function saveRepuestasDocente(Request $request){

        $docente = Docente::where('usuario_id', auth()->id())
             ->first();

        foreach ($request->preguntas as $index => $pregunta) {
            $curso = Respuesta::create([
                'id_pregunta' => $pregunta['id'],
                'id_alumno' => $docente->id,
                'id_curso' => $request->curso,
                'respuesta' => $request->respuestas[$index]
            ]);
            if(count($request->respuestas) == $index + 1){
                $curso = Respuesta::create([
                    'id_pregunta' => 8,
                    'id_alumno' => $docente->id,
                    'id_curso' => $request->curso,
                    'respuesta' => $request->sugerencia
                ]);
            }
        }

        $curso = Curso::find($request->curso);

        $curso->encuesta = 0;
        $curso->save();   

        $this->response['tipo'] = 'success';
        $this->response['titulo'] = '!ENCUESTA GUARDADA!';
        $this->response['mensaje'] = 'Todas las preguntas se registraron con exito.';
        $this->response['estado'] = true;
        return response()->json($this->response, 200);

    }
 
}
