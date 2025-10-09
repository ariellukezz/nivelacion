<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Alumno;
use App\Models\Docente;
use App\Models\Usuario;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class UsuarioController extends Controller
{

    public function index()
    {
        return Inertia::render('Usuarios/index');
    }


    public function getUsuarios(Request $request){

        $res = Usuario::select(
            'users.id', 'users.nombres', 'users.apellidos', 'users.email', 'users.estado',
            'programa.id as id_programa', 'programa.programa as programa',
            'rol.id as id_rol', 'rol.nombre as rol'
        )
        ->leftjoin('programa','users.programa_id','programa.id')
        ->join('rol','users.rol','rol.id')
        ->where(function ($query) use ($request) {
            return $query
                ->orWhere('users.nombres', 'LIKE', '%' . $request->term . '%')
                ->orWhere('users.apellidos', 'LIKE', '%' . $request->term . '%')
                ->orWhere('programa.programa', 'LIKE', '%' . $request->term . '%')
                ->orWhere('rol.nombre', 'LIKE', '%' . $request->term . '%');
        })->orderBy('users.id', 'DESC')
        ->paginate(10);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }


    public function save(Request $request ) {

        $usuario = null;
        if (!$request->id) {
            $usuario = Usuario::create([
                'nombres' => $request->nombres,
                'apellidos' => $request->apellidos,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'programa_id' => $request->programa,
                'rol' => $request->rol,
                'estado' => $request->estado,
                'estado_contraseña' => 1,
                'id_usuario' => auth()->id()
            ]);
            $this->response['tipo'] = 'success';
            $this->response['titulo'] = 'REGISTRO NUEVO';
            $this->response['mensaje'] = 'Usuario '.$usuario->nombres.' creado con exito';
            $this->response['estado'] = true;
            $this->response['datos'] = $usuario;
        } else {

            $usuario = Usuario::find($request->id);
            $usuario->nombres= $request->nombres;
            $usuario->apellidos = $request->apellidos;
            $usuario->email = $request->email;
            $usuario->estado = $request->estado;
            $usuario->programa_id = $request->programa;
            $usuario->rol = $request->rol;
            $usuario->id_usuario = auth()->id();
            $usuario->save();
            $this->response['tipo'] = 'info';
            $this->response['titulo'] = '!REGISTRO MODIFICADO!';
            $this->response['mensaje'] = 'Usuario '.$usuario->nombres.' acaba de ser modificado.';
            $this->response['estado'] = true;
            $this->response['datos'] = $usuario;
        }

    return response()->json($this->response, 200);
  }


  public function delete($id){
    $usuario = Usuario::find($id);
    $p = $usuario;
    $usuario->delete();

    $this->response['tipo'] = 'error';
    $this->response['titulo'] = '!REGISTRO ELIMINADO!';
    $this->response['mensaje'] = 'El Usuario '.$p->nombre.' acaba de ser eliminado.';
    $this->response['estado'] = true;
    $this->response['datos'] = $p;
    return response()->json($this->response, 200);
  }


    public function getUsuarioAdministrador(Request $request){

        $res = DB::select('SELECT users.nombres, programa.escuela, users.estado_contraseña as e_contra  FROM users
        JOIN programa ON programa.id = users.programa_id
        WHERE users.id = '. auth()->user()->id);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }

//     public function getUsuarioAdministrador(Request $request)
// {
//     $query = 'SELECT
//             users.nombres,
//             programa.escuela,
//             users.estado_contraseña AS e_contra,
//             IFNULL(notificaciones.activo, 0) AS e_notificacion
//         FROM users
//         JOIN programa ON programa.id = users.programa_id
//         LEFT JOIN notificaciones ON users.id = notificaciones.user_id
//             AND notificaciones.activo = 1
//             AND notificaciones.leido = 0
//         WHERE users.id = ?
//         LIMIT 1';
//     $res = DB::select($query, [auth()->user()->id]);
//     $this->response['estado'] = true;
//     $this->response['datos'] = $res;
//     return response()->json($this->response, 200);
// }



    public function getUsuarioDocente(Request $request){

        $res = DB::select('SELECT docente.nombres, docente.paterno, docente.materno, programa.escuela as user_escuela,
        users.estado_contraseña as e_contra, rol.nombre as escuela
        FROM docente
        JOIN users ON users.id = docente.usuario_id
        join rol ON users.rol = rol.id
        JOIN programa ON programa.id IN (SELECT users.programa_id FROM users WHERE users.id = docente.id_usuario)
        WHERE users.id = '. auth()->user()->id);
        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }

    public function getUsuarioSupervisor(Request $request){

        $res = DB::select('SELECT users.nombres, programa.escuela, users.estado_contraseña as e_contra  FROM users
        JOIN programa ON programa.id = users.programa_id
        WHERE users.id = '. auth()->user()->id);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }

    public function getUsuarioSuperadmi(Request $request){

        $res = DB::select('SELECT users.nombres, programa.escuela, users.estado_contraseña as e_contra  FROM users
        JOIN programa ON programa.id = users.programa_id
        WHERE users.id = '. auth()->user()->id);

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }

  //GET USUARIO ESTUDIANTE
    public function getUsuarioEstudiante(Request $request){

        $res = Alumno::select('estudiante.nombres', 'estudiante.paterno', 'estudiante.materno', 'programa.escuela', 'users.estado_contraseña as e_contra')
        ->join('users','users.id','estudiante.usuario_id')
        ->join('datos_ingreso','datos_ingreso.codigo_est','estudiante.codigo_est')
       //bdhh ->join('datos_ingreso','datos_ingreso.dni','estudiante.dni')
        ->join('programa','datos_ingreso.id_programa','programa.id')
        ->where('users.id','=', auth()->user()->id)->get();

        $this->response['estado'] = true;
        $this->response['datos'] = $res;
        return response()->json($this->response, 200);

    }

    public function saveNewContra(Request $request){
        $usuario = Usuario::find(auth()->user()->id);
        $usuario->password = Hash::make($request->contra);
        $usuario->estado_contraseña = 0;
        $usuario->save();

        $this->response['tipo'] = 'success';
        $this->response['titulo'] = '!CONTRASEÑA ACTUALIZADA!';
        $this->response['mensaje'] = '';
        $this->response['estado'] = true;
        return response()->json($this->response, 200);

    }








// Obtener notificación activa al inicio (igual que con contraseña)
public function getNoti()
{
    $n = Notificacion::where('user_id', auth()->id())
        ->where('activo', 1)
        ->where('leido', 0)
        ->orderByDesc('id')
        ->first();

    $this->response['estado'] = true;
    $this->response['datos']  = $n; // null si no hay
    return response()->json($this->response, 200);
}

// Marcar como leída
public function readNoti($id)
{
    $n = Notificacion::where('id', $id)
        ->where('user_id', auth()->id())
        ->firstOrFail();

    $n->leido = 1;
    $n->save();

    $this->response['estado']  = true;
    $this->response['mensaje'] = 'Notificación leída';
    return response()->json($this->response, 200);
}






// public function buscarUsuariosNotificaciones(Request $request)
// {
//     if (!Auth::check()) {
//         return response()->json(['message' => 'Usuario no autenticado'], 401);
//     }

//     $rolId       = $request->input('rol_id');            // 1=admin, 4=docente, 5=estudiante
//     $programaId  = $request->input('programa_id');       // opcional (para estudiantes por programa)
//     $escuelaId   = $request->input('escuela_id');        // opcional (para admins por escuela)
//     $term        = trim($request->input('term', ''));    // nombres, apellidos, dni, codigo_est, email
//     $limite      = (int)($request->input('limite', 50));

//     // Respuestas homogéneas: id, nombre_completo, rol_id, rol_nombre, programa/escuela
//     if ($rolId == 5) {
//         // Estudiantes
//         $q = DB::table('users as us')
//             ->join('rol as r', 'r.id', '=', 'us.rol')
//             ->join('estudiante as est', 'us.id', '=', 'est.usuario_id')
//             ->join('datos_ingreso as di', 'est.codigo_est', '=', 'di.codigo_est')
//             ->join('programa as p', 'di.id_programa', '=', 'p.id')
//             ->where('r.id', 5);

//         if ($programaId) {
//             $q->where('p.id', $programaId);
//         }

//         if ($term !== '') {
//             $q->where(function($q2) use ($term) {
//                 $q2->where('est.nombres', 'like', "%{$term}%")
//                    ->orWhere('est.paterno', 'like', "%{$term}%")
//                    ->orWhere('est.materno', 'like', "%{$term}%")
//                    ->orWhere('est.dni', 'like', "%{$term}%")
//                    ->orWhere('est.codigo_est', 'like', "%{$term}%")
//                    ->orWhere('us.email', 'like', "%{$term}%");
//             });
//         }

//         $usuarios = $q->select([
//                 'us.id',
//                 DB::raw("CONCAT(est.paterno,' ',est.materno,', ',est.nombres) as nombre_completo"),
//                 'r.id as rol_id',
//                 'r.nombre as rol_nombre',
//                 'p.id as programa_id',
//                 'p.programa'
//             ])
//             ->orderBy('est.paterno')
//             ->limit($limite)
//             ->get();

//     } elseif ($rolId == 4) {
//         // Docentes
//         $q = DB::table('users as us')
//             ->join('rol as r', 'us.rol', '=', 'r.id')
//             ->join('docente as doc', 'us.id', '=', 'doc.usuario_id')
//             ->where('r.id', 4);

//         if ($term !== '') {
//             $q->where(function($q2) use ($term) {
//                 $q2->where('doc.nombres', 'like', "%{$term}%")
//                    ->orWhere('doc.paterno', 'like', "%{$term}%")
//                    ->orWhere('doc.materno', 'like', "%{$term}%")
//                    ->orWhere('doc.nro_doc', 'like', "%{$term}%")
//                    ->orWhere('us.email', 'like', "%{$term}%");
//             });
//         }

//         $usuarios = $q->select([
//                 'us.id',
//                 DB::raw("CONCAT(doc.paterno,' ',doc.materno,', ',doc.nombres) as nombre_completo"),
//                 'r.id as rol_id',
//                 'r.nombre as rol_nombre'
//             ])
//             ->orderBy('doc.paterno')
//             ->limit($limite)
//             ->get();

//     } else {
//         // Administradores (u otro rol 1)
//         $q = DB::table('users as us')
//             ->join('rol as r', 'us.rol', '=', 'r.id')
//             ->leftJoin('escuela as es', 'us.id_escuela', '=', 'es.id')
//             ->where('r.id', 1);

//         if ($escuelaId) {
//             $q->where('es.id', $escuelaId);
//         }

//         if ($term !== '') {
//             $q->where(function($q2) use ($term) {
//                 $q2->where('us.nombres', 'like', "%{$term}%")
//                    ->orWhere('us.apellidos', 'like', "%{$term}%")
//                    ->orWhere('us.email', 'like', "%{$term}%");
//             });
//         }

//         $usuarios = $q->select([
//                 'us.id',
//                 DB::raw("CONCAT(us.apellidos,', ',us.nombres) as nombre_completo"),
//                 'r.id as rol_id',
//                 'r.nombre as rol_nombre',
//                 'es.id as escuela_id',
//                 'es.nombre as escuela'
//             ])
//             ->orderBy('us.apellidos')
//             ->limit($limite)
//             ->get();
//     }

//     return response()->json([
//         'estado' => true,
//         'datos'  => $usuarios
//     ], 200);
// }



public function buscarUsuariosNotificaciones(Request $request)
{
    if (!Auth::check()) {
        return response()->json(['message' => 'Usuario no autenticado'], 401);
    }

    $rolId       = $request->input('rol_id');            // 1=admin, 4=docente, 5=estudiante
    $programaId  = $request->input('programa_id');       // opcional (para estudiantes por programa)
    $escuelaId   = $request->input('escuela_id');        // opcional (para admins por escuela)
    $term        = trim($request->input('term', ''));    // nombres, apellidos, dni, codigo_est, email
    $limite      = (int)($request->input('limite', 50));

    // Respuestas homogéneas: id, nombre_completo, rol_id, rol_nombre, programa/escuela
    if ($rolId == 5) {
        // Estudiantes
        $q = DB::table('users as us')
            ->join('rol as r', 'r.id', '=', 'us.rol')
            ->join('estudiante as est', 'us.id', '=', 'est.usuario_id')
            ->join('datos_ingreso as di', 'est.codigo_est', '=', 'di.codigo_est')
            ->join('programa as p', 'di.id_programa', '=', 'p.id')
            ->where('r.id', 5);

        if ($programaId) {
            $q->where('p.id', $programaId);
        }

        if ($term !== '') {
            $q->where(function($q2) use ($term) {
                $q2->where('est.nombres', 'like', "%{$term}%")
                   ->orWhere('est.paterno', 'like', "%{$term}%")
                   ->orWhere('est.materno', 'like', "%{$term}%")
                   ->orWhere('est.dni', 'like', "%{$term}%")
                   ->orWhere('est.codigo_est', 'like', "%{$term}%")
                   ->orWhere('us.email', 'like', "%{$term}%");
            });
        }

        $usuarios = $q->select([
                'us.id',
                DB::raw("CONCAT(est.paterno,' ',est.materno,', ',est.nombres) as nombre_completo"),
                'r.id as rol_id',
                'r.nombre as rol_nombre',
                'p.id as programa_id',
                'p.programa'
            ])
            ->orderBy('est.paterno')
            ->limit($limite)
            ->get();

    } elseif ($rolId == 4) {
        // Docentes
        $q = DB::table('users as us')
            ->join('rol as r', 'us.rol', '=', 'r.id')
            ->join('docente as doc', 'us.id', '=', 'doc.usuario_id')
            ->where('r.id', 4);

        if ($term !== '') {
            $q->where(function($q2) use ($term) {
                $q2->where('doc.nombres', 'like', "%{$term}%")
                   ->orWhere('doc.paterno', 'like', "%{$term}%")
                   ->orWhere('doc.materno', 'like', "%{$term}%")
                   ->orWhere('doc.nro_doc', 'like', "%{$term}%")
                   ->orWhere('us.email', 'like', "%{$term}%");
            });
        }

        $usuarios = $q->select([
                'us.id',
                DB::raw("CONCAT(doc.paterno,' ',doc.materno,', ',doc.nombres) as nombre_completo"),
                'r.id as rol_id',
                'r.nombre as rol_nombre'
            ])
            ->orderBy('doc.paterno')
            ->limit($limite)
            ->get();

    } else {
        // Administradores (u otro rol 1)
        $q = DB::table('users as us')
            ->join('rol as r', 'us.rol', '=', 'r.id')
            ->leftJoin('escuela as es', 'us.id_escuela', '=', 'es.id')
            ->where('r.id', 1);

        if ($escuelaId) {
            $q->where('es.id', $escuelaId);
        }

        if ($term !== '') {
            $q->where(function($q2) use ($term) {
                $q2->where('us.nombres', 'like', "%{$term}%")
                   ->orWhere('us.apellidos', 'like', "%{$term}%")
                   ->orWhere('us.email', 'like', "%{$term}%");
            });
        }

        $usuarios = $q->select([
                'us.id',
                DB::raw("CONCAT(us.apellidos,', ',us.nombres) as nombre_completo"),
                'r.id as rol_id',
                'r.nombre as rol_nombre',
                'es.id as escuela_id',
                'es.nombre as escuela'
            ])
            ->orderBy('us.apellidos')
            ->limit($limite)
            ->get();
    }

    return response()->json([
        'estado' => true,
        'datos'  => $usuarios
    ], 200);
}



public function listarNotificaciones(Request $request)
{
    if (!Auth::check()) {
        return response()->json(['message' => 'Usuario no autenticado'], 401);
    }

    $rolId       = $request->input('rol_id');     // opcional
    $programaId  = $request->input('programa_id'); // opcional (para estudiantes)
    $escuelaId   = $request->input('escuela_id');  // opcional (para admins)
    $term        = trim($request->input('term', ''));
    $perPage     = (int)($request->input('per_page', 10));

    $q = DB::table('notificaciones as n')
        ->join('users as us', 'us.id', '=', 'n.user_id')
        ->join('rol as r', 'r.id', '=', 'us.rol')
        ->leftJoin('estudiante as est', 'us.id', '=', 'est.usuario_id')
        ->leftJoin('docente as doc', 'us.id', '=', 'doc.usuario_id')
        ->leftJoin('datos_ingreso as di', function($j){
            $j->on('est.codigo_est', '=', 'di.codigo_est');
        })
        ->leftJoin('programa as p', 'di.id_programa', '=', 'p.id')
        ->leftJoin('escuela as es', 'us.id_escuela', '=', 'es.id');

    if ($rolId) {
        $q->where('r.id', $rolId);
    }
    if ($programaId) {
        $q->where('p.id', $programaId);
    }
    if ($escuelaId) {
        $q->where('es.id', $escuelaId);
    }
    if ($term !== '') {
        $q->where(function($w) use ($term) {
            $w->where('n.titulo', 'like', "%{$term}%")
              ->orWhere('n.mensaje', 'like', "%{$term}%")
              ->orWhere('us.nombres', 'like', "%{$term}%")
              ->orWhere('us.apellidos', 'like', "%{$term}%")
              ->orWhere('us.email', 'like', "%{$term}%")
              ->orWhere('est.codigo_est', 'like', "%{$term}%")
              ->orWhere('est.dni', 'like', "%{$term}%")
              ->orWhere('doc.nro_doc', 'like', "%{$term}%");
        });
    }

    $q->select([
        'n.id',
        'n.user_id',
        'n.titulo',
        'n.mensaje',
        'n.tipo',
        'n.url',
        'n.imagen_url',
        'n.leido',
        'n.activo',
        'n.created_at',
        'r.id as rol_id',
        'r.nombre as rol_nombre',
        DB::raw("COALESCE(CONCAT(est.paterno,' ',est.materno,', ',est.nombres), CONCAT(doc.paterno,' ',doc.materno,', ',doc.nombres), CONCAT(us.apellidos,', ',us.nombres)) as usuario"),
        'p.id as programa_id',
        'p.programa',
        'es.id as escuela_id',
        'es.nombre as escuela'
    ])
    ->orderByDesc('n.id');

    $res = $q->paginate($perPage);

    return response()->json([
        'estado' => true,
        'datos'  => $res
    ], 200);
}




public function guardarNotificacion(Request $request)
{
    if (!Auth::check()) {
        return response()->json(['message' => 'Usuario no autenticado'], 401);
    }

    $validated = $request->validate([
        'titulo'      => 'nullable|string|max:255',
        'mensaje'     => 'required|string',
        'tipo'        => 'nullable|string|in:info,success,warning,warn,error,danger',
        'url'         => 'nullable|string|max:255',
        'imagen_url'  => 'nullable|string|max:255', // si viene como URL ya existente
        'activo'      => 'nullable|boolean',
        // targeting
        'user_ids'    => 'array',        // opcional: lista directa de usuarios
        'user_ids.*'  => 'integer',
        'rol_id'      => 'nullable|in:1,4,5',
        'programa_id' => 'nullable|integer',
        'escuela_id'  => 'nullable|integer',
        'term'        => 'nullable|string|max:150',
        // archivo de imagen (opcional, uno de los dos enfoques: archivo o imagen_url)
        'imagen_file' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
    ]);

    $titulo     = $validated['titulo'] ?? null;
    $mensaje    = $validated['mensaje'];
    $tipo       = $validated['tipo'] ?? 'info';
    $url        = $validated['url'] ?? null;
    $activo     = array_key_exists('activo', $validated) ? (bool)$validated['activo'] : true;

    // Procesar imagen: si suben archivo, priorizamos ese; si no, queda imagen_url tal cual
    $imagenUrl = $validated['imagen_url'] ?? null;
    if ($request->hasFile('imagen_file')) {
        $path = $request->file('imagen_file')->store('notificaciones', 'public');
        $imagenUrl = Storage::url($path);
    }

    // 1) Resolver destinatarios: user_ids directos o por filtros
    $userIds = collect($validated['user_ids'] ?? []);

    if ($userIds->isEmpty()) {
        $rolId      = $validated['rol_id'] ?? null;
        $programaId = $validated['programa_id'] ?? null;
        $escuelaId  = $validated['escuela_id'] ?? null;
        $term       = trim($validated['term'] ?? '');

        if (!$rolId) {
            return response()->json(['message' => 'Debe indicar user_ids o rol_id para seleccionar destinatarios'], 422);
        }

        // Reutilizamos la lógica de búsqueda (similar a buscarUsuariosNotificaciones)
        if ($rolId == 5) {
            $q = DB::table('users as us')
                ->join('rol as r', 'r.id', '=', 'us.rol')
                ->join('estudiante as est', 'us.id', '=', 'est.usuario_id')
                ->join('datos_ingreso as di', 'est.codigo_est', '=', 'di.codigo_est')
                ->join('programa as p', 'di.id_programa', '=', 'p.id')
                ->where('r.id', 5);

            if ($programaId) $q->where('p.id', $programaId);

            if ($term !== '') {
                $q->where(function($q2) use ($term) {
                    $q2->where('est.nombres', 'like', "%{$term}%")
                       ->orWhere('est.paterno', 'like', "%{$term}%")
                       ->orWhere('est.materno', 'like', "%{$term}%")
                       ->orWhere('est.dni', 'like', "%{$term}%")
                       ->orWhere('est.codigo_est', 'like', "%{$term}%")
                       ->orWhere('us.email', 'like', "%{$term}%");
                });
            }

            $userIds = $q->pluck('us.id')->unique()->values();

        } elseif ($rolId == 4) {
            $q = DB::table('users as us')
                ->join('rol as r', 'us.rol', '=', 'r.id')
                ->join('docente as doc', 'us.id', '=', 'doc.usuario_id')
                ->where('r.id', 4);

            if ($term !== '') {
                $q->where(function($q2) use ($term) {
                    $q2->where('doc.nombres', 'like', "%{$term}%")
                       ->orWhere('doc.paterno', 'like', "%{$term}%")
                       ->orWhere('doc.materno', 'like', "%{$term}%")
                       ->orWhere('doc.nro_doc', 'like', "%{$term}%")
                       ->orWhere('us.email', 'like', "%{$term}%");
                });
            }

            $userIds = $q->pluck('us.id')->unique()->values();

        } else {
            $q = DB::table('users as us')
                ->join('rol as r', 'us.rol', '=', 'r.id')
                ->leftJoin('escuela as es', 'us.id_escuela', '=', 'es.id')
                ->where('r.id', 1);

            if ($escuelaId) $q->where('es.id', $escuelaId);

            if ($term !== '') {
                $q->where(function($q2) use ($term) {
                    $q2->where('us.nombres', 'like', "%{$term}%")
                       ->orWhere('us.apellidos', 'like', "%{$term}%")
                       ->orWhere('us.email', 'like', "%{$term}%");
                });
            }

            $userIds = $q->pluck('us.id')->unique()->values();
        }
    }

    if ($userIds->isEmpty()) {
        return response()->json(['estado' => true, 'creadas' => 0, 'mensaje' => 'No se encontraron destinatarios.'], 200);
    }

    // 2) Construir filas
    $now  = now();
    $rows = $userIds->map(function($uid) use ($titulo, $mensaje, $tipo, $url, $imagenUrl, $activo, $now) {
        return [
            'user_id'    => $uid,
            'titulo'     => $titulo,
            'mensaje'    => $mensaje,
            'leido'      => 0,
            'tipo'       => $tipo,
            'url'        => $url,
            'imagen_url' => $imagenUrl,
            'activo'     => $activo ? 1 : 0,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    })->all();

    // 3) Insertar en bloque
    DB::table('notificaciones')->insert($rows);

    return response()->json([
        'estado'  => true,
        'creadas' => count($rows),
        'mensaje' => 'Notificaciones creadas correctamente.'
    ], 201);
}




public function actualizarNotificacion(Request $request, $id)
{
    if (!Auth::check()) {
        return response()->json(['message' => 'Usuario no autenticado'], 401);
    }

    $n = Notificacion::find($id);
    if (!$n) {
        return response()->json(['message' => 'Notificación no encontrada'], 404);
    }

    $validated = $request->validate([
        'titulo'      => 'nullable|string|max:255',
        'mensaje'     => 'nullable|string',
        'tipo'        => 'nullable|string|in:info,success,warning,warn,error,danger',
        'url'         => 'nullable|string|max:255',
        'imagen_url'  => 'nullable|string|max:255',
        'activo'      => 'nullable|boolean',
        'leido'       => 'nullable|boolean',
        'imagen_file' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
    ]);

    // Reemplazo de imagen si suben archivo
    if ($request->hasFile('imagen_file')) {
        $path = $request->file('imagen_file')->store('notificaciones', 'public');
        $n->imagen_url = Storage::url($path);
    } elseif (array_key_exists('imagen_url', $validated)) {
        $n->imagen_url = $validated['imagen_url']; // puede ser vacío para limpiar
    }

    if (array_key_exists('titulo', $validated))     $n->titulo  = $validated['titulo'];
    if (array_key_exists('mensaje', $validated))    $n->mensaje = $validated['mensaje'];
    if (array_key_exists('tipo', $validated))       $n->tipo    = $validated['tipo'];
    if (array_key_exists('url', $validated))        $n->url     = $validated['url'];
    if (array_key_exists('activo', $validated))     $n->activo  = (bool)$validated['activo'];
    if (array_key_exists('leido', $validated))      $n->leido   = (bool)$validated['leido'];

    $n->save();

    return response()->json([
        'estado'  => true,
        'mensaje' => 'Notificación actualizada',
        'datos'   => $n
    ], 200);
}



public function eliminarNotificacion($id)
{
    if (!Auth::check()) {
        return response()->json(['message' => 'Usuario no autenticado'], 401);
    }

    $n = Notificacion::find($id);
    if (!$n) {
        return response()->json(['message' => 'Notificación no encontrada'], 404);
    }

    $n->delete();

    return response()->json([
        'estado'  => true,
        'mensaje' => 'Notificación eliminada'
    ], 200);
}


public function subirImagenNotificacion(Request $request)
{
    if (!Auth::check()) {
        return response()->json(['message' => 'Usuario no autenticado'], 401);
    }

    $validated = $request->validate([
        'imagen_file' => 'required|file|mimes:png,jpg,jpeg|max:2048',
    ]);

    $path = $request->file('imagen_file')->store('notificaciones', 'public');
    $url  = Storage::url($path);

    return response()->json([
        'estado' => true,
        'url'    => $url
    ], 201);
}


}
