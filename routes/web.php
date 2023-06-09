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
use App\Http\Controllers\AvanceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use DB;
use App\Http\Controllers\Docente\DashboardController;
use Inertia\Inertia;



Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified','admin',])->name('dashboard');

Route::middleware('auth','admin')->group(function () {
    Route::get('/', fn () => Inertia::render('Inicio/index'))->name('inicio');
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
    Route::get('/notas-perfiles', fn () => Inertia::render('Admin/Matriz/index'));


    //COORDINADOR
    Route::prefix('coordinador')->group(function () {
        Route::get('/', fn () => Inertia::render('Admin/Coordinador/index'))->name('coordinador');    
        Route::post('/save-coordinador', [CoordinadorController::class, 'save']); #-->
        Route::post('/get-coordinadores', [CoordinadorController::class, 'getCoordinadores']);
        Route::get('/delete-coordinador/{id}', [CoordinadorController::class, 'delete']);
        Route::post('/get-escuelas', [CoordinadorController::class, 'getEscuelas']); #-->

        Route::get('/estudiante', fn () => Inertia::render('Admin/Estudiante/index'))->name('coordinador-estudiante');    
        Route::post('/get-alumnos', [CoordinadorController::class, 'getAlumnos']);

        Route::get('/docente', fn () => Inertia::render('Admin/Docente/index'))->name('coordinador-docente');    
        Route::post('/get-docentes', [DocenteController::class, 'getDocentes']);
        Route::post('/save-docente', [DocenteController::class, 'save']);
        Route::post('/get-competencia-x-docente', [DocenteController::class, 'getCompetenciasByDocente']);
        Route::get('/delete-docente/{id}', [DocenteController::class, 'delete']);

        Route::get('/asignacion', fn () => Inertia::render('Admin/Asignacion/index'))->name('coordinador-asignacion');
        Route::post('/get-docente-competencia', [AsignacionController::class, 'getDocentesXcompetencia']);
        Route::post('/save-curso', [AsignacionController::class, 'save']);
        Route::post('/get-cursos', [AsignacionController::class, 'getCursos']);
        Route::post('/asignar-curso-nivelacion', [AsignacionController::class, 'asignarCursoNivelacion']);
        Route::post('/get-detalle-curso', [AsignacionController::class, 'getDetalleCurso']);

        Route::post('/get-competencias', [CoordinadorController::class, 'compes']);
    });


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
    
    //DOCUMENTOS
    Route::post('documento/resolucion', [DocumentoController::class, 'resolucion']);
    Route::post('documento/plan', [DocumentoController::class, 'plan']);
    Route::post('documento/informe', [DocumentoController::class, 'informe']);
    Route::post('get-resoluciones', [DocumentoController::class, 'getResoluciones']);
    Route::post('get-planes', [DocumentoController::class, 'getPlanes']);
    Route::post('get-informes', [DocumentoController::class, 'getInformes']);

});


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
});


Route::get('/avance', fn () => Inertia::render('Admin/Avance/index'));
Route::get('/get-avance', [AvanceController::class, 'getAvance']);


require __DIR__.'/auth.php';
