<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id', 'titulo', 'mensaje', 'leido', 'tipo', 'url', 'imagen_url', 'activo'
    ];

    protected $casts = [
        'leido'  => 'boolean',
        'activo' => 'boolean',
    ];
}
