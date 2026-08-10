<?php

namespace App\Http\Controllers;

use App\Models\FichaRiesgoAcademico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class FichaRiesgoAcademicoController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | REPORTE SUPERVISOR
    |--------------------------------------------------------------------------
    */

    public function reporteSupervisor(Request $request)
    {
        try {

            $query = FichaRiesgoAcademico::query();


            /*
            |--------------------------------------------------------------------------
            | BUSCADOR
            |--------------------------------------------------------------------------
            */

            if ($request->filled('q')) {

                $q = trim($request->q);

                $query->where(function ($consulta) use ($q) {

                    $consulta
                        ->where(
                            'nombres_apellidos',
                            'like',
                            '%' . $q . '%'
                        )
                        ->orWhere(
                            'dni',
                            'like',
                            '%' . $q . '%'
                        )
                        ->orWhere(
                            'codigo',
                            'like',
                            '%' . $q . '%'
                        )
                        ->orWhere(
                            'escuela_profesional',
                            'like',
                            '%' . $q . '%'
                        );

                });
            }


            /*
            |--------------------------------------------------------------------------
            | ESCUELA
            |--------------------------------------------------------------------------
            */

            if ($request->filled('escuela')) {

                $query->where(
                    'escuela_profesional',
                    $request->escuela
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CICLO
            |--------------------------------------------------------------------------
            */

            if ($request->filled('ciclo')) {

                $query->where(
                    'ciclo_academico',
                    (int) $request->ciclo
                );
            }


            /*
            |--------------------------------------------------------------------------
            | CONDICIÓN MATRÍCULA
            |--------------------------------------------------------------------------
            */

            if ($request->filled('condicion')) {

                $query->where(
                    'condicion_matricula',
                    (int) $request->condicion
                );
            }


            /*
            |--------------------------------------------------------------------------
            | FECHA DESDE
            |--------------------------------------------------------------------------
            */

            if ($request->filled('fecha_desde')) {

                $query->whereDate(
                    'fecha',
                    '>=',
                    $request->fecha_desde
                );
            }


            /*
            |--------------------------------------------------------------------------
            | FECHA HASTA
            |--------------------------------------------------------------------------
            */

            if ($request->filled('fecha_hasta')) {

                $query->whereDate(
                    'fecha',
                    '<=',
                    $request->fecha_hasta
                );
            }


            /*
            |--------------------------------------------------------------------------
            | RESUMEN
            |--------------------------------------------------------------------------
            */

            $total = (clone $query)->count();

            $tercera = (clone $query)
                ->where('condicion_matricula', 3)
                ->count();

            $cuarta = (clone $query)
                ->where('condicion_matricula', 4)
                ->count();


            /*
            |--------------------------------------------------------------------------
            | CANTIDAD POR ESCUELA
            |--------------------------------------------------------------------------
            */

            $porEscuela = (clone $query)
                ->select(
                    'escuela_profesional',
                    DB::raw('COUNT(*) AS total')
                )
                ->groupBy('escuela_profesional')
                ->orderByDesc('total')
                ->get();


            /*
            |--------------------------------------------------------------------------
            | PAGINACIÓN
            |--------------------------------------------------------------------------
            */

            $porPagina = (int) $request->input(
                'per_page',
                20
            );

            if (!in_array(
                $porPagina,
                [10, 20, 50, 100]
            )) {
                $porPagina = 20;
            }


            /*
            |--------------------------------------------------------------------------
            | FICHAS
            |--------------------------------------------------------------------------
            */

            $fichas = $query
                ->select([
                    'id',
                    'fecha',
                    'condicion_matricula',
                    'nombres_apellidos',
                    'escuela_profesional',
                    'ciclo_academico',
                    'codigo',
                    'dni',
                    'celular',
                    'correo',
                    'created_at'
                ])
                ->orderByDesc('fecha')
                ->orderByDesc('id')
                ->paginate($porPagina);


            /*
            |--------------------------------------------------------------------------
            | ESCUELAS PARA EL SELECT
            |--------------------------------------------------------------------------
            */

            $escuelas = FichaRiesgoAcademico::query()
                ->whereNotNull('escuela_profesional')
                ->where(
                    'escuela_profesional',
                    '<>',
                    ''
                )
                ->distinct()
                ->orderBy('escuela_profesional')
                ->pluck('escuela_profesional');


            return response()->json([

                'ok' => true,

                'resumen' => [
                    'total' => $total,
                    'tercera' => $tercera,
                    'cuarta' => $cuarta,
                ],

                'escuelas' => $escuelas,

                'por_escuela' => $porEscuela,

                'fichas' => $fichas,

            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'ok' => false,
                'mensaje' =>
                    'No se pudo obtener el reporte.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | VER FICHA COMPLETA
    |--------------------------------------------------------------------------
    */

    public function verFichaSupervisor($id)
    {
        try {

            $ficha = FichaRiesgoAcademico::find($id);

            if (!$ficha) {

                return response()->json([
                    'ok' => false,
                    'mensaje' =>
                        'La ficha no fue encontrada.'
                ], 404);
            }


            return response()->json([
                'ok' => true,
                'ficha' => $ficha
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'ok' => false,
                'mensaje' =>
                    'No se pudo obtener la ficha.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZAR FICHA
    |--------------------------------------------------------------------------
    */

    public function actualizarSupervisor(
        Request $request,
        $id
    ) {

        $ficha = FichaRiesgoAcademico::find($id);

        if (!$ficha) {

            return response()->json([
                'ok' => false,
                'mensaje' =>
                    'La ficha no fue encontrada.'
            ], 404);
        }


        $validator = Validator::make(
            $request->all(),
            $this->reglas()
        );


        if ($validator->fails()) {

            return response()->json([
                'ok' => false,
                'mensaje' =>
                    'Revise los campos del formulario.',
                'errors' =>
                    $validator->errors()
            ], 422);
        }


        try {

            $ficha->update(
                $validator->validated()
            );


            return response()->json([
                'ok' => true,
                'mensaje' =>
                    'Ficha actualizada correctamente.',
                'ficha' =>
                    $ficha->fresh()
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'ok' => false,
                'mensaje' =>
                    'No se pudo actualizar la ficha.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | REGLAS
    |--------------------------------------------------------------------------
    */

    private function reglas()
    {
        return [

            /*
            |--------------------------------------------------------------------------
            | DATOS GENERALES
            |--------------------------------------------------------------------------
            */

            'correo_registro' =>
                'required|email|max:150',

            'fecha' =>
                'required|date',

            'condicion_matricula' =>
                'required|integer|in:3,4',

            'nombres_apellidos' =>
                'required|string|max:200',

            'escuela_profesional' =>
                'required|string|max:150',

            'ciclo_academico' =>
                'required|integer|between:1,15',

            'codigo' =>
                'required|string|max:30',

            'dni' =>
                'required|string|max:15',

            'celular' =>
                'required|string|max:20',

            'celular_tutor' =>
                'required|string|max:20',

            'celular_pariente' =>
                'required|string|max:20',

            'facebook' =>
                'required|string|max:200',

            'correo' =>
                'required|email|max:150',

            'lugar_procedencia' =>
                'required|string|max:200',

            'direccion_actual' =>
                'required|string|max:255',


            /*
            |--------------------------------------------------------------------------
            | ACADÉMICOS
            |--------------------------------------------------------------------------
            */

            'a1' => 'required|integer|between:1,5',
            'a2' => 'required|integer|between:1,5',
            'a3' => 'required|integer|between:1,5',
            'a4' => 'required|integer|between:1,5',
            'a5' => 'required|integer|between:1,5',
            'a6' => 'required|integer|between:1,5',
            'a7' => 'required|integer|between:1,5',
            'a8' => 'required|integer|between:1,5',
            'a9' => 'required|integer|between:1,5',
            'a10' => 'required|integer|between:1,5',


            /*
            |--------------------------------------------------------------------------
            | PERSONALES
            |--------------------------------------------------------------------------
            */

            'p1' => 'required|integer|between:1,5',
            'p2' => 'required|integer|between:1,5',

            'p3' => 'required|integer|in:0,1',

            'p4' => 'required|integer|between:1,5',
            'p5' => 'required|integer|between:1,5',
            'p6' => 'required|integer|between:1,5',
            'p7' => 'required|integer|between:1,5',
            'p8' => 'required|integer|between:1,5',
            'p9' => 'required|integer|between:1,5',

            'p10' => 'required|integer|in:0,1',

            'p11' => 'required|integer|between:1,5',
            'p12' => 'required|integer|between:1,5',
            'p13' => 'required|integer|between:1,5',


            /*
            |--------------------------------------------------------------------------
            | FAMILIARES
            |--------------------------------------------------------------------------
            */

            'f1' => 'required|integer|between:1,5',
            'f2' => 'required|integer|between:1,5',
            'f3' => 'required|integer|between:1,5',

            'f4' => 'required|integer|in:0,1',
            'f5' => 'required|integer|in:0,1',

            'f6' => 'required|integer|between:1,5',

            'f7' => 'required|integer|in:0,1',
            'f8' => 'required|integer|in:0,1',
        ];
    }


/*
|--------------------------------------------------------------------------
| IMPRIMIR FICHA
|--------------------------------------------------------------------------
*/

public function imprimirSupervisor($id)
{
    $ficha = FichaRiesgoAcademico::find($id);

    if (!$ficha) {
        abort(404, 'Ficha no encontrada.');
    }

    return view('reportes.ficha-riesgo', [
        'ficha' => $ficha,
        'modoPdf' => false
    ]);
}


/*
|--------------------------------------------------------------------------
| DESCARGAR PDF
|--------------------------------------------------------------------------
*/

public function pdfSupervisor($id)
{
    $ficha = FichaRiesgoAcademico::find($id);

    if (!$ficha) {
        abort(404, 'Ficha no encontrada.');
    }

    $pdf = Pdf::loadView(
        'reportes.ficha-riesgo',
        [
            'ficha' => $ficha,
            'modoPdf' => true
        ]
    );

    $pdf->setPaper('a4', 'portrait');

    $dni = preg_replace(
        '/[^A-Za-z0-9_-]/',
        '',
        (string) $ficha->dni
    );

    return $pdf->download(
        'ficha-riesgo-' . $dni . '.pdf'
    );
}


/*
|--------------------------------------------------------------------------
| ELIMINAR FICHA
|--------------------------------------------------------------------------
*/

public function eliminarSupervisor($id)
{
    try {

        $ficha = FichaRiesgoAcademico::find($id);

        if (!$ficha) {

            return response()->json([
                'ok' => false,
                'mensaje' => 'La ficha no fue encontrada.'
            ], 404);
        }

        $nombre = $ficha->nombres_apellidos;

        $ficha->delete();

        return response()->json([
            'ok' => true,
            'mensaje' =>
                'La ficha de ' .
                $nombre .
                ' fue eliminada correctamente.'
        ]);

    } catch (\Throwable $e) {

        return response()->json([
            'ok' => false,
            'mensaje' =>
                'No se pudo eliminar la ficha.',
            'error' => $e->getMessage()
        ], 500);
    }
}





/*
|--------------------------------------------------------------------------
| OBTENER TODAS LAS FICHAS PARA EXCEL
|--------------------------------------------------------------------------
*/

public function exportarDataSupervisor()
{
    try {

        $fichas = FichaRiesgoAcademico::orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'ok' => true,
            'datos' => $fichas
        ]);

    } catch (\Throwable $e) {

        return response()->json([
            'ok' => false,
            'mensaje' => 'No se pudo obtener la información.',
            'error' => $e->getMessage()
        ], 500);
    }
}


}