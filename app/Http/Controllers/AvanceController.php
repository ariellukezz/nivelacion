<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use DB;

class AvanceController extends Controller {
    
  public function getAvance(){


            $escuelas = DB::select('SELECT 
            distinct
            documento.id_escuela,
            escuela.nombre, escuela.nombre_corto
            FROM documento
            JOIN escuela ON documento.id_escuela = escuela.id');

            $data = DB::select('SELECT id, id_escuela, tipo FROM documento
            WHERE id_escuela IS NOT NULL;');

            $montos = [
                'Resolucion' => 15,
                'Plan' => 25,
                'Informe' => 60
            ];

            $resultado = [];

            foreach ($data as $item) {
                $escuela = $item->id_escuela;
                $tipo = $item->tipo;
            
                if (!isset($resultado[$escuela])) {
                    $resultado[$escuela] = 0;
                }
            
                if (isset($montos[$tipo])) {
                    $resultado[$escuela] += $montos[$tipo];
                }
            }
        
    
        $this->response['estado'] = true;
        $this->response['datos'] = $resultado;
        $this->response['escuelas'] = $escuelas;
        return response()->json($this->response, 200);
        

    }

}
