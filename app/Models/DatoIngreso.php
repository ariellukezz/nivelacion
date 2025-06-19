<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatoIngreso extends Model
{
    use HasFactory;

    protected $table = 'datos_ingreso';
    public $timestamps = false;

    protected $fillable = [
        'dni',
        'codigo_est',
        'anio',
        'semestre',
        'f_examen',
        't_examen',
        'puntaje_examen',
        'modalidad',
        'id_programa',
        'observacion'
    ];
}
