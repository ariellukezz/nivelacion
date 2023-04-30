<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    
    public function index()
    {
        return Inertia::render('Usuarios/index');
    }

    public function getUsuarios(Request $request){
      
        $res = Usuario::select(
            'users.id', 'users.nombres', 'users.apellidos', 'users.email', 'users.estado',
            'programa.id as id_programa', 'programa.programa as programa',
            'rol.id as id_rol', 'rol.nombre as rol'  
        )
        ->leftjoin('programa','users.programa_id','programa.id')
        ->join('rol','users.rol','rol.id')
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('users.nombres', 'LIKE', '%' . $request->term . '%')
                ->orWhere('users.apellidos', 'LIKE', '%' . $request->term . '%')
                ->orWhere('programa.programa', 'LIKE', '%' . $request->term . '%')
                ->orWhere('rol.nombre', 'LIKE', '%' . $request->term . '%');
        })->orderBy('users.id', 'DESC')
        ->paginate(10);
    
        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
      
    }


    public function save(Request $request ) {

        $usuario = null;
        if (!$request->id) {
            $usuario = Usuario::create([
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'programa_id' => $request->programa,
                'rol' => $request->rol,
                'estado' => $request->estado,
                'estado_contraseña' => 1,
                'id_usuario' => auth()->id()
            ]);
            $this->response['tipo'] = 'success';
            $this->response['titulo'] = 'REGISTRO NUEVO';
            $this->response['mensaje'] = 'Usuario '.$usuario->nombres.' creado con exito';
            $this->response['estado'] = true;
            $this->response['datos'] = $usuario;
        } else {

            $usuario = Usuario::find($request->id);
            $usuario->nombres= $request->nombres;
            $usuario->apellidos = $request->apellidos;
            $usuario->email = $request->email;
            $usuario->estado = $request->estado;
            $usuario->programa_id = $request->programa;
            $usuario->rol = $request->rol;
            $usuario->id_usuario = auth()->id();
            $usuario->save();
            $this->response['tipo'] = 'info';
            $this->response['titulo'] = '!REGISTRO MODIFICADO!';
            $this->response['mensaje'] = 'Usuario '.$usuario->nombres.' acaba de ser modificado.';
            $this->response['estado'] = true;
            $this->response['datos'] = $usuario;
        }

    return response()->json($this->response, 200);
  }


  public function delete($id){
    $usuario = Usuario::find($id);
    $p = $usuario;
    $usuario->delete();

    $this->response['tipo'] = 'error';
    $this->response['titulo'] = '!REGISTRO ELIMINADO!';
    $this->response['mensaje'] = 'El Usuario '.$p->nombre.' acaba de ser eliminado.';
    $this->response['estado'] = true;
    $this->response['datos'] = $p;
    return response()->json($this->response, 200);
  }




}
