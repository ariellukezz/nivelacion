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


    public function getAlumnosRegistro(Request $request){


        $query_where = [];

        if ($request->programa) array_push($query_where,[DB::raw('programa.id'), '=', $request->programa]);
        $id_competencia = $request->curso;
        $id_escuela = $request->escuela;
        $competencia = "";
        if( $id_competencia === 1 ) { $competencia = "C1_R";}
        if( $id_competencia === 2 ) { $competencia = 'C2_R';}
        if( $id_competencia === 3 ) { $competencia = 'C3_R';}
        if( $id_competencia === 4 ) { $competencia = 'C4_R';}
        if( $id_competencia === 5 ) { $competencia = 'C5_R';}
        if( $id_competencia === 6 ) { $competencia = 'C6_R';}
        if( $id_competencia === 7 ) { $competencia = 'C7_R';}
        if( $id_competencia === 8 ) { $competencia = 'C8_R';}
        if( $id_competencia === 9 ) { $competencia = 'C9_R';}
        if( $id_competencia === 10 ) { $competencia = 'C10_R';}
        if( $id_competencia === 11 ) { $competencia = 'C11_R';}
        $res = DB::table('matriz')
        ->join('datos_ingreso', 'matriz.codigo_est', '=', 'datos_ingreso.codigo_est')
        //bdhh ->join('datos_ingreso', 'matriz.dni', '=', 'datos_ingreso.dni')
        ->join('competencia_programa', 'datos_ingreso.id_programa', '=', 'competencia_programa.id_programa')
        ->join('estudiante', 'estudiante.codigo_est', '=', 'datos_ingreso.codigo_est')
        //bdhh ->join('estudiante', 'estudiante.dni', '=', 'datos_ingreso.dni')
        ->join('programa', 'programa.id', '=', 'datos_ingreso.id_programa')
        ->where($query_where)
        ->whereIn('datos_ingreso.codigo_est', function($query) use ($id_escuela) {
        //bdhh ->whereIn('datos_ingreso.dni', function($query) use ($id_escuela) {
            $query->select('estudiante.codigo_est')
           //bdhh $query->select('estudiante.dni')
                ->from('estudiante')
                ->join('datos_ingreso', 'estudiante.codigo_est', '=', 'datos_ingreso.codigo_est')
               //bdhh ->join('datos_ingreso', 'estudiante.dni', '=', 'datos_ingreso.dni')
                ->join('programa', 'programa.id', '=', 'datos_ingreso.id_programa')
                ->join('escuela', 'escuela.id', '=', 'programa.id_escuela')
                ->where('escuela.id', $id_escuela);
        })
        ->where('competencia_programa.id_competencia', $id_competencia)
        ->where('matriz.' . $competencia, '<=', 10.49)
        ->select('estudiante.id', 'programa.programa', 'datos_ingreso.semestre', 'estudiante.codigo_est', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno')
       //bdhh ->select('estudiante.id', 'programa.programa', 'datos_ingreso.semestre', 'estudiante.dni', 'estudiante.nombres', 'estudiante.paterno', 'estudiante.materno')
        ->distinct() // Se añade distinct para eliminar registros duplicados
        ->get();

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

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
