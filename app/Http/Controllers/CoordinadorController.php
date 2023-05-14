<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Docente;
use App\Models\Usuario;
use App\Models\Escuela;
use App\Models\Coordinador;
use App\Models\Alumno;
use Illuminate\Support\Facades\Hash;
use DB;


class CoordinadorController extends Controller
{
    
    public function getEscuelas(Request $request){

        $res = Escuela::select(
            'id as value', 'nombre as label' 
        )
        ->where('id','=',auth()->user()->id_escuela)
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('nombre', 'LIKE', '%' . $request->term . '%');
        })->orderBy('id', 'ASC')
        ->paginate(50);
    
        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
      
      }

    public function getCoordinadores(Request $request){

        $res = Coordinador::select(
            'coordinador.id', 'coordinador.dni','coordinador.nombres', 'coordinador.apellidos', 'coordinador.celular', 'coordinador.correo', 
            'escuela.id as id_escuela', 'escuela.nombre as escuela', 
            'users.estado as estado',)
        ->join('escuela','escuela.id','coordinador.id_escuela')
        ->join('users','users.id','coordinador.usuario_id')
        ->where('users.rol','=',2)
        ->where('coordinador.id_usuario','=', auth()->id())
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('coordinador.nombres', 'LIKE', '%' . $request->term . '%')
                ->orWhere('coordinador.apellidos', 'LIKE', '%' . $request->term . '%')
                ->orWhere('coordinador.dni', 'LIKE', '%' . $request->term . '%');
        })->orderBy('coordinador.id', 'DESC')
        ->paginate(10);
    
        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
      
    }


    public function save(Request $request ) {

        $coordinador = null;
        if (!$request->id) {

            $usuario = Usuario::create([
                'email' => $request->email,
                'password' => Hash::make($request->dni),
                'rol' => 2,
                'estado' => 1,
                'estado_contraseña' => 1,
                'id_escuela' => $request->escuela,
                'id_usuario' => auth()->id()
            ]);

            $coordinador = Coordinador::create([
                'dni' => $request->dni,
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'celular' => $request->celular,
                'correo' => $request->email,
                'id_escuela' => $request->escuela,
                'usuario_id' => $usuario->id, 
                'id_usuario' => auth()->id()
            ]);
            $this->response['tipo'] = 'success';
            $this->response['titulo'] = 'REGISTRO NUEVO';
            $this->response['mensaje'] = 'Coordinador '.$coordinador->nombres.' registrado con exito';
            $this->response['estado'] = true;
            $this->response['datos'] = $coordinador;
        } else {

            $coordinador = Coordinador::find($request->id);
            $coordinador->dni = $request->dni;
            $coordinador->nombres= $request->nombres;
            $coordinador->apellidos = $request->apellidos;
            $coordinador->celular = $request->celular;
            $coordinador->correo = $request->email;
            $coordinador->id_escuela = $request->escuela;
            $coordinador->save();
            $us = Usuario::find($coordinador->usuario_id);
            $us->email = $request->email;
            $us->estado = $request->estado;
            $us->id_escuela = $request->escuela;
            $us->save(); 

            $this->response['tipo'] = 'info';
            $this->response['titulo'] = '!REGISTRO MODIFICADO!';
            $this->response['mensaje'] = 'Docente '.$coordinador->nombres.' acaba de ser modificado.';
            $this->response['estado'] = true;
            $this->response['datos'] = $coordinador;
        }

        return response()->json($this->response, 200);
    }


// SELECT datos_ingreso.dni, datos_ingreso.id_programa, competencia_programa.id_competencia from matriz
// JOIN datos_ingreso ON matriz.dni = datos_ingreso.dni
// JOIN competencia_programa ON datos_ingreso.id_programa = competencia_programa.id_programa
// WHERE datos_ingreso.dni  IN  (
// SELECT estudiante.dni FROM estudiante
// JOIN datos_ingreso ON estudiante.dni = datos_ingreso.dni
// JOIN programa ON programa.id = datos_ingreso.id_programa
// JOIN escuela ON escuela.id = programa.id_escuela
// WHERE escuela.id = 26) AND competencia_programa.id_competencia = 1 
// AND matriz.C1_R <= 10.49

// SELECT * FROM estudiante
// JOIN datos_ingreso ON estudiante.dni = datos_ingreso.dni
// JOIN programa ON programa.id = datos_ingreso.id_programa
// JOIN escuela ON escuela.id = programa.id_escuela
// WHERE escuela.id = 26
    public function getAlumnos(Request $request){

        $consulta = ['estudiante.id', 'estudiante.dni', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno' , 'estudiante.sexo', 'datos_ingreso.t_examen as tipo_examen', 'programa.programa'];
        if($request->codigo == true) { array_push($consulta,'estudiante.codigo'); }
        if($request->telefono == true) { array_push($consulta,'estudiante.telefono'); }
        if($request->colegio == true) { array_push($consulta,'estudiante.nombre_colegio as colegio'); }
        if($request->tipo_colegio == true) { array_push($consulta,'estudiante.tipo_colegio'); }
        if($request->estado_civil == true) { array_push($consulta,'estudiante.estado_civil'); }
        if($request->area == true) { array_push($consulta,'programa.area'); }
        if($request->modalidad == true) { array_push($consulta,'datos_ingreso.modalidad'); }
        // array_push($consulta,$dni);
        // array_push($consulta,$nombres);

        $res = Alumno::select(
            $consulta
        )
        ->join('datos_ingreso','estudiante.dni','datos_ingreso.dni')
        ->join('programa','programa.id','datos_ingreso.id_programa')
        ->join('escuela','escuela.id','programa.id_escuela')
        ->where('escuela.id','=',auth()->user()->id_escuela)
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('estudiante.nombres', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.paterno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.materno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.dni', 'LIKE', '%' . $request->term . '%')
                ->orWhere('programa.programa', 'LIKE', '%' . $request->term . '%')
                ->orWhere('datos_ingreso.t_examen', 'LIKE', '%' . $request->term . '%')
                ->orWhere('programa.area', 'LIKE', '%' . $request->term . '%');
        })->orderBy('estudiante.id', 'DESC')
        ->paginate(200);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
    
    }

    public function delete($id){
        $coordinador = Coordinador::find($id);
        $p = $coordinador;
        $user = Usuario::find($p->usuario_id);
        $coordinador->delete();
        $user->delete();

        $this->response['tipo'] = 'error';
        $this->response['titulo'] = '!REGISTRO ELIMINADO!';
        $this->response['mensaje'] = 'El Coordinador '.$p->nombre.' acaba de ser eliminado.';
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
