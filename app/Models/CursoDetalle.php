<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CursoDetalle extends Model
{
    use HasFactory;

    protected $table = 'curso_detalle';

    protected $fillable = [
        'id_curso',
        'id_alumno',
        'nota',
        'condicion',
        'encuesta'
    ];
}
