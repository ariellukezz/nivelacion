<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Alumno;

class AlumnoController extends Controller
{
    public function index()
    {
        return Inertia::render('Alumno/index');
    }


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

            // 'programa.id as id_programa', 'programa.programa as programa',
            // 'rol.id as id_rol', 'rol.nombre as rol'  
        )
        ->leftjoin('datos_ingreso','estudiante.dni','datos_ingreso.dni')
        ->leftjoin('programa','programa.id','datos_ingreso.id_programa')
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
        ->paginate(10);
    
        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
      
    }



    public function getAlumnosRegistro(Request $request){

        $res = Alumno::select(
            'estudiante.id',
            'estudiante.dni',
            'estudiante.nombres',
            'estudiante.paterno',
            'estudiante.materno'
        )
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('estudiante.nombres', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.paterno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.materno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.dni', 'LIKE', '%' . $request->term . '%');
        })->orderBy('estudiante.id', 'DESC')
        ->paginate(10);
    
        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);
      
    }






}
