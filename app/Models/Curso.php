<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'curso';

    protected $fillable = [
        'nombre',
        'id_competencia',
        'id_docente',
        'grupo',
        'escuela',
        'id_usuario',
        'estado',
        'id_programa'
    ];
}
