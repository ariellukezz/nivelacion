<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocenteCompetencia extends Model
{
    use HasFactory;
    protected $table = 'docente_competencia';
    protected $fillable = [
        'id_competencia',
        'id_docente'
    ];
}
