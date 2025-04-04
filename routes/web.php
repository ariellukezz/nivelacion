<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\CoordinadorController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\TeController;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\AvanceController;
use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\SuperadmiController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Docente\DashboardController;
use Inertia\Inertia;

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
});

Route::post('/save-contrasenia', [UsuarioController::class, 'saveNewContra'])->middleware('auth');

   //supervisor


Route::middleware('auth','supervisor')->prefix('supervisor')->group(function () {
    Route::get('/', fn () => Inertia::render('Supervisor/index'))->name('supervisor-inicio');
    Route::get('/asignacion', fn () => Inertia::render('Supervisor/Asignacion/asignacion'))->name('supervisor-asignacion');
    Route::post('/get-usuario', [UsuarioController::class, 'getUsuarioSupervisor']);
    Route::post('/get-documentos', [SupervisorController::class, 'getDocumentos']);
    Route::get('/avance', fn () => Inertia::render('Admin/Avance/index'))->name('supervisor-avance');
    Route::get('/get-avance', [AvanceController::class, 'getAvance']);

    Route::get('/get-avance', [AvanceController::class, 'getAvance']);

    Route::post('/observar-documento', [SupervisorController::class, 'ObservarDocumento']);

    Route::post('/cambiar-estado-documento', [SupervisorController::class, 'cambiarEstado']);
    Route::post('/cambiar-periodo-documento', [SupervisorController::class, 'cambiarPeriodo']);


    Route::get('/notasperf-supervisor', [SupervisorController::class, 'getTestResultsvisor']);
Route::get('/notas-todosest-supervisor', fn () => Inertia::render('Supervisor/notasper/index'))->name('notas-todosest-supervisor');


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

    Route::get('/get-avance', [AvanceController::class, 'getAvance']);
    Route::get('/avances', fn () => Inertia::render('Superadmi/Avance/index'))->name('superadmi-documento');


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



});


require __DIR__.'/auth.php';


