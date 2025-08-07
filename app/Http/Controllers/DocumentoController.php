<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Periodo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class DocumentoController extends Controller
{
    protected $response = []; // Definimos la propiedad aquí

    public function download($uuid)
    {
        $book = Book::where('uuid', $uuid)->firstOrFail();
        $pathToFile = storage_path("app/public/subfolder/$book->id/" . $book->book_image);
        // return response()->download($pathToFile);
        return response()->file($pathToFile);
    }


public function resolucion(Request $request)
{
    // Escuela del usuario
    $escuela = DB::table('users')
                 ->join('programa', 'users.programa_id', '=', 'programa.id')
                 ->where('users.id', auth()->id())
                 ->value('programa.escuela');

    // Periodo activo
    $periodoActivo = Periodo::where('estado', 'activo')->first();
    if (!$periodoActivo) {
        return response()->json(['message' => 'No existe un periodo activo'], 422);
    }

    // Subida
    if ($request->hasFile('img')) {
        $file         = $request->file('img');
        $originalName = $file->getClientOriginalName();          // → nombre.pdf
        $timestamp    = time();
        $fileName     = "{$periodoActivo->nombre}-{$timestamp}-{$originalName}";
        $folder       = "documentos/resoluciones/{$escuela}/";
        $file->move(public_path($folder), $fileName);

        Documento::create([
            'nombre'       => $originalName,                     // solo el nombre limpio
            'url'          => $folder . $fileName,               // ruta + nombre largo
            'fecha_subida' => now()->toDateString(),
            'tipo'         => 'Resolucion',
            'periodo'      => $periodoActivo->nombre,            // p.ej. 2025-I
            'id_escuela'   => auth()->user()->id_escuela,
            'id_usuario'   => auth()->id()
        ]);

        return response()->json(['message' => 'Archivo subido con éxito'], 200);
    }

    return response()->json(['message' => 'No se recibió ningún archivo'], 400);
}

    public function plan(Request $request)
{
    // 1. Obtener nombre de escuela del usuario
    $escuelaNombre = DB::table('users')
        ->join('programa', 'users.programa_id', '=', 'programa.id')
        ->where('users.id', auth()->id())
        ->value('programa.escuela');

    // Sanear nombre de carpeta
    $escuelaSlug = Str::slug($escuelaNombre);  // para carpeta segura

    // 2. Buscar periodo activo
    $periodoActivo = Periodo::where('estado', 'activo')->first();
    if (!$periodoActivo) {
        return response()->json(['message' => 'No existe un periodo activo'], 422);
    }

    // 3. Procesar archivo
    if ($request->hasFile('img')) {
        $file         = $request->file('img');
        $originalName = $file->getClientOriginalName(); // nombre.pdf
        $timestamp    = time();
        $fileName     = "{$periodoActivo->nombre}-{$timestamp}-{$originalName}";
        $folder       = "documentos/planes/{$escuelaSlug}/";

        $file->move(public_path($folder), $fileName);

        // 4. Registrar en base de datos
        Documento::create([
            'nombre'       => $originalName,
            'url'          => $folder . $fileName,
            'fecha_subida' => now()->toDateString(),
            'tipo'         => 'Plan',
            'periodo'      => $periodoActivo->nombre,
            'id_escuela'   => auth()->user()->id_escuela,
            'id_usuario'   => auth()->id()
        ]);

        return response()->json(['message' => 'Plan guardado'], 200);
    }

    return response()->json(['message' => 'No se recibió ningún archivo'], 400);
}

    public function informe(Request $request)
{
    // 1. Escuela del usuario (nombre limpio para carpeta)
    $escuelaNombre = DB::table('users')
        ->join('programa', 'users.programa_id', '=', 'programa.id')
        ->where('users.id', auth()->id())
        ->value('programa.escuela');

    $escuelaSlug = Str::slug($escuelaNombre);   // p.ej. “Ingeniería Civil” → “ingenieria-civil”

    // 2. Periodo activo
    $periodoActivo = Periodo::where('estado', 'activo')->first();
    if (!$periodoActivo) {
        return response()->json(['message' => 'No existe un periodo activo'], 422);
    }

    // 3. Procesar archivo
    if ($request->hasFile('img')) {
        $file         = $request->file('img');
        $originalName = $file->getClientOriginalName();     // nombre.pdf
        $timestamp    = time();
        $fileName     = "{$periodoActivo->nombre}-{$timestamp}-{$originalName}";

        $folder = "documentos/informes/{$escuelaSlug}/";
        $file->move(public_path($folder), $fileName);

        // 4. Guardar en BD
        Documento::create([
            'nombre'       => $originalName,                // solo nombre original
            'url'          => $folder . $fileName,          // ruta + nombre largo
            'fecha_subida' => now()->toDateString(),
            'tipo'         => 'Informe',
            'periodo'      => $periodoActivo->nombre,
            'id_escuela'   => auth()->user()->id_escuela,
            'id_usuario'   => auth()->id()
        ]);

        return response()->json(['message' => 'Informe guardado'], 200);
    }

    return response()->json(['message' => 'No se recibió ningún archivo'], 400);
}

    public function dictantes(Request $request)
{
    // 1. Obtener nombre de escuela (carpeta segura)
    $escuelaNombre = DB::table('users')
        ->join('programa', 'users.programa_id', '=', 'programa.id')
        ->where('users.id', auth()->id())
        ->value('programa.escuela');

    $escuelaSlug = Str::slug($escuelaNombre);  // Ej. "ingenieria-civil"

    // 2. Obtener periodo activo
    $periodoActivo = Periodo::where('estado', 'activo')->first();
    if (!$periodoActivo) {
        return response()->json(['message' => 'No existe un periodo activo'], 422);
    }

    // 3. Procesar archivo
    if ($request->hasFile('img')) {
        $file         = $request->file('img');
        $originalName = $file->getClientOriginalName();
        $timestamp    = time();
        $fileName     = "{$periodoActivo->nombre}-{$timestamp}-{$originalName}";
        $folder       = "documentos/dictantes/{$escuelaSlug}/";

        $file->move(public_path($folder), $fileName);

        // 4. Registrar en BD
        Documento::create([
            'nombre'       => $originalName,
            'url'          => $folder . $fileName,
            'fecha_subida' => now()->toDateString(),
            'tipo'         => 'Dictantes',
            'periodo'      => $periodoActivo->nombre,
            'id_escuela'   => auth()->user()->id_escuela,
            'id_usuario'   => auth()->id()
        ]);

        return response()->json(['message' => 'Archivo de dictantes guardado correctamente'], 200);
    }

    return response()->json(['message' => 'No se recibió ningún archivo'], 400);
}


    public function otros(Request $request)
{
    // 1. Escuela del usuario
    $escuelaNombre = DB::table('users')
        ->join('programa', 'users.programa_id', '=', 'programa.id')
        ->where('users.id', auth()->id())
        ->value('programa.escuela');

    $escuelaSlug = Str::slug($escuelaNombre); // Carpeta segura

    // 2. Periodo activo
    $periodoActivo = Periodo::where('estado', 'activo')->first();
    if (!$periodoActivo) {
        return response()->json(['message' => 'No existe un periodo activo'], 422);
    }

    // 3. Procesar archivo
    if ($request->hasFile('img')) {
        $file         = $request->file('img');
        $originalName = $file->getClientOriginalName(); // nombre.pdf
        $timestamp    = time();
        $fileName     = "{$periodoActivo->nombre}-{$timestamp}-{$originalName}";
        $folder       = "documentos/otros/{$escuelaSlug}/";

        $file->move(public_path($folder), $fileName);

        // 4. Insertar en la base de datos
        Documento::create([
            'nombre'       => $originalName,
            'url'          => $folder . $fileName,
            'fecha_subida' => now()->toDateString(),
            'tipo'         => 'Otros',
            'periodo'      => $periodoActivo->nombre,
            'id_escuela'   => auth()->user()->id_escuela,
            'id_usuario'   => auth()->id()
        ]);

        return response()->json(['message' => 'Archivo cargado con éxito'], 200);
    }

    return response()->json(['message' => 'No se recibió ningún archivo'], 400);
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
