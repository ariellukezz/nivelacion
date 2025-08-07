<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    use HasFactory;

    protected $table = 'periodo';           // nombre de la tabla
    protected $primaryKey = 'id_periodo';   // clave primaria personalizada
    public $timestamps = true;              // usa created_at y updated_at

    protected $fillable = [
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'created_at',
        'updated_at'
    ];

    const ACTIVO = 'activo';

    /**
     * Obtener el ID del periodo activo actual.
     */
    public static function activoId(): int
    {
        return cache()->remember('periodo_activo_id', now()->addMinutes(10), function () {
            return static::where('estado', self::ACTIVO)->value('id_periodo') ?? 0;
        });
    }

    /**
     * Cursos que pertenecen a este periodo.
     */
    public function cursos()
    {
        return $this->hasMany(Curso::class, 'id_periodo', 'id_periodo');
    }

    /**
     * CursoDetalle que pertenece a este periodo.
     */
    public function cursoDetalles()
    {
        return $this->hasMany(CursoDetalle::class, 'id_periodo', 'id_periodo');
    }
}
