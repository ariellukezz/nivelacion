<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Dompdf\Dompdf;
use Dompdf\Options;



class SorteoController extends Controller
{
    /**
     * Devuelve todos los eventos
     */
    public function getEventos()
    {
        $eventos = Evento::orderBy('fecha', 'desc')->get();
        return response()->json($eventos);
    }

    /**
     * Crea o actualiza un evento
     */
    public function guardarEvento(Request $request)
    {
        $data = $request->validate([
            'nombre_evento' => 'required|string|max:100',
            'fecha' => 'required|date',
            'estado' => 'required|in:activo,inactivo'
        ]);

        try {
            DB::beginTransaction();

            if ($request->id) {
                $evento = Evento::find($request->id);

                if (!$evento) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Evento no encontrado'
                    ], 404);
                }

                $evento->update($data);
            } else {
                $evento = Evento::create($data);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Evento guardado correctamente',
                'evento' => $evento
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar el evento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina o cierra un evento
     */
    public function eliminarEvento($id)
    {
        try {
            $evento = Evento::find($id);

            if (!$evento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Evento no encontrado'
                ], 404);
            }

            // Si deseas eliminar completamente:
            // $evento->delete();

            // O mejor: marcar como inactivo (cierre lógico)
            $evento->estado = 'inactivo';
            $evento->save();

            return response()->json([
                'success' => true,
                'message' => 'Evento cerrado correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar el evento: ' . $e->getMessage()
            ], 500);
        }
    }








// Función para obtener datos del estudiante y su evento
    public function obtenerEstudianteEvento($dni)
    {
        $estudiante = DB::table('estudiante AS e')
            ->join('datos_ingreso AS di', 'e.codigo_est', '=', 'di.codigo_est')
            ->join('programa AS p', 'di.id_programa', '=', 'p.id')
            ->join('evento_est AS ev', 'e.codigo_est', '=', 'ev.codigo_est')
            ->where('e.dni', $dni)
            ->select('e.dni', 'e.codigo_est', 'di.semestre', 'p.id AS programa_id', 'p.programa', 'ev.mesa', 'ev.numero', 'ev.firma_ingreso')
            ->first();

        if ($estudiante) {
            return response()->json($estudiante);
        } else {
            return response()->json(['message' => 'Estudiante no encontrado o no registrado en el evento.'], 404);
        }
    }

    // Función para registrar el ingreso del estudiante
public function registrarIngreso(Request $request)
{
    // Validar los datos de entrada
    $validated = $request->validate([
        'dni' => 'required|string',
        'evento_id' => 'required|integer',
    ]);

    // Obtener los datos del estudiante y su asignación en el evento
    $estudiante = DB::table('estudiante AS e')
        ->join('evento_est AS ev', 'e.codigo_est', '=', 'ev.codigo_est')
        ->join('datos_ingreso AS di', 'e.codigo_est', '=', 'di.codigo_est')
        ->join('programa AS p', 'di.id_programa', '=', 'p.id')
        ->where('e.dni', $validated['dni'])
        ->where('ev.evento_id', $validated['evento_id'])
        ->select('e.dni', 'e.codigo_est', 'e.nombres', 'e.paterno', 'e.materno', 'p.programa', 'ev.mesa', 'ev.numero', 'ev.serie', 'ev.firma_ingreso')
        ->first();

    if ($estudiante) {
        // Si el estudiante no ha ingresado, actualizamos su estado
        if (!$estudiante->firma_ingreso) {
            DB::table('evento_est')
                ->where('codigo_est', $estudiante->codigo_est)
                ->update(['firma_ingreso' => 1]);

            return response()->json([
                'success' => true,
                'message' => 'Ingreso registrado correctamente.',
                'estudiante' => [
                    'nombres' => $estudiante->nombres,
                    'paterno' => $estudiante->paterno,
                    'materno' => $estudiante->materno,
                    'programa' => $estudiante->programa,
                ],
                'asignacion' => [
                    'mesa' => $estudiante->mesa,
                    'numero' => $estudiante->numero,
                    'serie' => $estudiante->serie,
                ]
            ]);
        } else {
            // Si el estudiante ya ingresó, aún mostramos los datos pero con un mensaje diferente
            return response()->json([
                'success' => true,
                'message' => 'El estudiante ya ha ingresado. El estudiante ya ha ingresado El estudiante ya ha ingresado El estudiante ya ha ingresado Mostrando su asignación.',
                'estudiante' => [
                    'nombres' => $estudiante->nombres,
                    'paterno' => $estudiante->paterno,
                    'materno' => $estudiante->materno,
                    'programa' => $estudiante->programa,
                ],
                'asignacion' => [
                    'mesa' => $estudiante->mesa,
                    'numero' => $estudiante->numero,
                    'serie' => $estudiante->serie,
                ]
            ]);
        }
    }

    return response()->json(['message' => 'Estudiante no encontrado o no registrado en el evento.'], 404);
}













 public function participantesIngresados($evento_id, Request $request)
    {
        $query = DB::table('estudiante AS e')
            ->join('datos_ingreso AS di', 'e.codigo_est', '=', 'di.codigo_est')
            ->join('programa AS p', 'di.id_programa', '=', 'p.id')
            ->join('evento_est AS ev', 'e.codigo_est', '=', 'ev.codigo_est')
            ->where('ev.firma_ingreso', '1')
            ->where(function ($query) use ($request) {
                if ($request->has('programa_id') && $request->programa_id) {
                    $query->where('p.id', $request->programa_id);
                }
                if ($request->has('programa') && $request->programa) {
                    $query->where('p.programa', 'like', '%' . $request->programa . '%');
                }
                if ($request->has('escuela') && $request->escuela) {
                    $query->where('p.escuela', 'like', '%' . $request->escuela . '%');
                }
                if ($request->has('facultad') && $request->facultad) {
                    $query->where('p.facultad', 'like', '%' . $request->facultad . '%');
                }
                if ($request->has('area') && $request->area) {
                    $query->where('p.area', 'like', '%' . $request->area . '%');
                }
            })
            ->select('e.dni', 'e.codigo_est', 'e.nombres', 'e.paterno', 'e.materno', 'ev.firma_ingreso', 'p.id', 'p.programa', 'p.escuela', 'p.facultad', 'p.area')
            ->get();

        return response()->json($query);
    }

    // Ejecutar el sorteo y obtener ganadores
    public function ejecutarSorteo(Request $request)
{
    $request->validate([
        'numero_ganadores' => 'required|integer|min:1',
        'evento_id' => 'required|integer|exists:eventos,id',
    ]);

    $numero_ganadores = $request->numero_ganadores;
    $evento_id = $request->evento_id;

    // Consulta con filtros
    $query = DB::table('estudiante AS e')
        ->join('evento_est AS ev', 'e.codigo_est', '=', 'ev.codigo_est')
        ->join('datos_ingreso AS di', 'e.codigo_est', '=', 'di.codigo_est')
        ->join('programa AS p', 'di.id_programa', '=', 'p.id')
        ->where('ev.firma_ingreso', '1')
        ->where('ev.evento_id', $evento_id);

    // Aplicar filtros dinámicos
    if ($request->filled('programa_id')) {
        $query->where('p.id', $request->programa_id);
    }
    if ($request->filled('escuela')) {
        $query->where('p.escuela', 'like', '%' . $request->escuela . '%');
    }
    if ($request->filled('facultad')) {
        $query->where('p.facultad', 'like', '%' . $request->facultad . '%');
    }
    if ($request->filled('area')) {
        $query->where('p.area', 'like', '%' . $request->area . '%');
    }

    $participantes = $query
        ->inRandomOrder()
        ->limit($numero_ganadores)
        ->select('e.codigo_est', 'e.dni', 'e.nombres', 'e.paterno', 'e.materno', 'p.programa')
        ->get();

    // Enumerar los ganadores en orden
    foreach ($participantes as $index => $ganador) {
        $ganador->orden_sorteo = $index + 1;
    }

    return response()->json($participantes);
}

public function guardarGanadores(Request $request)
{
    $request->validate([
        'evento_id' => 'required|integer|exists:eventos,id',
        'ganadores' => 'required|array|min:1',
        'ganadores.*.codigo_est' => 'required|string',
        'ganadores.*.orden_sorteo' => 'required|integer',
    ]);

    $evento_id = $request->evento_id;
    $ganadores = $request->ganadores;

    foreach ($ganadores as $ganador) {
        DB::table('sorteos')->updateOrInsert(
            [
                'evento_id' => $evento_id,
                'codigo_est' => $ganador['codigo_est'],
            ],
            [
                'orden_sorteo' => $ganador['orden_sorteo'],
                'fecha_sorteo' => now(),
            ]
        );
    }

    return response()->json(['message' => 'Ganadores guardados correctamente.']);
}




    // Listar los ganadores del evento
    public function listarGanadores($evento_id)
    {
        // Obtener los ganadores del sorteo para el evento específico
        $ganadores = DB::table('ganadores') // Asumiendo que tienes una tabla de ganadores
            ->where('evento_id', $evento_id)
            ->get();

        return response()->json($ganadores);
    }



    public function obtenerFiltros()
    {
        // Obtener los programas
        $programas = DB::table('programa')
            ->select('id', 'programa')
            ->get();

        // Obtener las escuelas
        $escuelas = DB::table('programa')
            ->select('escuela')
            ->distinct() // Para evitar duplicados
            ->get();

        // Obtener las facultades
        $facultades = DB::table('programa')
            ->select('facultad')
            ->distinct() // Para evitar duplicados
            ->get();

        // Obtener las áreas
        $areas = DB::table('programa')
            ->select('area')
            ->distinct() // Para evitar duplicados
            ->get();

        // Devolver todos los filtros
        return response()->json([
            'programas' => $programas,
            'escuelas' => $escuelas,
            'facultades' => $facultades,
            'areas' => $areas,
        ]);
    }




    public function exportarGanadoresPDF($evento_id)
{
    $ganadores = DB::table('sorteos AS s')
        ->join('estudiante AS e', 's.codigo_est', '=', 'e.codigo_est')
        ->join('datos_ingreso AS di', 'e.codigo_est', '=', 'di.codigo_est')
        ->join('programa AS p', 'di.id_programa', '=', 'p.id')
        ->where('s.evento_id', $evento_id)
        ->orderBy('s.orden_sorteo')
        ->select('s.orden_sorteo', 'e.nombres', 'e.paterno', 'e.materno', 'p.programa')
        ->get();

    $html = view('pdf.lista-ganadores', ['ganadores' => $ganadores])->render();

    $options = new Options();
    $options->set('defaultFont', 'Helvetica');
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    return $dompdf->stream("lista-ganadores-evento-$evento_id.pdf");
}


public function exportarGanadoresFiltradoPDF(Request $request, $evento_id)
{
    $query = DB::table('sorteos AS s')
        ->join('estudiante AS e', 's.codigo_est', '=', 'e.codigo_est')
        ->join('datos_ingreso AS di', 'e.codigo_est', '=', 'di.codigo_est')
        ->join('programa AS p', 'di.id_programa', '=', 'p.id')
        ->where('s.evento_id', $evento_id);

    // Filtros
    if ($request->filled('programa_id')) $query->where('p.id', $request->programa_id);
    if ($request->filled('facultad')) $query->where('p.facultad', $request->facultad);
    if ($request->filled('escuela')) $query->where('p.escuela', $request->escuela);
    if ($request->filled('area')) $query->where('p.area', $request->area);

    $ganadores = $query
        ->orderBy('s.orden_sorteo')
        ->select('s.orden_sorteo', 'e.nombres', 'e.paterno', 'e.materno', 'p.programa')
        ->get();

    if ($ganadores->isEmpty()) {
        return response()->json(['message' => 'No hay ganadores con esos filtros.'], 404);
    }

    $html = view('pdf.lista-ganadores', ['ganadores' => $ganadores])->render();

    $options = new \Dompdf\Options();
    $options->set('defaultFont', 'Helvetica');
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    return $dompdf->stream("lista-ganadores-evento-$evento_id.pdf");
}




public function participantesIngresadosPorPrograma($evento_id, Request $request)
{
    $query = DB::table('estudiante AS e')
        ->join('datos_ingreso AS di', 'e.codigo_est', '=', 'di.codigo_est')
        ->join('programa AS p', 'di.id_programa', '=', 'p.id')
        ->join('evento_est AS ev', 'e.codigo_est', '=', 'ev.codigo_est')
        ->where('ev.evento_id', $evento_id)   // filtra por evento
        ->where('ev.firma_ingreso', 1);       // solo los que ingresaron

    // opcionales filtros
    if ($request->filled('programa_id')) $query->where('p.id', $request->programa_id);
    if ($request->filled('programa'))    $query->where('p.programa', 'like', '%'.$request->programa.'%');
    if ($request->filled('escuela'))     $query->where('p.escuela', 'like', '%'.$request->escuela.'%');
    if ($request->filled('facultad'))    $query->where('p.facultad', 'like', '%'.$request->facultad.'%');
    if ($request->filled('area'))        $query->where('p.area', 'like', '%'.$request->area.'%');

    $porPrograma = (clone $query)
        ->select(
            'p.id as programa_id',
            'p.programa',
            DB::raw('COUNT(DISTINCT e.codigo_est) as total_ingresados')
        )
        ->groupBy('p.id', 'p.programa')
        ->orderByDesc('total_ingresados')
        ->get();

    $totalGeneral = (clone $query)
        ->select(DB::raw('COUNT(DISTINCT e.codigo_est) as total_ingresados'))
        ->value('total_ingresados');

    return response()->json([
        'evento_id'     => $evento_id,
        'total_general' => (int) $totalGeneral,
        'por_programa'  => $porPrograma,
    ]);
}

}
