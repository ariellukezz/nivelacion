<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BotonControl extends Model
{
    use HasFactory;

    // Especifica la tabla que este modelo gestionará
    protected $table = 'boton_control';

    // Definir los campos que son asignables
    protected $fillable = [
        'nombre_boton', 'estado', 'fecha_inicio', 'fecha_fin', 'modulo', 'id_escuela', 'id_usuario'
    ];

    // La propiedad $casts te permite convertir los valores a tipos de datos específicos
    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'estado' => 'boolean',
    ];

    // Método para verificar si el botón está habilitado según la fecha actual
    public function estaHabilitado($usuarioId = null, $escuelaId = null)
    {
        // Verificamos si la fecha actual está entre la fecha de inicio y fin, y si el botón está activado
        $now = Carbon::now();

        // Comprobar si las fechas están dentro del rango
        if ($this->estado && $now->between($this->fecha_inicio, $this->fecha_fin)) {
            // Si el botón tiene una restricción de usuario o escuela
            if ($this->id_usuario && $this->id_usuario != $usuarioId) {
                return false; // El usuario no tiene acceso
            }
            if ($this->id_escuela && $this->id_escuela != $escuelaId) {
                return false; // La escuela no tiene acceso
            }
            // Si no hay restricciones, el botón está habilitado
            return true;
        }

        return false; // El botón no está habilitado
    }
}
