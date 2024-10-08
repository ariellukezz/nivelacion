<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Programa;
use App\Models\Escuela;
use App\Models\Rol;
use App\Models\Competencia;


use Inertia\Inertia;

class DataController extends Controller
{

  public function getProgramas(Request $request){

    $query_where = [];
    $res = Programa::select(
        'id as value', 'programa as label'
    )
    ->where($query_where)
    ->where(function ($query) use ($request) {
        return $query
            ->orWhere('programa.programa', 'LIKE', '%' . $request->term . '%');
    })->orderBy('programa.id', 'ASC')
    ->paginate(50);

    $this->response['estado'] = true;
    $this->response['datos'] = $res;
    return response()->json($this->response, 200);

  }

  public function getProgramasEscuela(Request $request){

    $query_where = [];
    $res = Programa::select(
        'id as value', 'programa as label'
    )
    ->where('id_escuela','=',$request->id_escuela)
    ->where($query_where)
    ->where(function ($query) use ($request) {
        return $query
            ->orWhere('programa.programa', 'LIKE', '%' . $request->term . '%');
    })->orderBy('programa.id', 'ASC')
    ->paginate(50);

    $this->response['estado'] = true;
    $this->response['datos'] = $res;
    return response()->json($this->response, 200);

  }

  public function getRoles(Request $request){

    $query_where = [];
    $res = Rol::select(
        'id as value', 'nombre as label'
    )
    ->where($query_where)
    ->where(function ($query) use ($request) {
        return $query
            ->orWhere('nombre', 'LIKE', '%' . $request->term . '%');
    })->orderBy('rol.id', 'ASC')
    ->paginate(50);

    $this->response['estado'] = true;
    $this->response['datos'] = $res;
    return response()->json($this->response, 200);

  }

  public function getEscuelas(Request $request){
    $res = Escuela::select('escuela.id','escuela.nombre as escuela', 'programa.facultad', 'escuela.filial', 'programa.area')
    ->join('programa','programa.id_escuela','escuela.id')
    ->where('escuela.id','=',auth()->user()->id_escuela)
    ->where(function ($query) use ($request) {
        return $query
            ->orWhere('escuela.nombre', 'LIKE', '%' . $request->term . '%');
    })->distinct()
    ->paginate(100);

    $this->response['estado'] = true;
    $this->response['datos'] = $res;
    return response()->json($this->response, 200);

  }

  public function getCompetencias(Request $request){

    $query_where = [];
    $res = Competencia::select(
        'id as value', 'nombre as label'
    )
    ->where($query_where)
    ->where(function ($query) use ($request) {
        return $query
            ->orWhere('nombre', 'LIKE', '%' . $request->term . '%');
    })->orderBy('id', 'ASC')
    ->paginate(20);

    $this->response['estado'] = true;
    $this->response['datos'] = $res;
    return response()->json($this->response, 200);

  }








}
