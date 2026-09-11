<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmisionPostulante extends Model
{
    protected $table = 'admision_postulante';

    protected $fillable = [
        'id_proceso_admision', 'id_programa_admision', 'id_programa_nivelacion',
        'codigo', 'dni', 'paterno', 'materno', 'nombres', 'sexo', 'email',
        'f_nacimiento', 'ubigeo_nacimiento', 'estado_civil', 'anio_egreso',
        'tipo_colegio', 'nombre_colegio', 'ubigeo_colegio', 'direccion',
        'telefono', 'f_examen', 'modalidad', 'puntaje', 'proceso_nombre',
        'programa_nombre', 'departamento', 'provincia', 'distrito',
        'sincronizado_at'
    ];

    protected $casts = [
        'f_nacimiento' => 'date',
        'f_examen' => 'date',
        'sincronizado_at' => 'datetime'
    ];
}
