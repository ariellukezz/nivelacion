<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $table = 'documento';

    protected $fillable = [
        'nombre',
        'url',
        'fecha_subida',
        'id_usuario',
        'id_escuela',
        'tipo',
        'obser',
        'periodo',
        'aceptado'
    ];

}
