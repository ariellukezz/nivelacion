<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Usuario;
use App\Models\Alumno;
use App\Models\Docente;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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

  
    public function getUsuarioAdministrador(Request $request){

        $res = DB::select('SELECT users.nombres, programa.escuela, users.estado_contraseña as e_contra  FROM users
        JOIN programa ON programa.id = users.programa_id
        WHERE users.id = '. auth()->user()->id);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }

    public function getUsuarioDocente(Request $request){

        $res = DB::select('SELECT docente.nombres, docente.paterno, docente.materno, programa.escuela,
        users.estado_contraseña as e_contra 
        FROM docente
        JOIN users ON users.id = docente.usuario_id
        JOIN programa ON programa.id IN (SELECT users.programa_id FROM users WHERE users.id = docente.id_usuario)
        WHERE users.id = '. auth()->user()->id);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }

    public function getUsuarioSupervisor(Request $request){

        $res = DB::select('SELECT users.nombres, programa.escuela, users.estado_contraseña as e_contra  FROM users
        JOIN programa ON programa.id = users.programa_id
        WHERE users.id = '. auth()->user()->id);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }

    public function getUsuarioSuperadmi(Request $request){

        $res = DB::select('SELECT users.nombres, programa.escuela, users.estado_contraseña as e_contra  FROM users
        JOIN programa ON programa.id = users.programa_id
        WHERE users.id = '. auth()->user()->id);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }

  //GET USUARIO ESTUDIANTE
    public function getUsuarioEstudiante(Request $request){
        
        $res = Alumno::select('estudiante.nombres', 'estudiante.paterno', 'estudiante.materno', 'programa.escuela', 'users.estado_contraseña as e_contra')
        ->join('users','users.id','estudiante.usuario_id')
        ->join('datos_ingreso','datos_ingreso.dni','estudiante.dni')
        ->join('programa','datos_ingreso.id_programa','programa.id')
        ->where('users.id','=', auth()->user()->id)->get();

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
    
    }

    public function saveNewContra(Request $request){
        $usuario = Usuario::find(auth()->user()->id);
        $usuario->password = Hash::make($request->contra);
        $usuario->estado_contraseña = 0;
        $usuario->save();

        $this->response['tipo'] = 'success';
        $this->response['titulo'] = '!CONTRASEÑA ACTUALIZADA!';
        $this->response['mensaje'] = '';
        $this->response['estado'] = true;
        return response()->json($this->response, 200);

    }


}
