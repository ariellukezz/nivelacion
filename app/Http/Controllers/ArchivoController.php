<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArchivoController extends Controller
{
    public function descargarArchivo($nombreArchivo) {

        $rutaArchivo = public_path('documentos/' . $nombreArchivo);
        
        return response()->download($rutaArchivo, $nombreArchivo);
    }









}
