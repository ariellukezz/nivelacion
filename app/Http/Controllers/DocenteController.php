<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Docente;
use App\Models\Usuario;
use App\Models\Curso;
use App\Models\Programa;
use App\Models\CursoDetalle;
use App\Models\DocenteCompetencia;
use Illuminate\Support\Facades\Hash;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Http;
use DB;

class DocenteController extends Controller
{

    public function index()
    {
        return Inertia::render('Tutores/index');
    }

    public function getDocentes(Request $request){

        $res = Docente::select('docente.id', 'docente.tipo_doc','docente.nro_doc', 'docente.nombres', 'docente.paterno', 'docente.materno', 'docente.telefono',
        'docente.email', 'docente.direccion', 'docente.f_nac', 'docente.sexo', 'docente.estado'
        )
        ->join('users','users.id','docente.usuario_id')
        ->where('users.id_escuela','=',auth()->user()->id_escuela)
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('docente.nombres', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.paterno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.materno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.nro_doc', 'LIKE', '%' . $request->term . '%');
        })->orderBy('docente.id', 'DESC')
        ->paginate(200);

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
                'id_escuela' => auth()->user()->id_escuela,
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

            foreach($request->competencias as $item){
                $this->saveCompetencias($docente->id, $item);
            }
            $this->response['tipo'] = 'success';
            $this->response['titulo'] = 'REGISTRO NUEVO';
            $this->response['mensaje'] = 'Docente '.$docente->nombres.' registrado con exito';
            $this->response['estado'] = true;
            $this->response['datos'] = $docente;
        } else {

            $docente_competencias = DB::select('SELECT id_competencia as c FROM docente_competencia
            WHERE id_docente = '. $request->id);
            // $docente_competencia = $this->convert($docente_competencias);
            $opciones_eliminadas=array_diff($this->convert($docente_competencias), $request->competencias);
            $opciones_nuevas=array_diff($request->competencias, $this->convert($docente_competencias));
            foreach($opciones_nuevas as $item){
                $this->saveCompetencias($request->id, $item);
            }

            foreach($opciones_eliminadas as $item){
                $competencia = DB::select('SELECT id FROM docente_competencia WHERE id_docente = '. $request->id.' AND id_competencia = '.$item );
                $this->deletedocentecompetencia($competencia[0]->id);
            }


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

            // foreach($request->competencias as $item){
            //     if($this->buscarCompetencias($docente_competencias, $item) ){}
            //     $this->saveEditCompetencias($docente->id, $item);
            // }

            $docente->save();
            $usuario = Usuario::find($docente->usuario_id);
            $usuario->email = $request->correo;
            $usuario->save();
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

    public function saveCompetencias($docente, $competencia){
        $docente_competencia = DocenteCompetencia::create([
            'id_competencia' => $competencia,
            'id_docente' => $docente
        ]);
    }

    public function saveEditCompetencias($docente, $competencia){

        $competencias = DB::select('SELECT id, id_competencia, id_docente FROM docente_competencia
        WHERE id_competencia =' .$competencia.' AND id_docente = '.$docente);

        $docente_competencia = DocenteCompetencia::find($competencias[0]->id);
        $docente_competencia->id_competencia = $competencia;
        $docente_competencia->id_docente = $docente;
        $docente_competencia->save();
    }

    public function getCompetenciasByDocente(Request $request){

        $res = DocenteCompetencia::select('docente_competencia.id_competencia')
        ->join('docente','docente_competencia.id_docente','docente.id')
        ->where('docente_competencia.id_docente','=',$request->id_docente)
        ->paginate(10);

        $competencia = [];

        foreach($res as $item ){
            array_push($competencia,$item->id_competencia);
        }

        $this->response['estado'] = true;
        $this->response['datos'] = $competencia;
        return response()->json($this->response, 200);

    }

    public function convert($ar){
        $arr = [];
        foreach($ar as $item ){
            array_push($arr,$item->c);
        }
        return $arr;
    }

    public function deletedocentecompetencia($id){
        $docente_compe = DocenteCompetencia::find($id);
        $docente_compe->delete();
    }


    public function pdf($curso){

        $res = Curso::select(
            'curso.id AS id_curso',
            'curso.nombre AS curso',
            'curso.ciclo',
            'competencia.id AS id_competencia',
            'competencia.nombre AS competencia',
            'docente.nro_doc AS dni_docente',
            'docente.nombres AS nombre',
            'docente.paterno as paterno',
            'docente.materno as materno',
            'curso.grupo',
            'curso.escuela',
            'programa.programa',
            'escuela.filial'
        )
        ->join('competencia','competencia.id','curso.id_competencia')
        ->join('docente','curso.id_docente','docente.id')
        ->join('programa','curso.id_programa','programa.id')
        ->join('escuela', 'programa.id_escuela', '=', 'escuela.id')
        ->where('curso.id','=',$curso)
        ->get();

        $estudiantes = CursoDetalle::select(
            'estudiante.codigo_est', 'datos_ingreso.semestre', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno',
           //bdhh 'estudiante.dni', 'datos_ingreso.semestre', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno',
            'curso_detalle.nota', 'curso_detalle.condicion'
        )
        ->join('estudiante','curso_detalle.id_alumno','estudiante.id')
        ->join('datos_ingreso', 'datos_ingreso.codigo_est', 'estudiante.codigo_est')
      //bdhh  ->join('datos_ingreso', 'datos_ingreso.dni', 'estudiante.dni')
        ->where('curso_detalle.id_curso','=',$curso)
        ->get();

        $data = $res[0];
        $pdf = Pdf::loadView('RepCursoPDF/index', compact('data','estudiantes'));

        return $pdf->download('Reporte.pdf');
        //return $pdf->stream('Reporte.pdf');

    }



    // DOCENTE
    public function dashboardDocente()
    {
        return Inertia::render('Docente/Dashboard/index');
    }


    //GET DATA DE APIBRAYAN
    public function getDataPrisma($dni)
    {
        $url = "https://erpprisma.com/rucdni/l_dni.php?dni=" . $dni;

        $response = Http::get($url);
        $data = explode("|", $response->body());

        $resultado = [
            'codigo' => $data[0],
            'dni' => $data[1],
            'nombre' => $data[2],
            'paterno' => $data[3],
            'materno' => $data[4],
        ];

        return response()->json($resultado);
    }


    public function save2(Request $request ) {

        $esc = Programa::find($request->id_docente);

        $docente = null;
        if (!$request->id) {
            $usuario = Usuario::create([
                'email' => $request->correo,
                'password' => Hash::make($request->nro_doc),
                'rol' => 4,
                'estado' => 1,
                'estado_contraseña' => 1,
                'programa_id' => $request->id_docente,
                'id_escuela' => $esc->id_escuela,
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

            foreach($request->competencias as $item){
                $this->saveCompetencias($docente->id, $item);
            }
            $this->response['tipo'] = 'success';
            $this->response['titulo'] = 'REGISTRO NUEVO';
            $this->response['mensaje'] = 'Docente '.$docente->nombres.' registrado con exito';
            $this->response['estado'] = true;
            $this->response['datos'] = $docente;
        } else {

            $docente_competencias = DB::select('SELECT id_competencia as c FROM docente_competencia
            WHERE id_docente = '. $request->id);
            // $docente_competencia = $this->convert($docente_competencias);
            $opciones_eliminadas=array_diff($this->convert($docente_competencias), $request->competencias);
            $opciones_nuevas=array_diff($request->competencias, $this->convert($docente_competencias));
            foreach($opciones_nuevas as $item){
                $this->saveCompetencias($request->id, $item);
            }

            foreach($opciones_eliminadas as $item){
                $competencia = DB::select('SELECT id FROM docente_competencia WHERE id_docente = '. $request->id.' AND id_competencia = '.$item );
                $this->deletedocentecompetencia($competencia[0]->id);
            }


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

            // foreach($request->competencias as $item){
            //     if($this->buscarCompetencias($docente_competencias, $item) ){}
            //     $this->saveEditCompetencias($docente->id, $item);
            // }

            $docente->save();

            $usuario = Usuario::find($docente->usuario_id);
            $usuario->programa_id = $request->id_docente;
            $usuario->id_escuela = $esc->id_escuela;
            $usuario->email = $request->correo;
            
            $usuario->save();
            $this->response['tipo'] = 'info';
            $this->response['titulo'] = '!REGISTRO MODIFICADO!';
            $this->response['mensaje'] = 'Docente '.$docente->nombres.' acaba de ser modificado.';
            $this->response['estado'] = true;
            $this->response['datos'] = $docente;
        }

        return response()->json($this->response, 200);
    }


    public function getDocentesSuperAdmin(Request $request){

        $res = Docente::select('docente.id', 'docente.tipo_doc', 'docente.nro_doc', 'docente.nombres',
        'docente.paterno', 'docente.materno', 'docente.telefono', 'docente.email',
        'docente.direccion', 'docente.f_nac', 'docente.sexo', 'docente.estado',
        'programa.programa', 'escuela.nombre', 'users.estado_contraseña')
->join('users', 'docente.usuario_id', '=', 'users.id')
->leftJoin('escuela', 'users.id_escuela', '=', 'escuela.id')
->leftJoin('programa', 'users.programa_id', '=', 'programa.id')
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('docente.nombres', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.paterno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.materno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('docente.nro_doc', 'LIKE', '%' . $request->term . '%')
                ->orWhere('users.programa_id', 'LIKE', '%' . $request->term . '%')
                ->orWhere('users.id_escuela', 'LIKE', '%' . $request->term . '%');
        })->orderBy('docente.id', 'DESC')
        ->paginate(200);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }




}
