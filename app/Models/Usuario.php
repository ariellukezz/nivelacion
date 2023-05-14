<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    use HasFactory;

    protected $table = "users";
    protected $fillable = [
        'nombres',
        'apellidos',
        'email',
        'password',
        'estado',
        'programa_id',
        'id_escuela',
        'rol',
        'id_usuario',
        'estado_contraseña'
    ];
    
}
