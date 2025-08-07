<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EncargadoSistema extends Model
{
    protected $table = 'encargados_sistema';

    protected $fillable = [
        'nombres',
        'apellidos',
        'dni',
        'correo',
        'num_celular',
        'cargo',
        'fecha_designacion',
        'fecha_fin_designacion',
        'estado',
        'observaciones',
        'usuario_id',
    ];

    protected $casts = [
        'estado' => 'integer',
        'fecha_designacion' => 'date',
        'fecha_fin_designacion' => 'date',
    ];

    protected $primaryKey = 'id_encargado';
}
