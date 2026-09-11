<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmisionMatriz extends Model
{
    protected $table = 'admision_matriz';

    protected $fillable = [
        'id_periodo', 'dni',
        'C1', 'C2', 'C3', 'C4', 'C5', 'C6',
        'C7', 'C8', 'C9', 'C10', 'C11',
        'C1_R', 'C2_R', 'C3_R', 'C4_R', 'C5_R', 'C6_R',
        'C7_R', 'C8_R', 'C9_R', 'C10_R', 'C11_R',
        'nivelar', 'no_nivelar', 'observacion', 'archivo_origen', 'importado_at'
    ];

    protected $casts = [
        'importado_at' => 'datetime'
    ];
}
