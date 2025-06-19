<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alumno extends Model
{
    use HasFactory;

    protected $table = 'estudiante';

    protected $fillable = [
        'codigo',
        'dni',
        'codigo_est',
        'paterno',
        'materno',
        'nombres',
        'sexo',
        'email',
        'f_nacimiento',
        'ubigeo_nacimiento',
        'estado_civil',
        'anio_egreso',
        'tipo_colegio',
        'nombre_colegio',
        'ubigeo_colegio',
        'apto',
        'direccion',
        'telefono',
        'usuario_id'
    ];


}
