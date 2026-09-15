<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstudianteEstadoController extends Controller
{
    public function actualizarDirector(Request $request, int $id)
    {
        $data = $request->validate([
            'estado_nivelacion' => ['required', 'boolean'],
            'motivo' => ['nullable', 'string', 'max:255'],
        ]);

        $idEscuela = (int) (auth()->user()->id_escuela ?? 0);

        $pertenece = DB::table('estudiante as e')
            ->join('datos_ingreso as di', 'di.codigo_est', '=', 'e.codigo_est')
            ->join('programa as p', 'p.id', '=', 'di.id_programa')
            ->where('e.id', $id)
            ->where('p.id_escuela', $idEscuela)
            ->exists();

        if (!$pertenece) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'El estudiante no pertenece a su Escuela Profesional.',
            ], 403);
        }

        $alumno = Alumno::find($id);
        if (!$alumno) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'Estudiante no encontrado.',
            ], 404);
        }

        $nuevoEstado = $request->boolean('estado_nivelacion') ? 1 : 0;
        $motivo = trim((string) ($data['motivo'] ?? ''));

        if ($nuevoEstado === 0 && $motivo === '') {
            return response()->json([
                'estado' => false,
                'mensaje' => 'Indique el motivo del retiro o desactivación.',
            ], 422);
        }

        DB::transaction(function () use ($alumno, $nuevoEstado, $motivo) {
            $alumno->estado_nivelacion = $nuevoEstado;
            $alumno->motivo_estado_nivelacion = $nuevoEstado ? null : $motivo;
            $alumno->fecha_estado_nivelacion = now();
            $alumno->usuario_estado_nivelacion_id = auth()->id();
            $alumno->save();
        });

        return response()->json([
            'estado' => true,
            'tipo' => 'success',
            'titulo' => $nuevoEstado ? 'ESTUDIANTE REACTIVADO' : 'ESTUDIANTE RETIRADO',
            'mensaje' => $nuevoEstado
                ? 'El estudiante vuelve a estar disponible para nuevas asignaciones de nivelación.'
                : 'El estudiante fue retirado y ya no estará disponible para nuevas asignaciones de nivelación.',
            'datos' => [
                'id' => $alumno->id,
                'estado_nivelacion' => (int) $alumno->estado_nivelacion,
                'motivo_estado_nivelacion' => $alumno->motivo_estado_nivelacion,
                'fecha_estado_nivelacion' => $alumno->fecha_estado_nivelacion,
            ],
        ]);
    }
}
