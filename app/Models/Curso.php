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
    'estado',
    'id_programa',
    'id_usuario',
    'id_periodo', // 👈 MUY IMPORTANTE
];

public function periodo()
{
    return $this->belongsTo(Periodo::class, 'id_periodo', 'id_periodo');
}
}
