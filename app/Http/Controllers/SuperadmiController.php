<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Curso;
use App\Models\CursoDetalle;
use App\Models\Alumno;
use App\Models\Documento;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;


class SuperadmiController extends Controller
{

    public function getDocumentos(Request $request) {
        // Definir las condiciones iniciales
        $conditions = [];

        if ($request->programa) array_push($conditions,[DB::raw('documento.id_escuela'), '=', $request->programa]);   

        // Realizar la consulta de manera más legible
        $query = Documento::select(
            'documento.tipo', 
            'escuela.nombre AS escuela', 
            'escuela.nombre_corto', 
            'documento.fecha_subida', 
            'users.nombres AS username', 
            'users.apellidos AS userlastname',
            'documento.url'
        )
        ->join('escuela', 'documento.id_escuela', '=', 'escuela.id')
        ->join('users', 'users.id', '=', 'documento.id_usuario')
        ->where(function ($query) use ($request) {
            $query->where('escuela.nombre', 'LIKE', '%' . $request->term . '%')
                ->orWhere('documento.tipo', 'LIKE', '%' . $request->term . '%')
                ->orWhere('users.nombres', 'LIKE', '%' . $request->term . '%');
        });

        // Aplicar condiciones adicionales si las hay
        if (!empty($conditions)) {
            $query->where($conditions);
        }

        // $res = $query->paginate($request->paginashoja);
        $res = $query->paginate(200);

        // Devolver una respuesta JSON
        return response()->json([
            'estado' => true,
            'datos' => $res,
        ], 200);
    }

    public function getAlumnos($competencia) {
        $c=$competencia;

        $estudiantes = DB::table('estudiante AS e')
        ->select(
            'e.id',
            'e.dni',
            'e.codigo',
            'e.paterno',
            'e.materno',
            'e.nombres',
            'e.sexo',
            'e.email',
            'e.anio_egreso',
            'e.nombre_colegio',
            'e.telefono',
            'p.programa',
            'c.nombre',
            'd.modalidad',
            'd.semestre',
            'cd.nota',
            'c.id_competencia',
            'm.C'.$competencia.'_R'
        )
        ->join('datos_ingreso AS d', 'e.dni', '=', 'd.dni')
        ->join('matriz AS m', 'e.dni', '=', 'm.dni')
        ->join('programa AS p', 'd.id_programa', '=', 'p.id')
        ->join('curso AS c', function($join) use ($competencia)  {
            $join->on('p.id', '=', 'c.id_programa')
                ->where('c.id_competencia', $competencia);
        })
        ->join('curso_detalle AS cd', function($join)  {
            $join->on('e.id', '=', 'cd.id_alumno')
                ->on('cd.id_curso', '=', 'c.id');
        })
        ->distinct()
        ->get();

        $this->response['estado'] = true;
        $this->response['datos'] = $estudiantes;
        return response()->json($this->response, 200);
    }


    public function getDocentes(Request $request) {

    $docentes = DB::table('docente as d')
    ->select(
        'd.nro_doc', 
        'd.paterno', 
        'd.materno', 
        'd.nombres', 
        'd.sexo', 
        'd.telefono', 
        'd.email', 
        'u.nombres as nombre_usuario', 
        'e.nombre as nombre_escuela')
    ->join('users as u', 'd.id_usuario', '=', 'u.id')
    ->join('escuela as e', 'u.id_escuela', '=', 'e.id')
    ->get();

    $this->response['estado'] = true;
    $this->response['datos'] = $docentes;
    return response()->json($this->response, 200);
        }
}
