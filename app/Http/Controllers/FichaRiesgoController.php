<?php

namespace App\Http\Controllers;

use App\Models\FichaRiesgoAcademico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class FichaRiesgoController extends Controller
{
    /**
     * Mostrar formulario público
     */
    public function index()
    {
        return Inertia::render('FichaRiesgo/Index');
    }


    /**
     * Guardar formulario
     */
    public function guardar(Request $request)
    {
        $validator = Validator::make($request->all(), [

            // =============================
            // DATOS GENERALES
            // =============================

            'correo_registro' => 'required|email|max:150',
            'fecha' => 'required|date',

            'condicion_matricula' => 'required|integer|in:3,4',

            'nombres_apellidos' => 'required|string|max:200',

            'escuela_profesional' => 'required|string|max:150',

            'ciclo_academico' => 'required|integer|between:1,15',

            'codigo' => 'required|string|max:30',

            'dni' => 'required|string|max:15',

            'celular' => 'required|string|max:20',

            'celular_tutor' => 'required|string|max:20',

            'celular_pariente' => 'required|string|max:20',

            'facebook' => 'required|string|max:200',

            'correo' => 'required|email|max:150',

            'lugar_procedencia' => 'required|string|max:200',

            'direccion_actual' => 'required|string|max:255',


            // =============================
            // ACADÉMICOS
            // 1 Nunca
            // 2 Casi nunca
            // 3 A veces
            // 4 Casi siempre
            // 5 Siempre
            // =============================

            'a1'  => 'required|integer|between:1,5',
            'a2'  => 'required|integer|between:1,5',
            'a3'  => 'required|integer|between:1,5',
            'a4'  => 'required|integer|between:1,5',
            'a5'  => 'required|integer|between:1,5',
            'a6'  => 'required|integer|between:1,5',
            'a7'  => 'required|integer|between:1,5',
            'a8'  => 'required|integer|between:1,5',
            'a9'  => 'required|integer|between:1,5',
            'a10' => 'required|integer|between:1,5',


            // =============================
            // PERSONALES
            // =============================

            'p1' => 'required|integer|between:1,5',
            'p2' => 'required|integer|between:1,5',

            // SI / NO
            'p3' => 'required|integer|in:0,1',

            'p4' => 'required|integer|between:1,5',
            'p5' => 'required|integer|between:1,5',
            'p6' => 'required|integer|between:1,5',
            'p7' => 'required|integer|between:1,5',
            'p8' => 'required|integer|between:1,5',
            'p9' => 'required|integer|between:1,5',

            // SI / NO
            'p10' => 'required|integer|in:0,1',

            'p11' => 'required|integer|between:1,5',
            'p12' => 'required|integer|between:1,5',
            'p13' => 'required|integer|between:1,5',


            // =============================
            // FAMILIARES
            // =============================

            'f1' => 'required|integer|between:1,5',
            'f2' => 'required|integer|between:1,5',
            'f3' => 'required|integer|between:1,5',

            // SI / NO
            'f4' => 'required|integer|in:0,1',

            // SI / NO
            'f5' => 'required|integer|in:0,1',

            'f6' => 'required|integer|between:1,5',

            // SI / NO
            'f7' => 'required|integer|in:0,1',

            // SI / NO
            'f8' => 'required|integer|in:0,1',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'mensaje' => 'Complete correctamente todos los campos obligatorios.',
                'errors' => $validator->errors(),
            ], 422);
        }


        try {

            $ficha = FichaRiesgoAcademico::create(
                $validator->validated()
            );

            return response()->json([
                'ok' => true,
                'mensaje' => 'Ficha registrada correctamente.',
                'id' => $ficha->id,
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'ok' => false,
                'mensaje' => 'Ocurrió un error al registrar la ficha.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}