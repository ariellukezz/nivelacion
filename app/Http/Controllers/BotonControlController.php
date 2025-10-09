<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Periodo;
use App\Models\BotonControl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class BotonControlController extends Controller
{

     public function estadoBoton(Request $request, string $modulo)
    {
        $escuelaId = auth()->user()->id_escuela;
        $usuarioId = auth()->id();

        $control = BotonControl::where('modulo', $modulo)
            ->where(function ($query) use ($escuelaId, $usuarioId) {
                $query->where('id_usuario', $usuarioId)
                      ->orWhere('id_escuela', $escuelaId)
                      ->orWhere(fn($q) => $q->whereNull('id_escuela')->whereNull('id_usuario'));
            })
            ->orderByRaw("CASE WHEN id_usuario = ? THEN 1 WHEN id_escuela = ? THEN 2 ELSE 3 END", [$usuarioId, $escuelaId])
            ->first();

        if ($control && $control->estado == 1 && Carbon::now()->between($control->fecha_inicio, $control->fecha_fin)) {
            return response()->json([
                'habilitado' => true,
                'tiempo_restante' => Carbon::now()->diffInSeconds($control->fecha_fin)
            ]);
        }

        return response()->json(['habilitado' => false, 'tiempo_restante' => 0]);
    }











/**
     * FUNCIÓN PARA EL PANEL DE CONTROL
     * Obtiene los datos de la tabla boton_control para mostrarlos.
     * Es llamada por la ruta POST /get-controles.
     */
    public function listarReglas(Request $request)
    {
        // Añadimos la validación para el nuevo campo de búsqueda 'q'
        $request->validate([
            'q' => 'nullable|string|max:100', // Campo de búsqueda general
            'modulo' => 'nullable|string|max:50',
            'id_escuela' => 'nullable|integer',
        ]);

        $query = BotonControl::query()
            ->leftJoin('users', 'boton_control.id_usuario', '=', 'users.id')
            ->select('boton_control.*', 'users.nombres as nombre_usuario');

        // --- INICIA LA NUEVA LÓGICA DE BÚSQUEDA ---
        // Si se envía un término de búsqueda 'q'
        if ($request->filled('q')) {
            $terminoBusqueda = '%' . $request->q . '%';
            $query->where(function ($subQuery) use ($terminoBusqueda) {
                // Busca en el nombre del módulo O en el nombre del usuario
                $subQuery->where('boton_control.modulo', 'like', $terminoBusqueda)
                         ->orWhere('users.nombres', 'like', $terminoBusqueda);
            });
        }
        // --- FIN DE LA NUEVA LÓGICA DE BÚSQUEDA ---

        // Los filtros existentes siguen funcionando en combinación con la búsqueda
        if ($request->filled('modulo')) {
            $query->where('boton_control.modulo', $request->modulo);
        }
        if ($request->filled('id_escuela')) {
            $query->where('boton_control.id_escuela', $request->id_escuela);
        }

        $controles = $query->orderBy('id', 'desc')->paginate(15);

        return response()->json($controles);
    }

    /**
     * FUNCIÓN PARA EL PANEL DE CONTROL
     * Actualiza una o más reglas.
     * Es llamada por la ruta POST /update-controles.
     */
    public function actualizarReglas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'sometimes|array',
            'ids.*' => 'integer|exists:boton_control,id',
            'filtro_modulo' => 'sometimes|string|max:50',
            'filtro_id_escuela' => 'sometimes|integer',
            'estado' => 'sometimes|boolean',
            'fecha_inicio' => 'sometimes|date',
            'fecha_fin' => 'sometimes|date|after_or_equal:fecha_inicio',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();
        $query = BotonControl::query();

        if (isset($validated['ids']) && !empty($validated['ids'])) {
            $query->whereIn('id', $validated['ids']);
        }
        elseif (isset($validated['filtro_modulo']) || isset($validated['filtro_id_escuela'])) {
            if (isset($validated['filtro_modulo'])) $query->where('modulo', $validated['filtro_modulo']);
            if (isset($validated['filtro_id_escuela'])) $query->where('id_escuela', $validated['filtro_id_escuela']);
        } else {
            return response()->json(['message' => 'Debe especificar IDs o filtros para la actualización.'], 400);
        }

        $datosParaActualizar = [];
        if (array_key_exists('estado', $validated)) $datosParaActualizar['estado'] = $validated['estado'];
        if (isset($validated['fecha_inicio'])) $datosParaActualizar['fecha_inicio'] = $validated['fecha_inicio'];
        if (isset($validated['fecha_fin'])) $datosParaActualizar['fecha_fin'] = $validated['fecha_fin'];

        if (empty($datosParaActualizar)) {
            return response()->json(['message' => 'No hay datos para actualizar.'], 400);
        }

        $filasAfectadas = $query->update($datosParaActualizar);

        return response()->json(['message' => 'Actualización completada.', 'filas_afectadas' => $filasAfectadas]);
    }

}
