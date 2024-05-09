<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\CursoDetalle;
use App\Models\Alumno;
use App\Models\Documento;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;


class SupervisorController extends Controller
{
//     public function getDocumentos(Request $request) {

//         $query_where = [];

//         if ($request->programa) array_push($query_where,[DB::raw('documento.id_programa'), '=', $request->programa]);
//         $res = Documento::select(
//             'documento.tipo',
//             'escuela.nombre AS escuela', 'escuela.nombre_corto', 'documento.fecha_subida', 'users.nombres AS username', 'users.apellidos AS userlastname')
//         ->join('escuela','documento.id_escuela', 'escuela.id')
//         ->join('users','users.id', 'documento.id_usuario')
//         ->where($query_where)
//         ->where(function ($query) use ($request) {
//             return $query
//                 ->orWhere('escuela.nombre', 'LIKE', '%' . $request->term . '%')
//                 ->orWhere('escuela.tipo', 'LIKE', '%' . $request->term . '%')
//                 ->orWhere('users.nombres', 'LIKE', '%' . $request->term . '%');
//         })
//         ->paginate($request->paginashoja);

//         $this->response['estado'] = true;
//         $this->response['datos'] = $res;
//         return response()->json($this->response, 200);

//     }

    public function getDocumentos(Request $request) {
        // Definir las condiciones iniciales
        $conditions = [];

        if ($request->programa) array_push($conditions,[DB::raw('documento.id_escuela'), '=', $request->programa]);
        // Filtrar por programa si está presente en la solicitud
        // if ($request->has('programa')) {
        //     $conditions[] = ['documento.id_escuela', '=', $request->programa];
        // }

        // Realizar la consulta de manera más legible
        $query = Documento::select(
            'documento.id',
            'documento.tipo',
            'escuela.nombre AS escuela',
            'escuela.nombre_corto',
            'documento.fecha_subida',
            'users.nombres AS username',
            'users.apellidos AS userlastname',
            'documento.url',
            'documento.aceptado',
            'documento.obser'
        )
        ->join('escuela', 'documento.id_escuela', '=', 'escuela.id')
        ->join('users', 'users.id', '=', 'documento.id_usuario')
        ->where(function ($query) use ($request) {
            $query->where('escuela.nombre', 'LIKE', '%' . $request->term . '%')
                ->orWhere('documento.tipo', 'LIKE', '%' . $request->term . '%')
                ->orWhere('users.nombres', 'LIKE', '%' . $request->term . '%');
        });

        // Aplicar condiciones adicionales si las hay
        if (!empty($conditions)) {
            $query->where($conditions);
        }

        // $res = $query->paginate($request->paginashoja);
        $res = $query->paginate(200);

        // Devolver una respuesta JSON
        return response()->json([
            'estado' => true,
            'datos' => $res,
        ], 200);
    }

    public function ObservarDocumento(Request $request){

        $doc = Documento::find($request->id);
        $doc->obser = $request->obser;
        $doc->save();

        $this->response['tipo'] = 'info';
        $this->response['titulo'] = '!REGISTRO MODIFICADO!';
        $this->response['mensaje'] = ' ';
        $this->response['estado'] = true;
        $this->response['datos'] = $doc;

        return response()->json($this->response, 200);

    }

    public function cambiarEstado(Request $request){

        $doc = Documento::find($request->id);
        if($doc->aceptado  == 0 || $doc->aceptado == null ){
            $doc->aceptado = 1;
        }else{
            $doc->aceptado = 0;
        }
        $doc->save();
        $this->response['estado'] = true;
        $this->response['datos'] = $doc;

        return response()->json($this->response, 200);

    }





}
