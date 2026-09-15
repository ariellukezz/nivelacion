<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Usuario;
use App\Models\Alumno;
use App\Models\Matriz;
use App\Models\DatoIngreso;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use DB;

class AlumnoController extends Controller
{
    public function index()
    {
        return Inertia::render('Alumno/index');
    }


    public function getAlumnos(Request $request){

        $consulta = ['estudiante.id', 'estudiante.codigo_est', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno' , 'estudiante.sexo', 'datos_ingreso.t_examen as tipo_examen', 'programa.programa'];
        //bdhh $consulta = ['estudiante.id', 'estudiante.dni', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno' , 'estudiante.sexo', 'datos_ingreso.t_examen as tipo_examen', 'programa.programa'];
        if($request->codigo == true) { array_push($consulta,'estudiante.codigo'); }
        if($request->telefono == true) { array_push($consulta,'estudiante.telefono'); }
        if($request->colegio == true) { array_push($consulta,'estudiante.nombre_colegio as colegio'); }
        if($request->tipo_colegio == true) { array_push($consulta,'estudiante.tipo_colegio'); }
        if($request->estado_civil == true) { array_push($consulta,'estudiante.estado_civil'); }
        if($request->area == true) { array_push($consulta,'programa.area'); }
        if($request->modalidad == true) { array_push($consulta,'datos_ingreso.modalidad'); }
        // array_push($consulta,$dni);
        // array_push($consulta,$nombres);

        $res = Alumno::select(
            $consulta

            // 'programa.id as id_programa', 'programa.programa as programa',
            // 'rol.id as id_rol', 'rol.nombre as rol'
        )
        ->leftjoin('datos_ingreso','estudiante.codigo_est','datos_ingreso.codigo_est')
       //bdhh ->leftjoin('datos_ingreso','estudiante.dni','datos_ingreso.dni')
        ->leftjoin('programa','programa.id','datos_ingreso.id_programa')
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('estudiante.nombres', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.paterno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.materno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.codigo_est', 'LIKE', '%' . $request->term . '%')
               //bdhh  ->orWhere('estudiante.dni', 'LIKE', '%' . $request->term . '%')
                ->orWhere('programa.programa', 'LIKE', '%' . $request->term . '%')
                ->orWhere('datos_ingreso.t_examen', 'LIKE', '%' . $request->term . '%')
                ->orWhere('programa.area', 'LIKE', '%' . $request->term . '%');
        })->orderBy('estudiante.id', 'DESC')
        ->paginate(10);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }
    public function getAlumnosRegistro(Request $request)
    {
        $idPrograma = (int) $request->input('programa');
        $idCompetencia = (int) $request->input('curso');
        $idCursoActual = (int) $request->input('id_curso', 0);

        if (!$idPrograma || !$idCompetencia) {
            return response()->json([
                'estado' => false,
                'datos' => [],
                'mensaje' => 'Debe seleccionar un programa y una competencia.',
            ], 422);
        }

        $programa = DB::table('programa')
            ->where('id', $idPrograma)
            ->select('id', 'id_escuela')
            ->first();

        if (!$programa) {
            return response()->json([
                'estado' => false,
                'datos' => [],
                'mensaje' => 'Programa no encontrado.',
            ], 404);
        }

        $esSuperadmin = (int) (auth()->user()->rol ?? -1) === 0;
        if (!$esSuperadmin && (int) $programa->id_escuela !== (int) auth()->user()->id_escuela) {
            return response()->json([
                'estado' => false,
                'datos' => [],
                'mensaje' => 'El programa seleccionado no pertenece a su escuela profesional.',
            ], 403);
        }

        $cursoActual = null;
        if ($idCursoActual > 0) {
            $cursoActual = DB::table('curso')
                ->where('id', $idCursoActual)
                ->where('id_programa', $idPrograma)
                ->where('id_competencia', $idCompetencia)
                ->first();

            if (!$cursoActual) {
                return response()->json([
                    'estado' => false,
                    'datos' => [],
                    'mensaje' => 'El curso no coincide con el programa o la competencia seleccionados.',
                ], 422);
            }
        }

        $columnasCompetencia = [
            1 => 'C1_R', 2 => 'C2_R', 3 => 'C3_R', 4 => 'C4_R',
            5 => 'C5_R', 6 => 'C6_R', 7 => 'C7_R', 8 => 'C8_R',
            9 => 'C9_R', 10 => 'C10_R', 11 => 'C11_R',
        ];

        $columnaNota = $columnasCompetencia[$idCompetencia] ?? null;

        if (!$columnaNota) {
            return response()->json([
                'estado' => false,
                'datos' => [],
                'mensaje' => 'La competencia seleccionada no tiene una columna de nota configurada.',
            ], 422);
        }

        $competenciaHabilitada = DB::table('competencia_programa')
            ->where('id_programa', $idPrograma)
            ->where('id_competencia', $idCompetencia)
            ->where('estado', 1)
            ->exists();

        if (!$competenciaHabilitada) {
            return response()->json([
                'estado' => false,
                'datos' => [],
                'mensaje' => 'La competencia no está habilitada para el programa seleccionado.',
            ], 422);
        }

        $res = DB::table('matriz')
            ->join('datos_ingreso', 'matriz.codigo_est', '=', 'datos_ingreso.codigo_est')
            ->join('estudiante', 'estudiante.codigo_est', '=', 'datos_ingreso.codigo_est')
            ->join('programa', 'programa.id', '=', 'datos_ingreso.id_programa')
            ->where('datos_ingreso.id_programa', $idPrograma)
            ->where('estudiante.estado_nivelacion', 1)
            ->where('matriz.' . $columnaNota, '<=', 10.49)
            ->when($cursoActual, function ($query) use ($cursoActual, $idPrograma, $idCompetencia) {
                // Si existen varios grupos para la misma competencia, un alumno
                // no debe aparecer disponible si ya está matriculado en otro grupo.
                $query->whereNotExists(function ($sub) use ($cursoActual, $idPrograma, $idCompetencia) {
                    $sub->select(DB::raw(1))
                        ->from('curso_detalle as cd_otro')
                        ->join('curso as c_otro', 'c_otro.id', '=', 'cd_otro.id_curso')
                        ->whereColumn('cd_otro.id_alumno', 'estudiante.id')
                        ->where('c_otro.id_programa', $idPrograma)
                        ->where('c_otro.id_periodo', $cursoActual->id_periodo)
                        ->where('c_otro.id_competencia', $idCompetencia)
                        ->where('c_otro.id', '<>', $cursoActual->id);
                });
            })
            ->select(
                'estudiante.id',
                'programa.programa',
                'datos_ingreso.semestre',
                'estudiante.codigo_est',
                'estudiante.nombres',
                'estudiante.paterno',
                'estudiante.materno',
                DB::raw('matriz.' . $columnaNota . ' as nota_actual')
            )
            ->when($request->filled('term'), function ($query) use ($request) {
                $term = trim((string) $request->term);
                $query->where(function ($q) use ($term) {
                    $q->where('estudiante.codigo_est', 'LIKE', '%' . $term . '%')
                        ->orWhere('estudiante.nombres', 'LIKE', '%' . $term . '%')
                        ->orWhere('estudiante.paterno', 'LIKE', '%' . $term . '%')
                        ->orWhere('estudiante.materno', 'LIKE', '%' . $term . '%');
                });
            })
            ->distinct()
            ->orderBy('estudiante.paterno')
            ->get();

        return response()->json([
            'estado' => true,
            'datos' => $res,
        ], 200);
    }

    public function getAlumnosRegistroSSSS(Request $request){

        $res = Alumno::select(
            'estudiante.id',
            'estudiante.codigo_est',
           //bdhh 'estudiante.dni',
            'estudiante.nombres',
            'estudiante.paterno',
            'estudiante.materno'
        )
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('estudiante.nombres', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.paterno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.materno', 'LIKE', '%' . $request->term . '%')
                ->orWhere('estudiante.codigo_est', 'LIKE', '%' . $request->term . '%');
               //bdhh ->orWhere('estudiante.dni', 'LIKE', '%' . $request->term . '%');
        })->orderBy('estudiante.id', 'DESC')
        ->paginate(10);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }


    public function excelEstudiante(Request $request)
{
    // Leer parámetro para ignorar duplicados (enviado desde el frontend)
    $ignorar_duplicados = $request->input('ignorar_duplicados', false);

    // Inicializar variables para el reporte
    $reporte = [
        'total_registros' => count($request->datos),
        'registros_exitosos' => 0,
        'duplicados' => [],
        'errores' => [],
        'detalle_errores' => []
    ];

    DB::beginTransaction();

    try {
        foreach ($request->datos as $index => $item) {
            try {
                // Validar si el DNI ya existe en alguna tabla
                $dni = $item['dni'];
                $existeEnAlumnos = Alumno::where('dni', $dni)->exists();
                $existeEnMatriz = Matriz::where('dni', $dni)->exists();
                $existeEnDatosIngreso = DatoIngreso::where('dni', $dni)->exists();

                if ($existeEnAlumnos || $existeEnMatriz || $existeEnDatosIngreso) {
                    $reporte['duplicados'][] = [
                        'linea' => $index + 1,
                        'dni' => $dni,
                        'codigo_est' => $item['codigo_est'] ?? '',
                        'nombre' => ($item['nombres'] ?? '') . ' ' . ($item['paterno'] ?? '') . ' ' . ($item['materno'] ?? ''),
                        'tablas' => [
                            'alumnos' => $existeEnAlumnos,
                            'matriz' => $existeEnMatriz,
                            'datos_ingreso' => $existeEnDatosIngreso
                        ]
                    ];

                    // Si el usuario NO indicó que se ignoren, no procesar este registro
                    if (!$ignorar_duplicados) {
                        continue;
                    }
                }

                // Crear usuario
                $usuario = Usuario::create([
                    'email' => $item['codigo'],
                    'password' => Hash::make($item['dni']),
                    'rol' => 5,
                    'estado' => 1,
                    'estado_contraseña' => 1,
                    'programa_id' => $item['id_programa'],
                    'id_escuela' => $item['id_escuela']
                ]);

                // Crear alumno
                $alumno = new Alumno([
                    'codigo' => $item['codigo'],
                    'dni' => $dni,
                    'codigo_est' => $item['codigo_est'],
                    'paterno' => $item['paterno'],
                    'materno' => $item['materno'],
                    'nombres' => $item['nombres'],
                    'sexo' => $item['sexo'],
                    'email' => $item['email'],
                    'f_nacimiento' => $item['f_nacimiento'],
                    'ubigeo_nacimiento' => $item['ubigeo_nacimiento'],
                    'estado_civil' => $item['estado_civil'],
                    'anio_egreso' => $item['anio_egreso'],
                    'tipo_colegio' => $item['tipo_colegio'],
                    'nombre_colegio' => $item['nombre_colegio'],
                    'ubigeo_colegio' => $item['ubigeo_colegio'],
                    'apto' => $item['apto'],
                    'direccion' => $item['direccion'],
                    'telefono' => $item['telefono'],
                    'usuario_id' => $usuario->id
                ]);
                $alumno->save();

                // Crear registro en matriz
                Matriz::create([
                    'dni' => $dni,
                    'codigo_est' => $item['codigo_est'],
                    'C1' => $item['C1'] ?? 0,
                    'C2' => $item['C2'] ?? 0,
                    'C3' => $item['C3'] ?? 0,
                    'C4' => $item['C4'] ?? 0,
                    'C5' => $item['C5'] ?? 0,
                    'C6' => $item['C6'] ?? 0,
                    'C7' => $item['C7'] ?? 0,
                    'C8' => $item['C8'] ?? 0,
                    'C9' => $item['C9'] ?? 0,
                    'C10' => $item['C10'] ?? 0,
                    'C11' => $item['C11'] ?? 0,
                    'C1_R' => $item['C1_R'] ?? 0,
                    'C2_R' => $item['C2_R'] ?? 0,
                    'C3_R' => $item['C3_R'] ?? 0,
                    'C4_R' => $item['C4_R'] ?? 0,
                    'C5_R' => $item['C5_R'] ?? 0,
                    'C6_R' => $item['C6_R'] ?? 0,
                    'C7_R' => $item['C7_R'] ?? 0,
                    'C8_R' => $item['C8_R'] ?? 0,
                    'C9_R' => $item['C9_R'] ?? 0,
                    'C10_R' => $item['C10_R'] ?? 0,
                    'C11_R' => $item['C11_R'] ?? 0,
                    'nivelar' => $item['nivelar'] ?? 0,
                    'no_nivelar' => $item['no_nivelar'] ?? 0
                ]);

                // Crear registro en datos_ingreso
                DatoIngreso::create([
                    'dni' => $dni,
                    'codigo_est' => $item['codigo_est'],
                    'anio' => $item['anio'] ?? date('Y'),
                    'semestre' => $item['semestre'] ?? '-',
                    'f_examen' => $item['f_examen'] ?? now(),
                    't_examen' => $item['t_examen'] ?? 'Ordinario',
                    'puntaje_examen' => $item['puntaje_examen'] ?? '0',
                    'modalidad' => $item['modalidad'] ?? 'Regular',
                    'id_programa' => $item['id_programa'],
                    'observacion' => $item['observacion'] ?? ''
                ]);

                $reporte['registros_exitosos']++;

            } catch (\Exception $e) {
                $reporte['errores'][] = $index + 1;
                $reporte['detalle_errores'][] = [
                    'linea' => $index + 1,
                    'dni' => $dni ?? 'No identificado',
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ];
                Log::error("Error al importar estudiante en línea " . ($index + 1) . ": " . $e->getMessage());
            }
        }

        DB::commit();

        return response()->json([
            'estado' => true,
            'mensaje' => 'Importación completada con el siguiente reporte',
            'data' => $reporte
        ], 200);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'estado' => false,
            'mensaje' => 'Error general al importar: ' . $e->getMessage(),
            'reporte' => $reporte
        ], 500);
    }
}






}
