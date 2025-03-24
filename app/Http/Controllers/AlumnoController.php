<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Usuario;
use App\Models\Alumno;
use Illuminate\Support\Facades\Hash;
use DB;

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


        $query_where = [];

        if ($request->programa) array_push($query_where,[DB::raw('programa.id'), '=', $request->programa]);
        $id_competencia = $request->curso;
        $id_escuela = $request->escuela;
        $competencia = "";
        if( $id_competencia === 1 ) { $competencia = "C1_R";}
        if( $id_competencia === 2 ) { $competencia = 'C2_R';}
        if( $id_competencia === 3 ) { $competencia = 'C3_R';}
        if( $id_competencia === 4 ) { $competencia = 'C4_R';}
        if( $id_competencia === 5 ) { $competencia = 'C5_R';}
        if( $id_competencia === 6 ) { $competencia = 'C6_R';}
        if( $id_competencia === 7 ) { $competencia = 'C7_R';}
        if( $id_competencia === 8 ) { $competencia = 'C8_R';}
        if( $id_competencia === 9 ) { $competencia = 'C9_R';}
        if( $id_competencia === 10 ) { $competencia = 'C10_R';}
        if( $id_competencia === 11 ) { $competencia = 'C11_R';}
        $res = DB::table('matriz')
        ->join('datos_ingreso', 'matriz.dni', '=', 'datos_ingreso.dni')
        ->join('competencia_programa', 'datos_ingreso.id_programa', '=', 'competencia_programa.id_programa')
        ->join('estudiante', 'estudiante.dni', '=', 'datos_ingreso.dni')
        ->join('programa', 'programa.id', '=', 'datos_ingreso.id_programa')
        ->where($query_where)
        ->whereIn('datos_ingreso.dni', function($query) use ($id_escuela) {
            $query->select('estudiante.dni')
                ->from('estudiante')
                ->join('datos_ingreso', 'estudiante.dni', '=', 'datos_ingreso.dni')
                ->join('programa', 'programa.id', '=', 'datos_ingreso.id_programa')
                ->join('escuela', 'escuela.id', '=', 'programa.id_escuela')
                ->where('escuela.id', $id_escuela);
        })
        ->where('competencia_programa.id_competencia', $id_competencia)
        ->where('matriz.' . $competencia, '<=', 10.49)
        ->select('estudiante.id', 'programa.programa', 'datos_ingreso.semestre', 'estudiante.dni', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno')
        ->distinct() // Se añade distinct para eliminar registros duplicados
        ->get();

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }


    public function getAlumnosRegistroSSSS(Request $request){

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


    public function  excelEstudiante(Request $request){

        foreach ( $request->datos as  $item) {

            $usuario = Usuario::create([
                // 'email' => $item['email'],
                'email' => $item['codigo'],
                'password' => Hash::make($item['dni']),
                'rol' => 5,
                'estado' => 1,
                'estado_contraseña' => 1,
                'programa_id' => $item['id_programa'],
                'id_escuela' => $item['id_escuela']
            ]);

            $alumno = new Alumno([
                'codigo' => $item['codigo'],
                'dni' => $item['dni'],
                'paterno' => $item['paterno'],
                'materno' => $item['materno'],
                'nombres' => $item['nombres'],
                'sexo' => $item['sexo'],
                'email' => $item['email'],
                // 'f_nacimiento' => $item['f_nacimiento'],
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
        }

       $this->response['estado'] = true;
       return response()->json($this->response, 200);

    }







}
