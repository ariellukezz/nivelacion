<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataIngresoIntegracionController extends Controller
{
    /**
     * Datos para /data-ingreso.
     *
     * Fuente:
     * - admision_postulante: datos personales, código, proceso y programa.
     * - admision_proceso: relación proceso -> periodo.
     * - admision_matriz: competencias del periodo.
     * - programa / escuela: filtro de la escuela del director.
     *
     * Solo se muestran registros que:
     * 1. pertenecen al periodo activo;
     * 2. tienen código asignado por Admisión;
     * 3. tienen equivalencia de programa en Nivelación;
     * 4. tienen matriz del mismo periodo;
     * 5. pertenecen a la escuela del usuario autenticado.
     */
    public function getIngresantes(Request $request)
    {
        $idEscuela = (int) auth()->user()->id_escuela;

        $periodo = DB::table('periodo')
            ->where('estado', 'activo')
            ->orderByDesc('id_periodo')
            ->select('id_periodo', 'nombre')
            ->first();

        if (!$periodo) {
            return response()->json([
                'estado' => false,
                'mensaje' => 'No existe un periodo activo.',
                'datos' => [],
            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Una sola fila de Admisión por DNI dentro del periodo
        |--------------------------------------------------------------------------
        | Un periodo puede tener varios procesos de Admisión. Si por alguna
        | razón el mismo DNI aparece en más de un proceso del mismo periodo,
        | tomamos el último registro sincronizado que ya tenga código.
        */
        $apiSeleccion = DB::table('admision_postulante as ap')
            ->join(
                'admision_proceso as pr',
                'pr.id_admision',
                '=',
                'ap.id_proceso_admision'
            )
            ->where('pr.id_periodo', $periodo->id_periodo)
            ->whereNotNull('ap.codigo')
            ->where('ap.codigo', '<>', '')
            ->select(
                'ap.dni',
                DB::raw('MAX(ap.id) as api_id')
            )
            ->groupBy('ap.dni');

        /*
        |--------------------------------------------------------------------------
        | Cruce final para DATA INGRESO
        |--------------------------------------------------------------------------
        | La matriz pertenece al periodo y se enlaza temporalmente por DNI.
        | El programa viene desde Admisión y se valida con programa.id.
        */
        $ingresantes = DB::table('admision_matriz as m')
            ->joinSub($apiSeleccion, 'sel', function ($join) {
                $join->on('sel.dni', '=', 'm.dni');
            })
            ->join(
                'admision_postulante as a',
                'a.id',
                '=',
                'sel.api_id'
            )
            ->join(
                'programa as p',
                'p.id',
                '=',
                'a.id_programa_nivelacion'
            )
            ->join(
                'escuela as e',
                'e.id',
                '=',
                'p.id_escuela'
            )
            ->where('m.id_periodo', $periodo->id_periodo)
            ->where('e.id', $idEscuela)
            ->select(
                // Se mantienen alias compatibles con el Vue anterior.
                'a.dni as dni_ingr',
                'a.codigo as codigo_est',
                'a.paterno as primer_apellido',
                'a.materno as segundo_apellido',
                'a.nombres as nombres_ingr',
                'a.sexo',
                'a.email',
                'a.telefono as celular_ingre',

                'p.programa',
                'p.id as id_programa',

                'a.modalidad as mod_ingr',
                'a.proceso_nombre as proceso_ingr',
                'a.id_proceso_admision',

                'a.f_examen',
                'a.puntaje',
                'a.direccion',
                'a.departamento',
                'a.provincia',
                'a.distrito',

                'm.id_periodo',
                'm.observacion as observacion_matriz',

                'm.C1 as i_C1',
                'm.C2 as i_C2',
                'm.C3 as i_C3',
                'm.C4 as i_C4',
                'm.C5 as i_C5',
                'm.C6 as i_C6',
                'm.C7 as i_C7',
                'm.C8 as i_C8',
                'm.C9 as i_C9',
                'm.C10 as i_C10',
                'm.C11 as i_C11',

                'm.C1_R as i_C1_R',
                'm.C2_R as i_C2_R',
                'm.C3_R as i_C3_R',
                'm.C4_R as i_C4_R',
                'm.C5_R as i_C5_R',
                'm.C6_R as i_C6_R',
                'm.C7_R as i_C7_R',
                'm.C8_R as i_C8_R',
                'm.C9_R as i_C9_R',
                'm.C10_R as i_C10_R',
                'm.C11_R as i_C11_R',

                'm.nivelar as i_TN',
                'm.no_nivelar as i_TNN'
            )
            ->orderBy('p.programa')
            ->orderBy('a.paterno')
            ->orderBy('a.materno')
            ->orderBy('a.nombres')
            ->get();

        $programas = $ingresantes
            ->pluck('programa')
            ->filter()
            ->unique()
            ->values();

        $procesos = $ingresantes
            ->pluck('proceso_ingr')
            ->filter()
            ->unique()
            ->values();

        return response()->json([
            'estado' => true,
            'datos' => $ingresantes,
            'periodo_actual' => $periodo->nombre,
            'id_periodo' => $periodo->id_periodo,
            'resumen' => [
                'total' => $ingresantes->count(),
                'programas' => $programas->count(),
                'procesos' => $procesos->count(),
            ],
            'filtros' => [
                'programas' => $programas,
                'procesos' => $procesos,
            ],
        ]);
    }
}
