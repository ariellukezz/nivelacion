<?php

namespace App\Http\Controllers;

use App\Models\Periodo;
use App\Models\PermisoAsignacionEscuela;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermisoAsignacionController extends Controller
{
    public function director()
    {
        $idEscuela = (int) (auth()->user()->id_escuela ?? 0);
        $idPeriodo = Periodo::activoId();

        if (!$idEscuela || !$idPeriodo) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'No se pudo determinar la escuela o el período activo.',
            ], 422);
        }

        return response()->json([
            'estado' => true,
            'datos' => PermisoAsignacionEscuela::obtenerPara($idEscuela, $idPeriodo),
            'periodo_activo' => $idPeriodo,
        ]);
    }

    public function superadmin(Request $request)
    {
        $idPeriodo = (int) ($request->input('id_periodo') ?: Periodo::activoId());
        $idEscuela = (int) $request->input('id_escuela', 0);

        $periodos = DB::table('periodo')
            ->select('id_periodo as value', 'nombre as label', 'estado')
            ->orderByDesc('id_periodo')
            ->get();

        $datos = null;
        if ($idEscuela && $idPeriodo) {
            $existeEscuela = DB::table('escuela')->where('id', $idEscuela)->exists();
            $existePeriodo = DB::table('periodo')->where('id_periodo', $idPeriodo)->exists();

            if (!$existeEscuela || !$existePeriodo) {
                return response()->json([
                    'estado' => false,
                    'mensaje' => 'La escuela o el período seleccionado no existe.',
                ], 404);
            }

            $datos = PermisoAsignacionEscuela::obtenerPara($idEscuela, $idPeriodo);
        }

        return response()->json([
            'estado' => true,
            'datos' => $datos,
            'periodo_activo' => Periodo::activoId(),
            'periodos' => $periodos,
        ]);
    }

    public function guardar(Request $request)
    {
        $data = $request->validate([
            'id_escuela' => ['required', 'integer', 'exists:escuela,id'],
            'id_periodo' => ['required', 'integer', 'exists:periodo,id_periodo'],
            'puede_crear_curso' => ['required', 'boolean'],
            'puede_editar_curso' => ['required', 'boolean'],
            'puede_eliminar_curso' => ['required', 'boolean'],
            'puede_matricular' => ['required', 'boolean'],
            'puede_asignar_docente' => ['required', 'boolean'],
        ]);

        $permiso = PermisoAsignacionEscuela::updateOrCreate(
            [
                'id_escuela' => (int) $data['id_escuela'],
                'id_periodo' => (int) $data['id_periodo'],
            ],
            [
                'puede_crear_curso' => (bool) $data['puede_crear_curso'],
                'puede_editar_curso' => (bool) $data['puede_editar_curso'],
                'puede_eliminar_curso' => (bool) $data['puede_eliminar_curso'],
                'puede_matricular' => (bool) $data['puede_matricular'],
                'puede_asignar_docente' => (bool) $data['puede_asignar_docente'],
                'usuario_modificacion_id' => auth()->id(),
            ]
        );

        return response()->json([
            'estado' => true,
            'tipo' => 'success',
            'titulo' => 'PERMISOS ACTUALIZADOS',
            'mensaje' => 'Los permisos de la escuela fueron guardados para el período seleccionado.',
            'datos' => PermisoAsignacionEscuela::obtenerPara(
                (int) $permiso->id_escuela,
                (int) $permiso->id_periodo
            ),
        ]);
    }
}
