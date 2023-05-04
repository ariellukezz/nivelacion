<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\AsignacionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
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

    //ALUMNO
    Route::get('alumnos', [AlumnoController::class, 'index'])->name('alumno-index');
    Route::post('get-alumnos', [AlumnoController::class, 'getAlumnos']);
    
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

    //GET DATA
    Route::post('get-programas', [DataController::class, 'getProgramas']);
    Route::post('get-roles', [DataController::class, 'getRoles']);
    Route::post('get-competencias', [DataController::class, 'getCompetencias']);
    Route::post('get-escuelas', [DataController::class, 'getEscuelas']);
    
});

require __DIR__.'/auth.php';
