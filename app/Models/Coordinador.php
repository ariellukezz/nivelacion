<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinador extends Model
{
    use HasFactory;

    protected $table = 'coordinador';

    protected $fillable = [
    'dni',
    'nombres',
    'apellidos',
    'celular',
    'correo',
    'id_escuela',
    'id_usuario',
    'usuario_id',
    'estado'
    ];
}
