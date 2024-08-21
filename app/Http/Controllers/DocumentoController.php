<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class DocumentoController extends Controller
{

    public function download($uuid)
    {
        $book = Book::where('uuid', $uuid)->firstOrFail();
        $pathToFile = storage_path("app/public/subfolder/$book->id/" . $book->book_image);
        // return response()->download($pathToFile);
        return response()->file($pathToFile);
    }


    public function resolucion(Request $request)
    {
        $escuela = DB::select('SELECT programa.escuela FROM users
        JOIN programa ON users.programa_id = programa.id
        WHERE users.id = ' . auth()->id());

        try{
            if($request->hasFile('img')){
                $file = $request->file('img');
                $file_name =$file->getClientoriginalName();
                $file->move(public_path('documentos/resoluciones/'.$escuela[0]->escuela.'/'), time().'-'.$file_name);

                $doc = Documento::create([
                    'nombre' => $file_name,
                    'url' => 'documentos/resoluciones/'.$escuela[0]->escuela.'/'.time().'-'.$file_name,
                    'fecha_subida' => date('Y-m-d'),
                    'tipo' => 'Resolucion',
                    'periodo' => '2024-II',
                    'id_escuela'=> auth()->user()->id_escuela,
                    'id_usuario' => auth()->id()
                ]);
                return response()->json(['menssje'=>'file upload success'], 200);
            }
        }catch(\Exception $e){
            return response()->json([
                'mssage'=>$e->getMessage()
            ]);
        }

    }

    public function plan(Request $request)
    {
        $escuela = DB::select('SELECT programa.escuela FROM users
        JOIN programa ON users.programa_id = programa.id
        WHERE users.id = ' . auth()->id());

        try{
            if($request->hasFile('img')){
                $file = $request->file('img');
                $file_name =$file->getClientoriginalName();
                $file->move(public_path('documentos/planes/'.$escuela[0]->escuela.'/'), time().'-'.$file_name);

                $doc = Documento::create([
                    'nombre' => $file_name,
                    'url' => 'documentos/planes/'.$escuela[0]->escuela.'/'.time().'-'.$file_name,
                    'fecha_subida' => date('Y-m-d'),
                    'tipo' => 'Plan',
                    'periodo' => '2024-II',
                    'id_escuela'=> auth()->user()->id_escuela,
                    'id_usuario' => auth()->id()
                ]);
                return response()->json(['menssje'=>'Plan Guardado'], 200);
            }
        }catch(\Exception $e){
            return response()->json([
                'mssage'=>$e->getMessage()
            ]);
        }

    }

    public function informe(Request $request)
    {
        $escuela = DB::select('SELECT programa.escuela FROM users
        JOIN programa ON users.programa_id = programa.id
        WHERE users.id = ' . auth()->id());

        try{
            if($request->hasFile('img')){
                $file = $request->file('img');
                $file_name =$file->getClientoriginalName();
                $file->move(public_path('documentos/informes/'.$escuela[0]->escuela.'/'), time().'-'.$file_name);

                $doc = Documento::create([
                    'nombre' => $file_name,
                    'url' => 'documentos/informes/'.$escuela[0]->escuela.'/'.time().'-'.$file_name,
                    'fecha_subida' => date('Y-m-d'),
                    'tipo' => 'Informe',
                    'periodo' => '2024-II',
                    'id_escuela'=> auth()->user()->id_escuela,
                    'id_usuario' => auth()->id()
                ]);
                return response()->json(['menssje'=>'file upload success'], 200);
            }
        }catch(\Exception $e){
            return response()->json([
                'mssage'=>$e->getMessage()
            ]);
        }

    }

    public function dictantes(Request $request)
    {
        $escuela = DB::select('SELECT programa.escuela FROM users
        JOIN programa ON users.programa_id = programa.id
        WHERE users.id = ' . auth()->id());

        try{
            if($request->hasFile('img')){
                $file = $request->file('img');
                $file_name =$file->getClientoriginalName();
                $file->move(public_path('documentos/dictantes/'.$escuela[0]->escuela.'/'), time().'-'.$file_name);

                $doc = Documento::create([
                    'nombre' => $file_name,
                    'url' => 'documentos/dictantes/'.$escuela[0]->escuela.'/'.time().'-'.$file_name,
                    'fecha_subida' => date('Y-m-d'),
                    'tipo' => 'Dictantes',
                    'periodo' => '2024-II',
                    'id_escuela'=> auth()->user()->id_escuela,
                    'id_usuario' => auth()->id()
                ]);
                return response()->json(['menssje'=>'file upload success'], 200);
            }
        }catch(\Exception $e){
            return response()->json([
                'mssage'=>$e->getMessage()
            ]);
        }

    }


    public function otros(Request $request)
    {
        $escuela = DB::select('SELECT programa.escuela FROM users
        JOIN programa ON users.programa_id = programa.id
        WHERE users.id = ' . auth()->id());

        try{
            if($request->hasFile('img')){
                $file = $request->file('img');
                $file_name =$file->getClientoriginalName();
                $file->move(public_path('documentos/otros/'.$escuela[0]->escuela.'/'), time().'-'.$file_name);

                $doc = Documento::create([
                    'nombre' => $file_name,
                    'url' => 'documentos/otros/'.$escuela[0]->escuela.'/'.time().'-'.$file_name,
                    'fecha_subida' => date('Y-m-d'),
                    'tipo' => 'Otros',
                    'periodo' => '2024-II',
                    'id_escuela'=> auth()->user()->id_escuela,
                    'id_usuario' => auth()->id()
                ]);
                return response()->json(['menssje'=>'file upload success'], 200);
            }
        }catch(\Exception $e){
            return response()->json([
                'mssage'=>$e->getMessage()
            ]);
        }

    }


    public function getResoluciones(Request $request){

        $res = Documento::select('id','nombre','url','fecha_subida as fecha','tipo', 'obser','aceptado','periodo')
        ->where('tipo','=','Resolucion')
        ->where('id_usuario','=', auth()->id())
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('nombre', 'LIKE', '%' . $request->term . '%')
                ->orWhere('tipo', 'LIKE', '%' . $request->term . '%')
                ->orWhere('fecha_subida', 'LIKE', '%' . $request->term . '%');
        })->orderBy('id', 'DESC')
        ->paginate(10);

        $res->getCollection()->transform(function ($item) {
            $item->observacion = json_decode($item->observacion);
            return $item;
        });


        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }

    public function getPlanes(Request $request){

        $res = Documento::select('id','nombre','url','fecha_subida as fecha','tipo', 'obser','aceptado','periodo')
        ->where('tipo','=','Plan')
        ->where('id_usuario','=', auth()->id())
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('nombre', 'LIKE', '%' . $request->term . '%')
                ->orWhere('tipo', 'LIKE', '%' . $request->term . '%')
                ->orWhere('fecha_subida', 'LIKE', '%' . $request->term . '%');
        })->orderBy('id', 'DESC')
        ->paginate(10);

        $res->getCollection()->transform(function ($item) {
            $item->observacion = json_decode($item->observacion);
            return $item;
        });

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }

    public function getInformes(Request $request){

        $res = Documento::select('id','nombre','url','fecha_subida as fecha','tipo', 'obser','aceptado','periodo')
        ->where('tipo','=','Informe')
        ->where('id_usuario','=', auth()->id())
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('nombre', 'LIKE', '%' . $request->term . '%')
                ->orWhere('tipo', 'LIKE', '%' . $request->term . '%')
                ->orWhere('fecha_subida', 'LIKE', '%' . $request->term . '%');
        })->orderBy('id', 'DESC')
        ->paginate(10);

        $res->getCollection()->transform(function ($item) {
            $item->observacion = json_decode($item->observacion);
            return $item;
        });

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }

    public function getDictantes(Request $request){

        $res = Documento::select('id','nombre','url','fecha_subida as fecha','tipo', 'obser','aceptado','periodo')
        ->where('tipo','=','Dictantes')
        ->where('id_usuario','=', auth()->id())
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('nombre', 'LIKE', '%' . $request->term . '%')
                ->orWhere('tipo', 'LIKE', '%' . $request->term . '%')
                ->orWhere('fecha_subida', 'LIKE', '%' . $request->term . '%');
        })->orderBy('id', 'DESC')
        ->paginate(10);

        $res->getCollection()->transform(function ($item) {
            $item->observacion = json_decode($item->observacion);
            return $item;
        });

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }
    public function getOtros(Request $request){

        $res = Documento::select('id','nombre','url','fecha_subida as fecha','tipo', 'obser','aceptado','periodo')
        ->where('tipo','=','Otros')
        ->where('id_usuario','=', auth()->id())
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('nombre', 'LIKE', '%' . $request->term . '%')
                ->orWhere('tipo', 'LIKE', '%' . $request->term . '%')
                ->orWhere('fecha_subida', 'LIKE', '%' . $request->term . '%');
        })->orderBy('id', 'DESC')
        ->paginate(10);

        $res->getCollection()->transform(function ($item) {
            $item->observacion = json_decode($item->observacion);
            return $item;
        });

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }



}
