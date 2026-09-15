<?php

// Sistema de Nivelación de Ingresantes - rutas web.
// Organizado por roles y módulos; no se elimina ninguna ruta.
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
use App\Http\Controllers\FichaRiesgoAcademicoController;
use App\Http\Controllers\FichaRiesgoController;
use App\Http\Controllers\NotaCoordinadorController;
use App\Http\Controllers\AdmisionIntegracionController;
use App\Http\Controllers\DataIngresoIntegracionController;
use App\Http\Controllers\PermisoAsignacionController;
use App\Http\Controllers\EstudianteEstadoController;
use App\Http\Controllers\DireccionAsignacionController;

// Acceso general y dashboard.
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified','admin',])->name('dashboard');

// Login público.
Route::middleware('red')->get('/', fn () => Inertia::render('Auth/Login'));

// ROL 1: Director / administración académica.
Route::middleware('auth','admin')->group(function () {

    // Inicio y páginas generales.
    Route::get('/inicio', fn () => Inertia::render('Inicio/index'))->name('inicio');
    Route::get('/about', fn () => Inertia::render('About'))->name('about');

    // Usuarios y perfil.
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuario-index');
    Route::post('get-usuarios', [UsuarioController::class, 'getUsuarios']);
    Route::post('save-usuario', [UsuarioController::class, 'save']);
    Route::get('delete-usuario/{id}', [UsuarioController::class, 'delete']);
    Route::post('/get-usuario', [UsuarioController::class, 'getUsuarioAdministrador']);

    // Datos de nivelación y perfiles de ingreso.
    Route::get('/prueba2', [TeController::class, 'getTest']);
    Route::get('/notas-perfiles', fn () => Inertia::render('Admin/Matriz/index'))->name('notas-perfiles');
    // Route::get('/ingresantes', [TeController::class, 'getIngresantes']);
    Route::get('/ingresantes', [DataIngresoIntegracionController::class, 'getIngresantes']);
    Route::get('/data-ingreso', fn () => Inertia::render('Admin/Dataingreso/index'))->name('data-ingreso');
    Route::get('/reprobados-nivelacion/data', [TeController::class, 'getReprobadosNivelacion'])->name('reprobados-nivelacion.data');
    Route::get('/reprobados-nivelacion', fn () =>Inertia::render('Admin/Reprobados/index'))->name('reprobados-nivelacion');

    // Descarga de archivos.
    Route::get('/descargar-archivo/{nombreArchivo}', [ArchivoController::class, 'descargarArchivo']);

    // Módulo: coordinadores.
    Route::prefix('coordinador')->group(function () {
        Route::get('/', fn () => Inertia::render('Admin/Coordinador/index'))->name('coordinador');
        Route::post('/save-coordinador', [CoordinadorController::class, 'save']); #-->
        Route::post('/get-coordinadores', [CoordinadorController::class, 'getCoordinadores']);
        Route::get('/delete-coordinador/{id}', [CoordinadorController::class, 'delete']);
        // Route::post('/get-escuelas', [CoordinadorController::class, 'getEscuelas']);

        // Módulo: estudiantes.
        Route::get('/estudiante', fn () => Inertia::render('Admin/Estudiante/index'))->name('coordinador-estudiante');
        Route::post('/get-alumnos', [CoordinadorController::class, 'getAlumnos']);
        Route::patch('/estudiante/{id}/estado-nivelacion', [EstudianteEstadoController::class, 'actualizarDirector']);

        // Módulo: docentes.
        Route::get('/docente', fn () => Inertia::render('Admin/Docente/index'))->name('coordinador-docente');
        Route::post('/get-docentes', [DocenteController::class, 'getDocentes']);
        Route::post('/save-docente', [DocenteController::class, 'save']);
        Route::post('/get-competencia-x-docente', [DocenteController::class, 'getCompetenciasByDocente']);
        Route::get('/delete-docente/{id}', [DocenteController::class, 'delete']);
        Route::get('/get-data-docente/{dni}', [DocenteController::class, 'getDataPrisma']);

        // Módulo: asignación docente.
        Route::get('/asignacion', fn () => Inertia::render('Admin/Asignacion/index'))->name('coordinador-asignacion');
        Route::get('/asignacion-permisos', [PermisoAsignacionController::class, 'director']);
        Route::post('/get-docente-competencia', [AsignacionController::class, 'getDocentesXcompetencia']);
        Route::post('/save-curso', [AsignacionController::class, 'save']);
        Route::post('/get-cursos', [AsignacionController::class, 'getCursos']);
        Route::post('/asignar-curso-nivelacion', [AsignacionController::class, 'asignarCursoNivelacion']);
        Route::post('/get-detalle-curso', [AsignacionController::class, 'getDetalleCurso']);
        Route::post('/get-competencias', [CoordinadorController::class, 'compes']);
        Route::get('delete-curso/{id}', [CursoController::class, 'delete']);
        Route::get('/generar-pdf/{id}', [AsignacionController::class, 'pdf']);

        // Módulo: notas.
        Route::get('/notas', function () {
        return Inertia::render('Admin/NotasCoordinador/index');
        })->name('coordinador.notas');
        Route::post('/notas/get-competencias', [NotaCoordinadorController::class, 'getCompetencias']);
        Route::post('/notas/get-cursos', [NotaCoordinadorController::class, 'getCursos']);
        Route::post('/notas/get-alumnos', [NotaCoordinadorController::class, 'getAlumnos']);
        Route::post('/notas/update-nota', [NotaCoordinadorController::class, 'updateNota']);
    });

    // Módulo: importación de estudiantes.
    Route::get('/alumnos-importar', fn () => Inertia::render('Admin/Alumnos/index'))->name('alumnos-importar');
    Route::post('importar-excel-estudiante', [AlumnoController::class, 'excelEstudiante']);

    // Módulo heredado: alumnos.
    Route::get('alumnos', [AlumnoController::class, 'index'])->name('alumno-index');
    Route::post('get-alumnos', [AlumnoController::class, 'getAlumnos']);
    Route::post('get-alumnos-registro', [AlumnoController::class, 'getAlumnosRegistro']);

    // Módulo heredado: tutores/docentes.
    Route::get('tutores', [DocenteController::class, 'index'])->name('tutor-index');
    Route::post('save-docente', [DocenteController::class, 'save']);
    Route::post('get-docentes', [DocenteController::class, 'getDocentes']);
    Route::get('delete-docente/{id}', [DocenteController::class, 'delete']);

    // Módulo heredado: asignación.
    Route::get('asignacion', [AsignacionController::class, 'index'])->name('asignacion-index');
    Route::post('get-docente-competencia', [AsignacionController::class, 'getDocentesXcompetencia']);
    Route::post('save-curso', [AsignacionController::class, 'save']);
    Route::post('get-cursos', [AsignacionController::class, 'getCursos']);
    Route::post('asignar-curso-nivelacion', [AsignacionController::class, 'asignarCursoNivelacion']);
    Route::post('get-detalle-curso', [AsignacionController::class, 'getDetalleCurso']);

    // Datos comunes: programas, roles, competencias y escuelas.
    Route::post('get-programas', [DataController::class, 'getProgramas']);
    Route::post('get-roles', [DataController::class, 'getRoles']);
    Route::post('get-competencias', [DataController::class, 'getCompetencias']);
    Route::post('get-escuelas', [DataController::class, 'getEscuelas']);
    Route::post('get-programas-escuela', [DataController::class, 'getProgramasEscuela']);
    Route::post('get-mis-programas', [DataController::class, 'getMisProgramas']);

    // Módulo: documentos.
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

    // Control de disponibilidad de módulos.
    Route::get('/estado-boton/{modulo}', [BotonControlController::class, 'estadoBoton']);

    // Módulo: encargados.
    Route::get('encargados', fn () => Inertia::render('Admin/Encargado/index'))->name('encargado-index');
    Route::post('get-encargados', [PreguntaController::class, 'getEncargados']);
    Route::post('save-encargado', [PreguntaController::class, 'saveEncargado']);
    Route::delete('delete-encargado/{id}', [PreguntaController::class, 'deleteEncargado']);
});

// Histórico Superadmin comentado; se conserva.
// Route::middleware('auth','superadmi')->group(function () {
//     Route::get('/docentes-verdadero', fn () => Inertia::render('Superadmi/docentes/index'))->name('super-docente');
//     Route::post('/get-competencia-x-docente', [DocenteController::class, 'getCompetenciasByDocente']);
//     Route::get('/get-data-docente/{dni}', [DocenteController::class, 'getDataPrisma']);
//     Route::get('tutores', [DocenteController::class, 'index'])->name('tutor-index');
//     Route::post('save-docente', [DocenteController::class, 'save2']);
//     Route::post('get-docentes', [DocenteController::class, 'getDocentes']);
//     Route::get('delete-docente/{id}', [DocenteController::class, 'delete']);
//     Route::get('asignacion', [AsignacionController::class, 'index'])->name('asignacion-index');
//     Route::post('get-docente-competencia', [AsignacionController::class, 'getDocentesXcompetencia']);
//     Route::post('save-curso', [AsignacionController::class, 'save']);
//     Route::post('get-cursos', [AsignacionController::class, 'getCursos']);
//     Route::post('asignar-curso-nivelacion', [AsignacionController::class, 'asignarCursoNivelacion']);
//     Route::post('get-detalle-curso', [AsignacionController::class, 'getDetalleCurso']);
//     Route::post('get-programas', [DataController::class, 'getProgramas']);
//     Route::post('get-roles', [DataController::class, 'getRoles']);
//     Route::post('get-competencias', [DataController::class, 'getCompetencias']);
//     Route::post('get-escuelas', [DataController::class, 'getEscuelas']);

// ROL 4: Docente.
Route::middleware('auth','docente')->group(function () {
//    Route::get('docente', [DocenteController::class, 'dashboardDocente'])->name('docente-inicio');

    // Inicio, cursos, estudiantes, notas y PDF.
    Route::get('/docente', fn () => Inertia::render('Docente/Inicio/inicio'))->name('docente-inicio');
    // Duplicada: se conserva para revisión.
    Route::get('/docente', fn () => Inertia::render('Docente/Inicio/inicio'))->name('docente-inicio');
    Route::get('docente/curso', [CursoController::class, 'cursoDocente'])->name('docente-curso');
    Route::post('docente/get-cursos', [CursoController::class, 'getCursos']);
    Route::post('docente/get-alumnos-curso', [CursoController::class, 'getAlumnosXCurso']);
    Route::post('docente/update-nota', [CursoController::class, 'updateNota']);
    Route::post('/docente/get-usuario', [UsuarioController::class, 'getUsuarioDocente']);
    Route::get('/docente/generar-pdf/{id}', [DocenteController::class, 'pdf']);

    // Módulo: encuestas.
    Route::get('docente/encuestas', fn () => Inertia::render('Docente/Encuestas/index'))->name('docente-encuestas');
    Route::get('docente/cursos-encuesta', [CursoController::class, 'getCursosEncuestaD']);
    Route::get('docente/get-preguntas/{encuesta}', [PreguntaController::class, 'getPreguntas']);
    Route::post('docente/save-encuesta-docente', [PreguntaController::class, 'saveRepuestasDocente']);
});

// ROL 5: Estudiante.
Route::middleware('auth','estudiante')->prefix('estudiante')->group(function () {

    // Usuario, inicio, notas y encuestas.
    Route::post('/get-usuario', [UsuarioController::class, 'getUsuarioEstudiante']);
    //    Route::get('docente', [DocenteController::class, 'dashboardDocente'])->name('docente-inicio');
    Route::get('/inicio', fn () => Inertia::render('Estudiante/Inicio/index'))->name('estudiante-inicio');
    Route::get('/notas', fn () => Inertia::render('Estudiante/Notas/index'))->name('estudiante-notas');
    Route::get('/encuestas', fn () => Inertia::render('Estudiante/Encuestas/index'))->name('estudiante-encuestas');
    Route::post('/get-notas', [CursoController::class, 'getNotasByAlumno']);
    Route::get('/cursos-encuesta', [CursoController::class, 'getCursosEncuesta']);
    Route::get('/get-preguntas/{encuesta}', [PreguntaController::class, 'getPreguntas']);
    Route::post('/save-encuesta-estudiante', [PreguntaController::class, 'saveRepuestasPostulante']);

    // Módulo: eventos e inducción.
    Route::get('/evento', fn () => Inertia::render('Estudiante/Evento/index'))->name('estudiante-evento');
    Route::post('/get-evento-induccion', [CursoController::class, 'getEventoInduccionByAlumno']);

    // Módulo: historial de notas.
    Route::get('historial-notas', function () {
        return Inertia::render('Estudiante/HistorialNotas/index');
    })->name('estudiante.historial-notas');
    Route::get('/get-historial-notas', [CursoController::class, 'getHistorialNotas'])
         ->withoutMiddleware('estudiante')
         ->name('estudiante.get-historial-notas');
});

// Común a todos los roles: contraseña, perfil y notificaciones.
Route::post('/save-contrasenia', [UsuarioController::class, 'saveNewContra'])->middleware('auth');
Route::get('/mi-perfil', [UsuarioController::class, 'miPerfil'])
    ->middleware('auth');
Route::post('/get-noti', [UsuarioController::class, 'getNoti'])->middleware('auth');
Route::post('/read-noti/{id}', [UsuarioController::class, 'readNoti'])->middleware('auth');

// ROL 6: Supervisor.
Route::middleware('auth','supervisor')->prefix('supervisor')->group(function () {

    // Inicio, documentos, avance y periodos.
    Route::get('/', fn () => Inertia::render('Supervisor/index'))->name('supervisor-inicio');
    Route::get('/asignacion', fn () => Inertia::render('Supervisor/Asignacion/asignacion'))->name('supervisor-asignacion');
    Route::post('/get-usuario', [UsuarioController::class, 'getUsuarioSupervisor']);
    Route::post('/get-documentos', [SupervisorController::class, 'getDocumentos']);
    Route::get('/avance', fn () => Inertia::render('Admin/Avance/index'))->name('supervisor-avance');
    Route::get('/get-avance', [AvanceController::class, 'getAvance']);
    // Duplicada: se conserva para revisión.
    Route::get('/get-avance', [AvanceController::class, 'getAvance']);
    Route::get('/periodos', [SupervisorController::class, 'getPeriodos']);
    Route::post('/observar-documento', [SupervisorController::class, 'ObservarDocumento']);
    Route::post('/cambiar-estado-documento', [SupervisorController::class, 'cambiarEstado']);
    Route::post('/cambiar-periodo-documento', [SupervisorController::class, 'cambiarPeriodo']);
    Route::post('/eliminar-documento', [SupervisorController::class, 'eliminarDocumento']);

    // Datos comunes: escuelas, programas, competencias y periodos.
    Route::post('get-escuelas', [DataController::class, 'getEscuelas']);
    Route::post('get-programas', [DataController::class, 'getProgramas']);
    Route::post('get-competencias', [DataController::class, 'getCompetencias']);
    Route::get('/obtener-periodos', [SuperadmiController::class, 'obtenerListadoPeriodos']);

    // Módulo: notas y perfiles.
    Route::get('/notasperf-supervisor', [SupervisorController::class, 'getTestResultsvisor']);
    Route::get('/notas-todosest-supervisor', fn () => Inertia::render('Supervisor/notasper/index'))->name('notas-todosest-supervisor');

    // Módulo: docentes por competencia.
    Route::get('/docentes-competencias-data', [SupervisorController::class, 'getDocentesCompetencias']);
    Route::get('/docentes-competencias', fn () => Inertia::render('Supervisor/docentesCompetencias/index'))->name('docentes-competencias');

    // Módulo: búsqueda de estudiantes.
    Route::get('/busqueda-estudiantes', [SupervisorController::class, 'busquedaEstudiantes']);
    Route::get('/estudiantes', fn () => Inertia::render('Supervisor/Estudiantes/index'))->name('busqueda-estudiantes');

    // Módulo: reporte de documentos.
    Route::get('/generar-reporte-documentos', [SupervisorController::class, 'generateReport'])->name('report.documents');

    // Módulo: encargados del sistema.
    Route::get('/encargados-sistema/data', [SupervisorController::class, 'listarEncargadosSistema'])->name('encargados-sistema.data');
    Route::get('/encargados-sistema', fn () =>Inertia::render('Supervisor/Encargados/index'))->name('encargados-sistemas');
    // Route::get('ingresos', fn () => Inertia::render('Supervisor/Ingresos/Index'))->name('ingresos');
    // Route::post('registrar-ingreso', [SorteoController::class, 'registrarIngreso']);
    // Route::get('get-eventos', [SorteoController::class, 'getEventos']);

    // Módulo: ingreso y asistencia a eventos.
Route::get('ingresos', fn () => Inertia::render('Supervisor/Ingresos/Index'))->name('ingresos'); // Vista del panel de ingresos
Route::post('registrar-ingreso', [SorteoController::class, 'registrarIngreso']); // Registrar ingreso con escaneo de DNI
Route::get('get-eventos', [SorteoController::class, 'getEventos']); // Obtener eventos disponibles
Route::get('get-estudiante/{dni}', [SorteoController::class, 'obtenerEstudianteEvento']); // Obtener datos del estudiante por DNI

    // Módulo: sorteo y ganadores.
Route::get('sorteo', fn () => Inertia::render('Supervisor/Sorteo/Index'))->name('sorteo');
Route::get('participantes-evento/{evento_id}', [SorteoController::class, 'participantesIngresados']);
Route::get('participantes-evento/{evento_id}/resumen', [SorteoController::class, 'participantesIngresadosPorPrograma']);
Route::post('ejecutar-sorteo', [SorteoController::class, 'ejecutarSorteo']);
Route::get('ganadores-evento/{evento_id}', [SorteoController::class, 'listarGanadores']);
Route::get('filtros', [SorteoController::class, 'obtenerFiltros']);
Route::post('guardar-ganadores', [SorteoController::class, 'guardarGanadores']);
Route::get('exportar-ganadores-pdf/{evento_id}', [SorteoController::class, 'exportarGanadoresPDF']);
Route::get('exportar-ganadores-filtrado/{evento_id}', [SorteoController::class, 'exportarGanadoresFiltradoPDF']);
Route::get('lista-ganadores', fn () => Inertia::render('Supervisor/Sorteo/ListaGanadores'))->name('lista.ganadores');

    // Módulo: controles del sistema.
    Route::get('controles', fn () => Inertia::render('Supervisor/Controles/Index'))->name('controles');
    Route::post('get-controles', [BotonControlController::class, 'listarReglas']);
    Route::post('update-controles', [BotonControlController::class, 'actualizarReglas']);

    // Módulo: notificaciones.
    Route::get('notificaciones', fn () => Inertia::render('Supervisor/Notificaciones/Index'))->name('notificaciones');
    Route::post('listar-notificaciones', [UsuarioController::class, 'listarNotificaciones']);
    Route::post('buscar-usuarios-notificaciones', [UsuarioController::class, 'buscarUsuariosNotificaciones']);
    Route::post('guardar-notificacion', [UsuarioController::class, 'guardarNotificacion']);
    Route::post('actualizar-notificacion/{id}', [UsuarioController::class, 'actualizarNotificacion']);
    Route::post('eliminar-notificacion/{id}', [UsuarioController::class, 'eliminarNotificacion']);
    Route::post('subir-imagen-notificacion', [UsuarioController::class, 'subirImagenNotificacion']);

    // Datos auxiliares: programas con filial.
    Route::get('/get-programas-filial', [SupervisorController::class, 'getProgramasConFilial']);

    // Módulo: monitoreo de docentes.
Route::get('/dashboard-docentes', [SupervisorController::class, 'getDashboardDocentes']);
Route::get('/competencias-sin-docente', [SupervisorController::class, 'getCompetenciasSinDocente']);
Route::get('/docentes-faltan-notas', [SupervisorController::class, 'getDocentesFaltanNotas']);
Route::get('/monitoreo-docentes', fn () => Inertia::render('Supervisor/monitoreoDocentes/index'))->name('monitoreo-docentes');
Route::get('/monitoreo-docentes/competencias-sin-docente', fn () => Inertia::render('Supervisor/monitoreoDocentes/CompetenciasSinDocente'))->name('competencias-sin-docente-vista');
Route::get('/monitoreo-docentes/docentes-faltan-notas', fn () => Inertia::render('Supervisor/monitoreoDocentes/DocentesFaltanNotas'))->name('docentes-faltan-notas-vista');

    // Módulo: reporte de matriculados.
Route::get('/reporte-matriculados-vista', fn () => Inertia::render('Supervisor/reporteMatriculados/index'))->name('reporte-matriculados');
Route::get('/reporte-matriculados', [SupervisorController::class, 'getReporteMatriculados']);

    // Módulo: fichas de riesgo académico. Vista, datos, impresión, PDF, edición, eliminación y Excel.
Route::get('/fichas-riesgo', fn () => Inertia::render('Supervisor/FichaRiesgo/index'))->name('supervisor.fichas-riesgo');
Route::get('/fichas-riesgo-data', [FichaRiesgoAcademicoController::class, 'reporteSupervisor']);
Route::get('/fichas-riesgo/{id}/imprimir', [FichaRiesgoAcademicoController::class, 'imprimirSupervisor']);
Route::get('/fichas-riesgo/{id}/pdf', [FichaRiesgoAcademicoController::class, 'pdfSupervisor']);
Route::get('/fichas-riesgo/{id}', [FichaRiesgoAcademicoController::class, 'verFichaSupervisor']);
Route::put('/fichas-riesgo/{id}', [FichaRiesgoAcademicoController::class, 'actualizarSupervisor']);
Route::delete('/fichas-riesgo/{id}', [FichaRiesgoAcademicoController::class, 'eliminarSupervisor']);
Route::get('/fichas-riesgo-excel', [FichaRiesgoAcademicoController::class, 'exportarDataSupervisor']);
    });

// ROL 0: Superadministrador.
   Route::middleware(['auth', 'superadmi'])->prefix('superadmi')->group(function () {
//   Route::middleware('auth','superadmi')->group(function () {
//   Route::middleware('auth','superadmi')->prefix('superadmi')->group(function () {

    // Módulo: gestión principal de docentes.
        Route::get('/docentes-verdadero', fn () => Inertia::render('Superadmi/docentes/index'))->name('super-docente');
        Route::post('/get-competencia-x-docente', [DocenteController::class, 'getCompetenciasByDocente']);
        Route::get('/get-data-docente/{dni}', [DocenteController::class, 'getDataPrisma']);
        Route::get('tutores', [DocenteController::class, 'index'])->name('tutor-index');
        Route::post('save-docente', [DocenteController::class, 'save2']);
       // Route::post('get-docentes', [DocenteController::class, 'getDocentes']);
        Route::get('delete-docente/{id}', [DocenteController::class, 'delete']);

    // Asignación asociada a docentes.
       //sospe Route::get('asignacion', [AsignacionController::class, 'index'])->name('asignacion-index');
        Route::post('get-docente-competencia', [AsignacionController::class, 'getDocentesXcompetencia']);
        Route::post('save-curso', [AsignacionController::class, 'save']);
       // Route::post('get-cursos', [AsignacionController::class, 'getCursos']);
        Route::post('asignar-curso-nivelacion', [AsignacionController::class, 'asignarCursoNivelacion']);
      //  Route::post('get-detalle-curso', [AsignacionController::class, 'getDetalleCurso']);

    // Datos auxiliares para docentes y asignación.
        Route::post('get-programas', [DataController::class, 'getProgramas']);
      //  Route::post('get-roles', [DataController::class, 'getRoles']);
       // Route::post('get-competencias', [DataController::class, 'getCompetencias']);
      //  Route::post('get-escuelas', [DataController::class, 'getEscuelas']);
        Route::get('/generar-pdf/{id}', [AsignacionController::class, 'pdf']);
        Route::get('/delete-curso/{id}', [CursoController::class, 'delete']);
        Route::post('get-programas-escuela', [DataController::class, 'getProgramasEscuela']);
        Route::post('get-alumnos-registro', [AlumnoController::class, 'getAlumnosRegistro']);
        Route::post('/get-docentes-superadmi', [DocenteController::class, 'getDocentesSuperAdmin']);

    // Inicio, usuario y restablecimiento de contraseña.
    Route::post('/get-usuario', [UsuarioController::class, 'getUsuarioSuperadmi']);
    Route::post('/restablecerContraseña', [SuperadmiController::class, 'restablecer']);
    Route::get('/', fn () => Inertia::render('Superadmi/index'))->name('superadmi-inicio');

    // Módulo: estudiantes.
    Route::post('getAlumnosc', [SuperadmiController::class, 'getAlumnos']);
    Route::get('alumnos', fn () => Inertia::render('Superadmi/estudiantes/alumnos'))->name('superadmi-estudiante');

    // Módulo: consulta general de docentes.
    Route::post('getDocentes', [SuperadmiController::class, 'getDocentes']);
    Route::get('docentes', fn () => Inertia::render('Superadmi/docentes/docentes'))->name('superadmi-docentes');

    // Módulo: documentos y revisión.
    Route::post('/get-documentos', [SupervisorController::class, 'getDocumentos']);
    Route::get('/documentos-avance', fn () => Inertia::render('Superadmi/documentos/index'))->name('superadmi-avance');
    Route::post('/get-usuario',[UsuarioController::class,'getUsuarioSupervisor']);
    Route::get('/periodos',[SupervisorController::class,'getPeriodos']);
    Route::post('/observar-documento',[SupervisorController::class,'ObservarDocumento']);
    Route::post('/cambiar-estado-documento',[SupervisorController::class,'cambiarEstado']);
    Route::post('/cambiar-periodo-documento',[SupervisorController::class,'cambiarPeriodo']);

    // Módulo: avance.
    Route::get('/get-avance', [AvanceController::class, 'getAvance']);
    Route::get('/avances', fn () => Inertia::render('Superadmi/Avance/index'))->name('superadmi-documento');

    // Módulo: usuarios.
    Route::post('getUsuarios', [SuperadmiController::class, 'getUsuarios']);
    Route::get('usuarios', fn () => Inertia::render('Superadmi/usuarios/usuarios'))->name('superadmi-usuarios');
    Route::post('/get-docentes', [DocenteController::class, 'getDocentes']);

    // Datos comunes: roles, escuelas y competencias.
    Route::post('get-roles', [SuperadmiController::class, 'getRoles']);
    Route::post('get-escuelas', [SuperadmiController::class, 'getEscuelas']);
    Route::post('get-competencias', [SuperadmiController::class, 'getCompetencias']);

    // Módulo: asignación.
    Route::get('/asignacion', fn () => Inertia::render('Superadmi/Asignacion/index'))->name('asignacion-superadmi');
    Route::get('/asignacion-permisos', [PermisoAsignacionController::class, 'superadmin']);
    Route::post('/asignacion-permisos', [PermisoAsignacionController::class, 'guardar']);
    Route::post('/asignacion-masiva/preview', [DireccionAsignacionController::class, 'preview']);
    Route::post('/asignacion-masiva/crear-cursos', [DireccionAsignacionController::class, 'crearCursos']);
    Route::post('/asignacion-masiva/matricular', [DireccionAsignacionController::class, 'matricular']);
    //Route::get('asignacion', [SuperadmiController::class, 'index'])->name('asignacion-index');
    Route::post('get-cursos', [SuperadmiController::class, 'getCursos']);
    Route::post('get-detalle-curso', [SuperadmiController::class, 'getDetalleCurso']);
    // Route::post('asignar-curso-nivelacion', [SuperadmiController::class, 'asignarCursoNivelacion']);
    // Route::post('get-docente-competencia', [SuperadmiController::class, 'getDocentesXcompetencia']);
    // Route::post('save-curso', [SuperadmiController::class, 'save']);

    // Módulo: preguntas y encuestas.
    Route::get('get-pregunta', [SuperadmiController::class, 'getPregunta']);
    Route::get('/pregrentas', fn () => Inertia::render('Superadmi/Pregunta/pregunta'))->name('pregun');
    Route::post('guardarp', [SuperadmiController::class, 'savep']);
    Route::get('eliminarp/{id}', [SuperadmiController::class, 'eliminarp']);

    // Módulo: notas y perfiles.
    Route::get('/notasperf', [SuperadmiController::class, 'getTestResults']);
    Route::get('/notas-todosest', fn () => Inertia::render('Superadmi/notasperfiles/notasperfil'))->name('notas-todosest');

    // Módulo: información de estudiantes.
    Route::get('/get-estudiantes', [SuperadmiController::class, 'getEstudiantes']);
    Route::get('/estudiantesinfo', fn () => Inertia::render('Superadmi/Informacion/informacion'))->name('info_est');

    // Módulo: periodos académicos.
    Route::get('get-periodos', [SuperadmiController::class, 'getPeriodos']);
    Route::get('/periodos', fn () => Inertia::render('Superadmi/Periodos/Index'))->name('periodos');
    Route::post('guardar-periodo', [SuperadmiController::class, 'savePeriodo']);
    Route::get('eliminar-periodo/{id}', [SuperadmiController::class, 'eliminarPeriodo']);

    // Módulo: actualización de notas por periodo.
    Route::get('/notas/actualizar', fn () => Inertia::render('Superadmi/Notas/Actualizar'))
        ->name('superadmi.notas.actualizar');
    Route::post('/notas/preview', [SuperadmiController::class, 'previewUpdate'])
        ->name('superadmi.notas.preview');
    Route::post('/notas/execute', [SuperadmiController::class, 'executeUpdate'])
        ->name('superadmi.notas.execute');
    Route::get('/obtener-escuelas', [SuperadmiController::class, 'obtenerListadoEscuelas']);
    Route::get('/obtener-periodos', [SuperadmiController::class, 'obtenerListadoPeriodos']);

    // Módulo: importación de estudiantes.
        Route::get('/alumnos-importar', fn () => Inertia::render('Superadmi/Alumnos/index'))->name('alumnos-importar');
   // Route::post('importar-excel-estudiante', [AlumnoController::class, 'excelEstudiante']);
     Route::post('subir-estudiantes', [SuperadmiController::class, 'importarEstudiante']); //->name('alumnos-importar');

    // Módulo: eventos.
    Route::get('/eventos', fn () => Inertia::render('Superadmi/Eventos/Index'))->name('eventos');
    Route::get('get-eventos', [SorteoController::class, 'getEventos']);
    Route::post('guardar-evento', [SorteoController::class, 'guardarEvento']);
    Route::get('eliminar-evento/{id}', [SorteoController::class, 'eliminarEvento']);

    // Módulo: encargados del sistema.
    Route::get('/encargados-sistema/data', [SupervisorController::class, 'listarEncargadosSistema'])->name('encargados-sistema.data');
    Route::get('/encargados-sistema', fn () =>Inertia::render('Superadmi/Encargados/index'))->name('encargados-sistema');

    // Módulo: controles del sistema.
    Route::get('controles', fn () => Inertia::render('Superadmi/Controles/Index'))->name('controlesadmin');
    Route::post('get-controles', [BotonControlController::class, 'listarReglas']);
    Route::post('update-controles', [BotonControlController::class, 'actualizarReglas']);

    // Módulo: docentes por competencia.
    Route::get('/docentes-competencias-data', [SupervisorController::class, 'getDocentesCompetencias']);
    Route::get('/docentes-competencias', fn () => Inertia::render('Superadmi/docentesCompetencias/index'))->name('docentes-competencia');

    // Módulo: búsqueda de estudiantes.
    Route::get('/busqueda-estudiantes', [SupervisorController::class, 'busquedaEstudiantes']);
    Route::get('/estudiantes', fn () => Inertia::render('Superadmi/Estudiante/index'))->name('busqueda-estudiante');

    // Módulo: notificaciones.
    Route::get('notificaciones', fn () => Inertia::render('Superadmi/Notificaciones/Index'))->name('notificaciones.admin');
    Route::post('listar-notificaciones', [UsuarioController::class, 'listarNotificaciones']);
    Route::post('buscar-usuarios-notificaciones', [UsuarioController::class, 'buscarUsuariosNotificaciones']);
    Route::post('guardar-notificacion', [UsuarioController::class, 'guardarNotificacion']);
    Route::post('actualizar-notificacion/{id}', [UsuarioController::class, 'actualizarNotificacion']);
    Route::post('eliminar-notificacion/{id}', [UsuarioController::class, 'eliminarNotificacion']);
    Route::post('subir-imagen-notificacion', [UsuarioController::class, 'subirImagenNotificacion']);

    // Módulo: integración con Admisión.
    Route::get('/admision-integracion', [AdmisionIntegracionController::class, 'index'])->name('admision-integracion');
    Route::get('/admision/procesos', [AdmisionIntegracionController::class, 'procesos']);
    Route::post('/admision/sincronizar-procesos', [AdmisionIntegracionController::class, 'sincronizarProcesos']);
    Route::post('/admision/asignar-periodo', [AdmisionIntegracionController::class, 'asignarPeriodo']);
    Route::get('/admision/programas', [AdmisionIntegracionController::class, 'programas']);
    Route::post('/admision/sincronizar-postulantes', [AdmisionIntegracionController::class, 'sincronizarPostulantes']);
    Route::post('/admision/importar-matriz', [AdmisionIntegracionController::class, 'importarMatriz']);
    Route::get('/admision/resumen', [AdmisionIntegracionController::class, 'resumen']);
    Route::get('/admision/cruce', [AdmisionIntegracionController::class, 'cruce']);
    Route::post('/admision/verificar-postulantes', [AdmisionIntegracionController::class, 'verificarPostulantes']);
    Route::get('/admision-reportes', [AdmisionIntegracionController::class, 'reporte'])->name('admision-reportes');
    Route::get('/admision/reporte-data', [AdmisionIntegracionController::class, 'reporteData']);
    });

// Rutas públicas o especiales.
Route::get('/dni/{dni}', [SuperadmiController::class, 'show'])->whereNumber('dni');
Route::get('/fichariesgo', [FichaRiesgoController::class,'index'])->name('fichariesgo');
Route::post('/fichariesgo/guardar', [FichaRiesgoController::class,'guardar'])->name('fichariesgo.guardar');

// Autenticación Laravel/Breeze.
require __DIR__.'/auth.php';
