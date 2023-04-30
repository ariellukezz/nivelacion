<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Programa;
use App\Models\Rol;


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

}
