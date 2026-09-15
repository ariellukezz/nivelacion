<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoAsignacionEscuela extends Model
{
    use HasFactory;

    protected $table = 'permiso_asignacion_escuela';

    protected $fillable = [
        'id_escuela',
        'id_periodo',
        'puede_crear_curso',
        'puede_editar_curso',
        'puede_eliminar_curso',
        'puede_matricular',
        'puede_asignar_docente',
        'usuario_modificacion_id',
    ];

    protected $casts = [
        'puede_crear_curso' => 'boolean',
        'puede_editar_curso' => 'boolean',
        'puede_eliminar_curso' => 'boolean',
        'puede_matricular' => 'boolean',
        'puede_asignar_docente' => 'boolean',
    ];

    public static function valoresPorDefecto(): array
    {
        return [
            'puede_crear_curso' => false,
            'puede_editar_curso' => false,
            'puede_eliminar_curso' => false,
            'puede_matricular' => false,
            'puede_asignar_docente' => true,
        ];
    }

    public static function obtenerPara(int $idEscuela, int $idPeriodo): array
    {
        $permiso = static::where('id_escuela', $idEscuela)
            ->where('id_periodo', $idPeriodo)
            ->first();

        if (!$permiso) {
            return array_merge(static::valoresPorDefecto(), [
                'id' => null,
                'id_escuela' => $idEscuela,
                'id_periodo' => $idPeriodo,
            ]);
        }

        return [
            'id' => $permiso->id,
            'id_escuela' => (int) $permiso->id_escuela,
            'id_periodo' => (int) $permiso->id_periodo,
            'puede_crear_curso' => (bool) $permiso->puede_crear_curso,
            'puede_editar_curso' => (bool) $permiso->puede_editar_curso,
            'puede_eliminar_curso' => (bool) $permiso->puede_eliminar_curso,
            'puede_matricular' => (bool) $permiso->puede_matricular,
            'puede_asignar_docente' => (bool) $permiso->puede_asignar_docente,
        ];
    }

    public static function permitido(int $idEscuela, int $idPeriodo, string $accion): bool
    {
        $permitidos = [
            'crear' => 'puede_crear_curso',
            'editar' => 'puede_editar_curso',
            'eliminar' => 'puede_eliminar_curso',
            'matricular' => 'puede_matricular',
            'asignar_docente' => 'puede_asignar_docente',
        ];

        if (!isset($permitidos[$accion])) {
            return false;
        }

        $datos = static::obtenerPara($idEscuela, $idPeriodo);
        return (bool) ($datos[$permitidos[$accion]] ?? false);
    }
}
