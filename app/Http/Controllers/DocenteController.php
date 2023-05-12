<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Docente;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use DB;


class DocenteController extends Controller
{
    
    public function index()
    {
        return Inertia::render('Tutores/index');
    }

    public function getDocentes(Request $request){

        $res = Docente::select('id', 'tipo_doc','nro_doc', 'nombres', 'paterno', 'materno', 'telefono', 'email', 'direccion', 'f_nac', 'sexo', 'estado')
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('docente.nombres', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.paterno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.materno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.nro_doc', 'LIKE', '%' . $request->term . '%');
        })->orderBy('docente.id', 'DESC')
        ->paginate(10);
    
        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
      
    }


    public function save(Request $request ) {

        $docente = null;
        if (!$request->id) {

            $usuario = Usuario::create([
                'email' => $request->correo,
                'password' => Hash::make($request->nro_doc),
                'rol' => 4,
                'estado' => 1,
                'estado_contraseña' => 1,
                'id_usuario' => auth()->id()
            ]);

            $docente = Docente::create([
                'tipo_doc' => $request->tipo_doc,
                'nro_doc' => $request->nro_doc,
                'nombres' => $request->nombres,
                'paterno' => $request->primer_apellido,
                'materno' => $request->segundo_apellido,
                'telefono' => $request->celular,
                'email' => $request->correo,
                'direccion' => $request->direccion,
                'f_nac' => $request->fecha,
                'sexo' => $request->sexo,
                'estado' => $request->estado,
                'usuario_id' => $usuario->id, 
                'id_usuario' => auth()->id()

            ]);
            $this->response['tipo'] = 'success';
            $this->response['titulo'] = 'REGISTRO NUEVO';
            $this->response['mensaje'] = 'Docente '.$docente->nombres.' registrado con exito';
            $this->response['estado'] = true;
            $this->response['datos'] = $docente;
        } else {

            $docente = Docente::find($request->id);
            $docente->tipo_doc = $request->tipo_doc;
            $docente->nro_doc= $request->nro_doc;
            $docente->nombres= $request->nombres;
            $docente->paterno= $request->primer_apellido;
            $docente->materno = $request->segundo_apellido;
            $docente->email = $request->correo;
            $docente->direccion = $request->direccion;
            $docente->f_nac = $request->fecha;
            $docente->sexo = $request->sexo;
            $docente->estado = $request->estado;
            $docente->save();
            $this->response['tipo'] = 'info';
            $this->response['titulo'] = '!REGISTRO MODIFICADO!';
            $this->response['mensaje'] = 'Docente '.$docente->nombres.' acaba de ser modificado.';
            $this->response['estado'] = true;
            $this->response['datos'] = $docente;
        }

        return response()->json($this->response, 200);
    }

    
    public function delete($id){
        $docente = Docente::find($id);
        $p = $docente;
        $docente->delete();

        $this->response['tipo'] = 'error';
        $this->response['titulo'] = '!REGISTRO ELIMINADO!';
        $this->response['mensaje'] = 'El Docente '.$p->nombre.' acaba de ser eliminado.';
        $this->response['estado'] = true;
        $this->response['datos'] = $p;
        return response()->json($this->response, 200);
    }

    // DOCENTE 
    public function dashboardDocente()
    {
        return Inertia::render('Docente/Dashboard/index');
    }


}
