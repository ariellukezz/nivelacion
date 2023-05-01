<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    use HasFactory;

    protected $table = 'docente';

    protected $fillable = [
        'tipo_doc',
        'nro_doc',
        'paterno',
        'materno',
        'nombres',
        'f_nac',
        'sexo',
        'direccion',
        'telefono',
        'email',
        'estado',
        'id_usuario',
    ];

}
