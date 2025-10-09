<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeController;
use Illuminate\Foundation\Application;
use App\Http\Controllers\DataController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AvanceController;
use App\Http\Controllers\SorteoController;
use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\SuperadmiController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\CoordinadorController;
use App\Http\Controllers\BotonControlController;
use App\Http\Controllers\Auth\RecoveryController;
use App\Http\Controllers\Docente\DashboardController;

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified','admin',])->name('dashboard');


Route::middleware('red')->get('/', fn () => Inertia::render('Auth/Login'));

Route::middleware('auth','admin')->group(function () {
    Route::get('/inicio', fn () => Inertia::render('Inicio/index'))->name('inicio');
    Route::get('/about', fn () => Inertia::render('About'))->name('about');

    Route::get('users', [UserController::class, 'index'])->name('users.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //USUARIOS
    Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuario-index');
    Route::post('get-usuarios', [UsuarioController::class, 'getUsuarios']);
    Route::post('save-usuario', [UsuarioController::class, 'save']);
    Route::get('delete-usuario/{id}', [UsuarioController::class, 'delete']);
    Route::post('/get-usuario', [UsuarioController::class, 'getUsuarioAdministrador']);

    Route::get('/prueba2', [TeController::class, 'getTest']);
    Route::get('/notas-perfiles', fn () => Inertia::render('Admin/Matriz/index'))->name('notas-perfiles');

    // NUEVO
    Route::get('/ingresantes', [TeController::class, 'getIngresantes']);
    Route::get('/data-ingreso', fn () => Inertia::render('Admin/Dataingreso/index'))->name('data-ingreso');

    // Endpoint JSON
    Route::get('/reprobados-nivelacion/data', [TeController::class, 'getReprobadosNivelacion'])->name('reprobados-nivelacion.data');
    // Página Inertia (ajusta el path si quieres otro nombre de carpeta)
    Route::get('/reprobados-nivelacion', fn () =>Inertia::render('Admin/Reprobados/index'))->name('reprobados-nivelacion');


    Route::get('/descargar-archivo/{nombreArchivo}', [ArchivoController::class, 'descargarArchivo']);

    //COORDINADOR
    Route::prefix('coordinador')->group(function () {
        Route::get('/', fn () => Inertia::render('Admin/Coordinador/index'))->name('coordinador');
        Route::post('/save-coordinador', [CoordinadorController::class, 'save']); #-->
        Route::post('/get-coordinadores', [CoordinadorController::class, 'getCoordinadores']);
        Route::get('/delete-coordinador/{id}', [CoordinadorController::class, 'delete']);
        // Route::post('/get-escuelas', [CoordinadorController::class, 'getEscuelas']);

        Route::get('/estudiante', fn () => Inertia::render('Admin/Estudiante/index'))->name('coordinador-estudiante');
        Route::post('/get-alumnos', [CoordinadorController::class, 'getAlumnos']);

        Route::get('/docente', fn () => Inertia::render('Admin/Docente/index'))->name('coordinador-docente');
        Route::post('/get-docentes', [DocenteController::class, 'getDocentes']);
        Route::post('/save-docente', [DocenteController::class, 'save']);
        Route::post('/get-competencia-x-docente', [DocenteController::class, 'getCompetenciasByDocente']);
        Route::get('/delete-docente/{id}', [DocenteController::class, 'delete']);
        Route::get('/get-data-docente/{dni}', [DocenteController::class, 'getDataPrisma']);


        Route::get('/asignacion', fn () => Inertia::render('Admin/Asignacion/index'))->name('coordinador-asignacion');
        Route::post('/get-docente-competencia', [AsignacionController::class, 'getDocentesXcompetencia']);
        Route::post('/save-curso', [AsignacionController::class, 'save']);
        Route::post('/get-cursos', [AsignacionController::class, 'getCursos']);
        Route::post('/asignar-curso-nivelacion', [AsignacionController::class, 'asignarCursoNivelacion']);
        Route::post('/get-detalle-curso', [AsignacionController::class, 'getDetalleCurso']);

        Route::post('/get-competencias', [CoordinadorController::class, 'compes']);

        Route::get('delete-curso/{id}', [CursoController::class, 'delete']);

        //pdf
        Route::get('/generar-pdf/{id}', [AsignacionController::class, 'pdf']);



    });


    Route::get('/alumnos-importar', fn () => Inertia::render('Admin/Alumnos/index'))->name('alumnos-importar');
    Route::post('importar-excel-estudiante', [AlumnoController::class, 'excelEstudiante']);

    //ALUMNO
    Route::get('alumnos', [AlumnoController::class, 'index'])->name('alumno-index');
    Route::post('get-alumnos', [AlumnoController::class, 'getAlumnos']);
    Route::post('get-alumnos-registro', [AlumnoController::class, 'getAlumnosRegistro']);

    //TUTORES
    Route::get('tutores', [DocenteController::class, 'index'])->name('tutor-index');
    Route::post('save-docente', [DocenteController::class, 'save']);
    Route::post('get-docentes', [DocenteController::class, 'getDocentes']);
    Route::get('delete-docente/{id}', [DocenteController::class, 'delete']);

    //ASIGNACIÓN
    Route::get('asignacion', [AsignacionController::class, 'index'])->name('asignacion-index');
    Route::post('get-docente-competencia', [AsignacionController::class, 'getDocentesXcompetencia']);
    Route::post('save-curso', [AsignacionController::class, 'save']);
    Route::post('get-cursos', [AsignacionController::class, 'getCursos']);
    Route::post('asignar-curso-nivelacion', [AsignacionController::class, 'asignarCursoNivelacion']);
    Route::post('get-detalle-curso', [AsignacionController::class, 'getDetalleCurso']);

    //GET DATA
    Route::post('get-programas', [DataController::class, 'getProgramas']);
    Route::post('get-roles', [DataController::class, 'getRoles']);
    Route::post('get-competencias', [DataController::class, 'getCompetencias']);
    Route::post('get-escuelas', [DataController::class, 'getEscuelas']);
    Route::post('get-programas-escuela', [DataController::class, 'getProgramasEscuela']);

    //DOCUMENTOS
    Route::post('documento/resolucion', [DocumentoController::class, 'resolucion']);
    Route::post('documento/plan', [DocumentoController::class, 'plan']);
    Route::post('documento/informe', [DocumentoController::class, 'informe']);
    Route::post('documento/otros', [DocumentoController::class, 'otros']);
    Route::post('documento/dictantes', [DocumentoController::class, 'dictantes']);
    Route::post('get-resoluciones', [DocumentoController::class, 'getResoluciones']);
    Route::post('get-planes', [DocumentoController::class, 'getPlanes']);
    Route::post('get-informes', [DocumentoController::class, 'getInformes']);
    Route::post('get-dictantes', [DocumentoController::class, 'getDictantes']);
    Route::post('get-otros', [DocumentoController::class, 'getOtros']);

    // Route::get('estado-boton', [DocumentoController::class, 'estadoBoton']);
    Route::get('/estado-boton/{modulo}', [BotonControlController::class, 'estadoBoton']);





    // encargados
    Route::get('encargados', fn () => Inertia::render('Admin/Encargado/index'))->name('encargado-index');
    Route::post('get-encargados', [PreguntaController::class, 'getEncargados']);
    Route::post('save-encargado', [PreguntaController::class, 'saveEncargado']);
    Route::delete('delete-encargado/{id}', [PreguntaController::class, 'deleteEncargado']);


});

// Route::middleware('auth','superadmi')->group(function () {
//     Route::get('/docentes-verdadero', fn () => Inertia::render('Superadmi/docentes/index'))->name('super-docente');

//     Route::post('/get-competencia-x-docente', [DocenteController::class, 'getCompetenciasByDocente']);
//     Route::get('/get-data-docente/{dni}', [DocenteController::class, 'getDataPrisma']);

//     //TUTORES
//     Route::get('tutores', [DocenteController::class, 'index'])->name('tutor-index');
//     Route::post('save-docente', [DocenteController::class, 'save2']);
//     Route::post('get-docentes', [DocenteController::class, 'getDocentes']);
//     Route::get('delete-docente/{id}', [DocenteController::class, 'delete']);

//     //ASIGNACIÓN
//     Route::get('asignacion', [AsignacionController::class, 'index'])->name('asignacion-index');
//     Route::post('get-docente-competencia', [AsignacionController::class, 'getDocentesXcompetencia']);
//     Route::post('save-curso', [AsignacionController::class, 'save']);
//     Route::post('get-cursos', [AsignacionController::class, 'getCursos']);
//     Route::post('asignar-curso-nivelacion', [AsignacionController::class, 'asignarCursoNivelacion']);
//     Route::post('get-detalle-curso', [AsignacionController::class, 'getDetalleCurso']);

//     //GET DATA
//     Route::post('get-programas', [DataController::class, 'getProgramas']);
//     Route::post('get-roles', [DataController::class, 'getRoles']);
//     Route::post('get-competencias', [DataController::class, 'getCompetencias']);
//     Route::post('get-escuelas', [DataController::class, 'getEscuelas']);


// });


Route::middleware('auth','docente')->group(function () {
//    Route::get('docente', [DocenteController::class, 'dashboardDocente'])->name('docente-inicio');
    Route::get('/docente', fn () => Inertia::render('Docente/Inicio/inicio'))->name('docente-inicio');
    Route::get('/docente', fn () => Inertia::render('Docente/Inicio/inicio'))->name('docente-inicio');
    Route::get('docente/curso', [CursoController::class, 'cursoDocente'])->name('docente-curso');
    Route::post('docente/get-cursos', [CursoController::class, 'getCursos']);
    Route::post('docente/get-alumnos-curso', [CursoController::class, 'getAlumnosXCurso']);
    Route::post('docente/update-nota', [CursoController::class, 'updateNota']);
    Route::post('/docente/get-usuario', [UsuarioController::class, 'getUsuarioDocente']);
    Route::get('/docente/generar-pdf/{id}', [DocenteController::class, 'pdf']);

    Route::get('docente/encuestas', fn () => Inertia::render('Docente/Encuestas/index'))->name('docente-encuestas');
    Route::get('docente/cursos-encuesta', [CursoController::class, 'getCursosEncuestaD']);
    Route::get('docente/get-preguntas/{encuesta}', [PreguntaController::class, 'getPreguntas']);
    Route::post('docente/save-encuesta-docente', [PreguntaController::class, 'saveRepuestasDocente']);

});


Route::middleware('auth','estudiante')->prefix('estudiante')->group(function () {
    Route::post('/get-usuario', [UsuarioController::class, 'getUsuarioEstudiante']);
    //ESTUDIANTE
    //    Route::get('docente', [DocenteController::class, 'dashboardDocente'])->name('docente-inicio');
    Route::get('/inicio', fn () => Inertia::render('Estudiante/Inicio/index'))->name('estudiante-inicio');
    Route::get('/notas', fn () => Inertia::render('Estudiante/Notas/index'))->name('estudiante-notas');
    Route::get('/encuestas', fn () => Inertia::render('Estudiante/Encuestas/index'))->name('estudiante-encuestas');
    Route::post('/get-notas', [CursoController::class, 'getNotasByAlumno']);
    Route::get('/cursos-encuesta', [CursoController::class, 'getCursosEncuesta']);

    Route::get('/get-preguntas/{encuesta}', [PreguntaController::class, 'getPreguntas']);
    Route::post('/save-encuesta-estudiante', [PreguntaController::class, 'saveRepuestasPostulante']);

    Route::get('/evento', fn () => Inertia::render('Estudiante/Evento/index'))->name('estudiante-evento');
    Route::post('/get-evento-induccion', [CursoController::class, 'getEventoInduccionByAlumno']);


});

Route::post('/save-contrasenia', [UsuarioController::class, 'saveNewContra'])->middleware('auth');


Route::post('/get-noti', [UsuarioController::class, 'getNoti'])->middleware('auth');
Route::post('/read-noti/{id}', [UsuarioController::class, 'readNoti'])->middleware('auth');

   //supervisor


Route::middleware('auth','supervisor')->prefix('supervisor')->group(function () {
    Route::get('/', fn () => Inertia::render('Supervisor/index'))->name('supervisor-inicio');
    Route::get('/asignacion', fn () => Inertia::render('Supervisor/Asignacion/asignacion'))->name('supervisor-asignacion');
    Route::post('/get-usuario', [UsuarioController::class, 'getUsuarioSupervisor']);
    Route::post('/get-documentos', [SupervisorController::class, 'getDocumentos']);
    Route::get('/avance', fn () => Inertia::render('Admin/Avance/index'))->name('supervisor-avance');
    Route::get('/get-avance', [AvanceController::class, 'getAvance']);

    Route::get('/get-avance', [AvanceController::class, 'getAvance']);
    Route::get('/periodos', [SupervisorController::class, 'getPeriodos']);

    Route::post('/observar-documento', [SupervisorController::class, 'ObservarDocumento']);

    Route::post('/cambiar-estado-documento', [SupervisorController::class, 'cambiarEstado']);
    Route::post('/cambiar-periodo-documento', [SupervisorController::class, 'cambiarPeriodo']);

    Route::post('/eliminar-documento', [SupervisorController::class, 'eliminarDocumento']);


     //GET DATA

    Route::post('get-escuelas', [DataController::class, 'getEscuelas']);

    Route::post('get-programas', [DataController::class, 'getProgramas']);
    Route::post('get-competencias', [DataController::class, 'getCompetencias']);
    Route::get('/obtener-periodos', [SuperadmiController::class, 'obtenerListadoPeriodos']);



    Route::get('/notasperf-supervisor', [SupervisorController::class, 'getTestResultsvisor']);
    Route::get('/notas-todosest-supervisor', fn () => Inertia::render('Supervisor/notasper/index'))->name('notas-todosest-supervisor');

    // Ruta para la API (JSON) - sin cambios
    Route::get('/docentes-competencias-data', [SupervisorController::class, 'getDocentesCompetencias']);
    // Ruta para la vista (Inertia/Vue) - cambiamos la URL
    Route::get('/docentes-competencias', fn () => Inertia::render('Supervisor/docentesCompetencias/index'))->name('docentes-competencias');

    // Ruta para la API (JSON)
    Route::get('/busqueda-estudiantes', [SupervisorController::class, 'busquedaEstudiantes']);
    // Ruta para la vista (Inertia/Vue)
    Route::get('/estudiantes', fn () => Inertia::render('Supervisor/Estudiantes/index'))->name('busqueda-estudiantes');


    Route::get('/generar-reporte-documentos', [SupervisorController::class, 'generateReport'])->name('report.documents');


    // API JSON
    Route::get('/encargados-sistema/data', [SupervisorController::class, 'listarEncargadosSistema'])->name('encargados-sistema.data');
    // Vista Inertia/Vue
    Route::get('/encargados-sistema', fn () =>Inertia::render('Supervisor/Encargados/index'))->name('encargados-sistemas');


    //  // Vista del panel de ingresos (para supervisores)
    // Route::get('ingresos', fn () => Inertia::render('Supervisor/Ingresos/Index'))->name('ingresos');
    // // Registrar ingreso (escaneo de DNI)
    // Route::post('registrar-ingreso', [SorteoController::class, 'registrarIngreso']);
    // Route::get('get-eventos', [SorteoController::class, 'getEventos']);


    // Rutas de Laravel
Route::get('ingresos', fn () => Inertia::render('Supervisor/Ingresos/Index'))->name('ingresos'); // Vista del panel de ingresos
Route::post('registrar-ingreso', [SorteoController::class, 'registrarIngreso']); // Registrar ingreso con escaneo de DNI
Route::get('get-eventos', [SorteoController::class, 'getEventos']); // Obtener eventos disponibles
Route::get('get-estudiante/{dni}', [SorteoController::class, 'obtenerEstudianteEvento']); // Obtener datos del estudiante por DNI



 // Vista para el sorteo
Route::get('sorteo', fn () => Inertia::render('Supervisor/Sorteo/Index'))->name('sorteo');
// Obtener los participantes ingresados para el evento, con filtros
Route::get('participantes-evento/{evento_id}', [SorteoController::class, 'participantesIngresados']);
// Resumen de participantes ingresados por programa en un evento
Route::get('participantes-evento/{evento_id}/resumen', [SorteoController::class, 'participantesIngresadosPorPrograma']);

// Ejecutar sorteo y obtener los ganadores
Route::post('ejecutar-sorteo', [SorteoController::class, 'ejecutarSorteo']);
// Obtener los ganadores del sorteo
Route::get('ganadores-evento/{evento_id}', [SorteoController::class, 'listarGanadores']);
// Obtener los filtros disponibles
Route::get('filtros', [SorteoController::class, 'obtenerFiltros']);
Route::post('guardar-ganadores', [SorteoController::class, 'guardarGanadores']);



Route::get('exportar-ganadores-pdf/{evento_id}', [SorteoController::class, 'exportarGanadoresPDF']);
Route::get('exportar-ganadores-filtrado/{evento_id}', [SorteoController::class, 'exportarGanadoresFiltradoPDF']);
Route::get('lista-ganadores', fn () => Inertia::render('Supervisor/Sorteo/ListaGanadores'))->name('lista.ganadores');





        // Ruta para MOSTRAR la página (Renderiza la vista directamente desde aquí)
    Route::get('controles', fn () => Inertia::render('Supervisor/Controles/Index'))->name('controles');

    // Ruta para OBTENER los datos para la tabla (Vue llama a esta ruta)
    Route::post('get-controles', [BotonControlController::class, 'listarReglas']);

    // Ruta para ACTUALIZAR los datos (Vue llama a esta ruta para guardar)
    Route::post('update-controles', [BotonControlController::class, 'actualizarReglas']);




      // --- Panel de notificaciones ---
    Route::get('notificaciones', fn () => Inertia::render('Supervisor/Notificaciones/Index'))->name('notificaciones');

    // Listar notificaciones con filtros
    Route::post('listar-notificaciones', [UsuarioController::class, 'listarNotificaciones']);

    // Buscar usuarios para enviar notificación
    Route::post('buscar-usuarios-notificaciones', [UsuarioController::class, 'buscarUsuariosNotificaciones']);

    // Crear nueva notificación
    Route::post('guardar-notificacion', [UsuarioController::class, 'guardarNotificacion']);

    // Editar notificación existente
    Route::post('actualizar-notificacion/{id}', [UsuarioController::class, 'actualizarNotificacion']);

    // Eliminar notificación
    Route::post('eliminar-notificacion/{id}', [UsuarioController::class, 'eliminarNotificacion']);

    // Subir imagen de notificación
    Route::post('subir-imagen-notificacion', [UsuarioController::class, 'subirImagenNotificacion']);




});

   //superadmi
   Route::middleware(['auth', 'superadmi'])->prefix('superadmi')->group(function () {
//   Route::middleware('auth','superadmi')->group(function () {
//   Route::middleware('auth','superadmi')->prefix('superadmi')->group(function () {

        Route::get('/docentes-verdadero', fn () => Inertia::render('Superadmi/docentes/index'))->name('super-docente');

        Route::post('/get-competencia-x-docente', [DocenteController::class, 'getCompetenciasByDocente']);
        Route::get('/get-data-docente/{dni}', [DocenteController::class, 'getDataPrisma']);

        //TUTORES
        Route::get('tutores', [DocenteController::class, 'index'])->name('tutor-index');
        Route::post('save-docente', [DocenteController::class, 'save2']);
       // Route::post('get-docentes', [DocenteController::class, 'getDocentes']);
        Route::get('delete-docente/{id}', [DocenteController::class, 'delete']);

        //ASIGNACIÓN
       //sospe Route::get('asignacion', [AsignacionController::class, 'index'])->name('asignacion-index');
        Route::post('get-docente-competencia', [AsignacionController::class, 'getDocentesXcompetencia']);
        Route::post('save-curso', [AsignacionController::class, 'save']);
       // Route::post('get-cursos', [AsignacionController::class, 'getCursos']);
        Route::post('asignar-curso-nivelacion', [AsignacionController::class, 'asignarCursoNivelacion']);
      //  Route::post('get-detalle-curso', [AsignacionController::class, 'getDetalleCurso']);

        //GET DATA
        Route::post('get-programas', [DataController::class, 'getProgramas']);
      //  Route::post('get-roles', [DataController::class, 'getRoles']);
       // Route::post('get-competencias', [DataController::class, 'getCompetencias']);
      //  Route::post('get-escuelas', [DataController::class, 'getEscuelas']);
        //nwegpt
        Route::get('/generar-pdf/{id}', [AsignacionController::class, 'pdf']);
        Route::get('/delete-curso/{id}', [CursoController::class, 'delete']);
       // Route::post('get-programas-escuela', [DataController::class, 'getProgramasEscuela']);
        Route::post('get-alumnos-registro', [AlumnoController::class, 'getAlumnosRegistro']);
        // termiagpt
        Route::post('/get-docentes-superadmi', [DocenteController::class, 'getDocentesSuperAdmin']);




    Route::post('/get-usuario', [UsuarioController::class, 'getUsuarioSuperadmi']);
    Route::post('/restablecerContraseña', [SuperadmiController::class, 'restablecer']);
    Route::get('/', fn () => Inertia::render('Superadmi/index'))->name('superadmi-inicio');

    Route::post('getAlumnosc', [SuperadmiController::class, 'getAlumnos']);
    Route::get('alumnos', fn () => Inertia::render('Superadmi/estudiantes/alumnos'))->name('superadmi-estudiante');

    Route::post('getDocentes', [SuperadmiController::class, 'getDocentes']);
    Route::get('docentes', fn () => Inertia::render('Superadmi/docentes/docentes'))->name('superadmi-docentes');

    Route::post('/get-documentos', [SupervisorController::class, 'getDocumentos']);
    Route::get('/documentos-avance', fn () => Inertia::render('Superadmi/documentos/index'))->name('superadmi-avance');


    Route::post('/get-usuario',[UsuarioController::class,'getUsuarioSupervisor']);
    Route::get('/periodos',[SupervisorController::class,'getPeriodos']);
    Route::post('/observar-documento',[SupervisorController::class,'ObservarDocumento']);
    Route::post('/cambiar-estado-documento',[SupervisorController::class,'cambiarEstado']);
    Route::post('/cambiar-periodo-documento',[SupervisorController::class,'cambiarPeriodo']);






    Route::get('/get-avance', [AvanceController::class, 'getAvance']);
    Route::get('/avances', fn () => Inertia::render('Superadmi/Avance/index'))->name('superadmi-documento');

// desde aqui
    Route::post('getUsuarios', [SuperadmiController::class, 'getUsuarios']);
    Route::get('usuarios', fn () => Inertia::render('Superadmi/usuarios/usuarios'))->name('superadmi-usuarios');
    Route::post('/get-docentes', [DocenteController::class, 'getDocentes']);

    /// otros sacados de gtp
    Route::post('get-programas', [SuperadmiController::class, 'getProgramas']);
    Route::post('get-roles', [SuperadmiController::class, 'getRoles']);
    Route::post('get-escuelas', [SuperadmiController::class, 'getEscuelas']);
    Route::post('get-competencias', [SuperadmiController::class, 'getCompetencias']);

    // Rutas para el controlador AsignacionController
    Route::get('/asignacion', fn () => Inertia::render('Superadmi/Asignacion/index'))->name('asignacion-superadmi');
    //Route::get('asignacion', [SuperadmiController::class, 'index'])->name('asignacion-index');
    Route::post('get-cursos', [SuperadmiController::class, 'getCursos']);
    Route::post('get-detalle-curso', [SuperadmiController::class, 'getDetalleCurso']);
    // Route::post('asignar-curso-nivelacion', [SuperadmiController::class, 'asignarCursoNivelacion']);
    // Route::post('get-docente-competencia', [SuperadmiController::class, 'getDocentesXcompetencia']);
    // Route::post('save-curso', [SuperadmiController::class, 'save']);
// ------------------prueba de crud----------
    Route::get('get-pregunta', [SuperadmiController::class, 'getPregunta']);
    Route::get('/pregrentas', fn () => Inertia::render('Superadmi/Pregunta/pregunta'))->name('pregun');
    Route::post('guardarp', [SuperadmiController::class, 'savep']);
    Route::get('eliminarp/{id}', [SuperadmiController::class, 'eliminarp']);

// --------------------------------------------notas perfil
    Route::get('/notasperf', [SuperadmiController::class, 'getTestResults']);
    Route::get('/notas-todosest', fn () => Inertia::render('Superadmi/notasperfiles/notasperfil'))->name('notas-todosest');

    // -------------------info estidiantes----------------
    Route::get('/get-estudiantes', [SuperadmiController::class, 'getEstudiantes']);
    Route::get('/estudiantesinfo', fn () => Inertia::render('Superadmi/Informacion/informacion'))->name('info_est');

    // ----------periodo ------------------------------
    Route::get('get-periodos', [SuperadmiController::class, 'getPeriodos']);
    Route::get('/periodos', fn () => Inertia::render('Superadmi/Periodos/Index'))->name('periodos');
    Route::post('guardar-periodo', [SuperadmiController::class, 'savePeriodo']);
    Route::get('eliminar-periodo/{id}', [SuperadmiController::class, 'eliminarPeriodo']);
    // ------------------- actulizar notas perido -------------------
    Route::get('/notas/actualizar', fn () => Inertia::render('Superadmi/Notas/Actualizar'))
        ->name('superadmi.notas.actualizar');

    Route::post('/notas/preview', [SuperadmiController::class, 'previewUpdate'])
        ->name('superadmi.notas.preview');

    Route::post('/notas/execute', [SuperadmiController::class, 'executeUpdate'])
        ->name('superadmi.notas.execute');
    Route::get('/obtener-escuelas', [SuperadmiController::class, 'obtenerListadoEscuelas']);
    Route::get('/obtener-periodos', [SuperadmiController::class, 'obtenerListadoPeriodos']);

        Route::get('/alumnos-importar', fn () => Inertia::render('Superadmi/Alumnos/index'))->name('alumnos-importar');
   // Route::post('importar-excel-estudiante', [AlumnoController::class, 'excelEstudiante']);


     Route::post('subir-estudiantes', [SuperadmiController::class, 'importarEstudiante']); //->name('alumnos-importar');

     // crear evento:
    Route::get('/eventos', fn () => Inertia::render('Superadmi/Eventos/Index'))->name('eventos');
    Route::get('get-eventos', [SorteoController::class, 'getEventos']);
    Route::post('guardar-evento', [SorteoController::class, 'guardarEvento']);
    Route::get('eliminar-evento/{id}', [SorteoController::class, 'eliminarEvento']);


        // API JSON
    Route::get('/encargados-sistema/data', [SupervisorController::class, 'listarEncargadosSistema'])->name('encargados-sistema.data');
    // Vista Inertia/Vue
    Route::get('/encargados-sistema', fn () =>Inertia::render('Superadmi/Encargados/index'))->name('encargados-sistema');



            // Ruta para MOSTRAR la página (Renderiza la vista directamente desde aquí)
    Route::get('controles', fn () => Inertia::render('Superadmi/Controles/Index'))->name('controlesadmin');

    // Ruta para OBTENER los datos para la tabla (Vue llama a esta ruta)
    Route::post('get-controles', [BotonControlController::class, 'listarReglas']);

    // Ruta para ACTUALIZAR los datos (Vue llama a esta ruta para guardar)
    Route::post('update-controles', [BotonControlController::class, 'actualizarReglas']);


    // Ruta para la API (JSON) - sin cambios
    Route::get('/docentes-competencias-data', [SupervisorController::class, 'getDocentesCompetencias']);
    // Ruta para la vista (Inertia/Vue) - cambiamos la URL
    Route::get('/docentes-competencias', fn () => Inertia::render('Superadmi/docentesCompetencias/index'))->name('docentes-competencia');


    // Ruta para la API (JSON)
    Route::get('/busqueda-estudiantes', [SupervisorController::class, 'busquedaEstudiantes']);
    // Ruta para la vista (Inertia/Vue)
    Route::get('/estudiantes', fn () => Inertia::render('Superadmi/Estudiante/index'))->name('busqueda-estudiante');



         // --- Panel de notificaciones ---
    Route::get('notificaciones', fn () => Inertia::render('Superadmi/Notificaciones/Index'))->name('notificaciones.admin');
    // Listar notificaciones con filtros
    Route::post('listar-notificaciones', [UsuarioController::class, 'listarNotificaciones']);
    // Buscar usuarios para enviar notificación
    Route::post('buscar-usuarios-notificaciones', [UsuarioController::class, 'buscarUsuariosNotificaciones']);
    // Crear nueva notificación
    Route::post('guardar-notificacion', [UsuarioController::class, 'guardarNotificacion']);
    // Editar notificación existente
    Route::post('actualizar-notificacion/{id}', [UsuarioController::class, 'actualizarNotificacion']);
    // Eliminar notificación
    Route::post('eliminar-notificacion/{id}', [UsuarioController::class, 'eliminarNotificacion']);
    // Subir imagen de notificación
    Route::post('subir-imagen-notificacion', [UsuarioController::class, 'subirImagenNotificacion']);




});

Route::get('/dni/{dni}', [SuperadmiController::class, 'show'])
    ->whereNumber('dni');

require __DIR__.'/auth.php';


