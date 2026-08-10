<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FichaRiesgoAcademico extends Model
{
    use HasFactory;

    protected $table = 'fichas_riesgo_academico';

    protected $primaryKey = 'id';

    protected $fillable = [

        // DATOS GENERALES
        'correo_registro',
        'fecha',
        'condicion_matricula',
        'nombres_apellidos',
        'escuela_profesional',
        'ciclo_academico',
        'codigo',
        'dni',
        'celular',
        'celular_tutor',
        'celular_pariente',
        'facebook',
        'correo',
        'lugar_procedencia',
        'direccion_actual',

        // ACADÉMICOS
        'a1',
        'a2',
        'a3',
        'a4',
        'a5',
        'a6',
        'a7',
        'a8',
        'a9',
        'a10',

        // PERSONALES
        'p1',
        'p2',
        'p3',
        'p4',
        'p5',
        'p6',
        'p7',
        'p8',
        'p9',
        'p10',
        'p11',
        'p12',
        'p13',

        // FAMILIARES
        'f1',
        'f2',
        'f3',
        'f4',
        'f5',
        'f6',
        'f7',
        'f8',
    ];

    protected $casts = [
        'fecha' => 'date',

        'condicion_matricula' => 'integer',
        'ciclo_academico' => 'integer',

        'a1' => 'integer',
        'a2' => 'integer',
        'a3' => 'integer',
        'a4' => 'integer',
        'a5' => 'integer',
        'a6' => 'integer',
        'a7' => 'integer',
        'a8' => 'integer',
        'a9' => 'integer',
        'a10' => 'integer',

        'p1' => 'integer',
        'p2' => 'integer',
        'p3' => 'integer',
        'p4' => 'integer',
        'p5' => 'integer',
        'p6' => 'integer',
        'p7' => 'integer',
        'p8' => 'integer',
        'p9' => 'integer',
        'p10' => 'integer',
        'p11' => 'integer',
        'p12' => 'integer',
        'p13' => 'integer',

        'f1' => 'integer',
        'f2' => 'integer',
        'f3' => 'integer',
        'f4' => 'integer',
        'f5' => 'integer',
        'f6' => 'integer',
        'f7' => 'integer',
        'f8' => 'integer',
    ];
}