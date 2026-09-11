<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmisionProceso extends Model
{
    protected $table = 'admision_proceso';

    protected $fillable = [
        'id_admision', 'nombre', 'slug', 'anio', 'estado_api', 'fec_fin',
        'id_sede_filial', 'fecha_examen', 'url', 'fec_1', 'fec_2',
        'semestre_detectado', 'id_periodo', 'sincronizado_at'
    ];

    protected $casts = [
        'fec_fin' => 'date',
        'fec_1' => 'date',
        'fec_2' => 'date',
        'sincronizado_at' => 'datetime'
    ];
}
