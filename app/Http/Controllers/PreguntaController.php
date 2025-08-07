<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use App\Models\Pregunta;
use App\Models\Alumno;
use App\Models\Docente;
use App\Models\EncargadoSistema;
use App\Models\Curso;
use App\Models\Respuesta;
use App\Models\CursoDetalle;
use DB;
use Illuminate\Support\Facades\Auth;

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




public function getEncargados(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $term = $request->input('term');
        $encargados = EncargadoSistema::query()
            ->where('usuario_id', Auth::id()) // Filtrar por usuario autenticado
            ->when($term, function ($query, $term) {
                return $query->where('nombres', 'like', '%' . $term . '%')
                             ->orWhere('apellidos', 'like', '%' . $term . '%')
                             ->orWhere('dni', 'like', '%' . $term . '%')
                             ->orWhere('correo', 'like', '%' . $term . '%');
            })
            ->get();

        return response()->json($encargados);
    }

    public function saveEncargado(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $validated = $request->validate([
            'dni' => 'required|string|max:20|unique:encargados_sistema,dni,' . ($request->id_encargado ?? 'NULLvijay') . ',id_encargado',
            'nombres' => 'required|string|max:70',
            'apellidos' => 'required|string|max:80',
            'correo' => 'required|email|max:150|unique:encargados_sistema,correo,' . ($request->id_encargado ?? 'NULL') . ',id_encargado',
            'num_celular' => 'nullable|string|max:20',
            'cargo' => 'required|string|max:100',
            'fecha_designacion' => 'nullable|date',
            'fecha_fin_designacion' => 'nullable|date|after_or_equal:fecha_designacion',
            'observaciones' => 'nullable|string|max:500',
            'estado' => 'nullable|boolean',
        ]);

        $validated['usuario_id'] = Auth::id(); // Asignar usuario_id del usuario autenticado

        if ($request->id_encargado) {
            $encargado = EncargadoSistema::where('id_encargado', $request->id_encargado)
                ->where('usuario_id', Auth::id()) // Asegurar que el usuario solo edite sus propios encargados
                ->first();
            if (!$encargado) {
                return response()->json(['message' => 'Encargado no encontrado o no pertenece al usuario'], 404);
            }
            $encargado->update($validated);
        } else {
            $encargado = EncargadoSistema::create($validated);
        }

        return response()->json(['message' => 'Encargado guardado con éxito', 'data' => $encargado], 200);
    }

    public function deleteEncargado($id)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Usuario no autenticado'], 401);
        }

        $encargado = EncargadoSistema::where('id_encargado', $id)
            ->where('usuario_id', Auth::id()) // Asegurar que el usuario solo elimine sus propios encargados
            ->first();
        if (!$encargado) {
            return response()->json(['message' => 'Encargado no encontrado o no pertenece al usuario'], 404);
        }

        $encargado->delete();
        return response()->json(['message' => 'Encargado eliminado con éxito'], 200);
    }

}
