<?php

use App\Http\Controllers\ActividadProyectoController;
use App\Http\Controllers\asignacionCompetenciaRapController;
use App\Http\Controllers\AsignacionParticipanteController;
use App\Http\Controllers\gestion_empresa\CompanyController;
use App\Http\Controllers\gestion_rol\RolController;
use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\auth\UserController;
use App\Http\Controllers\CentroFormacionController;
use App\Http\Controllers\gestion_programas\CompetenciasController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\FaseController;
use App\Http\Controllers\gestion_dia\DiaController;
use App\Http\Controllers\gestion_dia_jornada\DiaJornadaController;
use App\Http\Controllers\gestion_jornada\JornadaController;
use App\Http\Controllers\gestion_mediopago\MedioPagoController;
use App\Http\Controllers\gestion_notificacion\NotificacionController;
use App\Http\Controllers\gestion_proceso\ProcesoController;
use App\Http\Controllers\gestion_rol_permisos\AsignacionRolPermiso;
use App\Http\Controllers\gestion_tipo_documento\TipoDocumentoController;
use App\Http\Controllers\gestion_tipopago\TipoPagoController;
use App\Http\Controllers\gestion_tipotransaccion\TipoTransaccionController;
use App\Http\Controllers\gestion_usuario\UserController as Gestion_usuarioUserController;
use App\Http\Controllers\gestion_programas\resultadoAprendizajeController;
use App\Http\Controllers\gestion_programas\actividadAprendizajeController;
use App\Http\Controllers\ProgramaController;
use App\Http\Controllers\ProyectoFormativoController;
use App\Http\Controllers\TipoProgramasController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\RegionalController;
use App\Http\Controllers\gestion_configuracion_rap\ConfiguracionRapController;
use App\Http\Controllers\AsignacionCompetenciaProyectoController;
use App\Http\Controllers\PlaneacionController;


use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

use App\Http\Controllers\gestion_grupo\AsignacionJornadaGrupoController;
use App\Http\Controllers\gestion_grupo\EstadoGrupoController;
use App\Http\Controllers\gestion_grupo\GrupoController;
use App\Http\Controllers\gestion_grupo\TipoFormacionController;
use App\Http\Controllers\gestion_grupo\TipoGrupoController;
use App\Http\Controllers\gestion_grupo\TipoOfertaController;
use App\Http\Controllers\gestion_grupo\HorarioInfraestructuraGrupoController;
use App\Http\Controllers\gestion_grupo\ActividadEventoController;
use App\Models\AsignacionParticipante;

use App\Http\Controllers\gestion_infraestructuras\AreaController;
use App\Http\Controllers\gestion_infraestructuras\InfraestructuraController;
use App\Http\Controllers\gestion_infraestructuras\SedeController;
use App\Http\Controllers\MatriculaController;
use App\Http\Controllers\TipoParticipacionController;
use App\Models\TipoParticipacion;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\AprendicesTmpController;
use App\Http\Controllers\AsignacionFaseProyFormativoController;
use App\Http\Controllers\CriteriosEvaluacion;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\gestion_grupo\AsignacionJornadaActividadEventoController;
use App\Http\Controllers\HistorialDocumentoController;
use App\Http\Controllers\EstadoProgramaController;
use App\Http\Controllers\pruebaController;
use App\Http\Controllers\TipoCompetenciasController;
use App\Models\asignacionCompetenciaProyecto;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);
Route::post('/login', [LoginController::class, 'authenticate']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);
    Route::get('/user', [UserController::class, 'logged']);
    Route::post('/user_company/{idUserActive}', [UserController::class, 'setCompany']);
});

Route::resource('roles', RolController::class);
Route::get('list_companies', [CompanyController::class, 'index']);

//permisos
Route::get('permisos', [AsignacionRolPermiso::class, 'index']);
Route::get('permisos_rol', [AsignacionRolPermiso::class, 'permissionsByRole']);
Route::put('asignar_rol_permiso', [AsignacionRolPermiso::class, 'assignFunctionality']);

// notificaciones
Route::resource('notificaciones', NotificacionController::class);
Route::put('notificaciones/read/{id}', [NotificacionController::class, 'read']);

// proceso
Route::resource('procesos', ProcesoController::class);

// tipo documento
Route::resource('tipo_documentos', TipoDocumentoController::class);
// medio pagos
Route::resource('medio_pagos', MedioPagoController::class);
// tipo pagos
Route::resource('tipo_pagos', TipoPagoController::class);
// tipo transaccion
Route::resource('tipo_transacciones', TipoTransaccionController::class);
// traer listado de los usuario por empresa
Route::get('lista_usuarios', [Gestion_usuarioUserController::class, 'index']);

Route::resource('usuarios', Gestion_usuarioUserController::class);

Route::put('asignar_roles', [Gestion_usuarioUserController::class, 'asignation']);


// crear ruta para competencias 1 vanesa
Route::resource('competencias', CompetenciasController::class);
Route::get('competencias/actividad_proyecto/{id}', [CompetenciasController::class, 'showByIdActividadP']);

//rutas para resultado aprendizaje 2 vanesa
Route::resource('resultadoAprendizaje', resultadoAprendizajeController::class);
Route::get('resultadoAprendizaje/competencia/{id}', [resultadoAprendizajeController::class, 'showByIdCompetencia']);

//asignacion competencias raps
Route::resource('competenciaRap', asignacionCompetenciaRapController::class);
Route::get('competenciaRap/competencia/{id}', [asignacionCompetenciaRapController::class, 'showByCompetencia']);
//rutas para tipo resultados aprendizaje
Route::resource('tipo_competencias',  TipoCompetenciasController::class);
//rutas para actividad aprendizaje 3 vanesa
Route::resource('actividadAprendizaje', actividadAprendizajeController::class);
Route::get('actividadAprendizaje/rap/{id}', [actividadAprendizajeController::class, 'showByIdRap']);

Route::resource('asignacionFaseP', AsignacionFaseProyFormativoController::class);
Route::get('asignacionFaseP/proyecto/{id}', [AsignacionFaseProyFormativoController::class, 'showByIdProyecto']);

Route::resource('asignacionCompetenciaProyecto', AsignacionCompetenciaProyectoController::class);
Route::get('asignacionCompetenciaProyecto/proyecto/{id}', [AsignacionCompetenciaProyectoController::class, 'showByIdProyecto']);

Route::resource('planeacion', PlaneacionController::class);
Route::get('planeacion/actividadProyecto/{id}', [PlaneacionController::class, 'showByIdActividadProyecto']);
Route::post('planeacions', [PlaneacionController::class, 'store']);
Route::get('planeacion/resultado/{id}', [PlaneacionController::class, 'showByRestultado']);
Route::delete('/planeacion/{id}', [PlaneacionController::class, 'destroy']);


//ruta tipo_programas
Route::resource('tipo_programas',  TipoProgramasController::class);
//ruta Estado programa
Route::resource('estado_programa',  EstadoProgramaController::class);
//ruta para programas
Route::resource('programas',  ProgramaController::class);
//ruta asignar y guardar competencias raps
Route::post('resultados', [resultadoAprendizajeController::class, 'store'])->name('resultados.store');
//ruta para proyecto formativo
Route::resource('proyecto_formativo', ProyectoFormativoController::class);
Route::get('proyecto_formativo/programa/{id}', [ProyectoFormativoController::class, 'showByIdPrograma']);
//ruta para fases
Route::resource('fases', FaseController::class);
Route::get('fases/proyecto/{id}', [FaseController::class, 'showByIdProyecto']);

//ruta para actividades de proyecto
Route::resource('actividad_proyecto', ActividadProyectoController::class);
Route::get('actividad_proyecto/fase/{id}', [ActividadProyectoController::class, 'showByIdFase']);
//ruta para configuracion de rap
Route::resource('configuracion_rap', ConfiguracionRapController::class);
//ruta para transferir participantes de fichas
Route::post('transferir-ficha', [ConfiguracionRapController::class, 'transferirFicha']);
//ruta para optener los resultados de un participante
Route::get('participantes/{participante_id}/resultados', [ConfiguracionRapController::class, 'obtenerResultados']);


//rutas para ciudad y departamento
Route::resource('departamentos', CountryController::class);
Route::resource('ciudades', CityController::class);
Route::get('ciudades/departamento/{id}', [CityController::class, 'showByDepartamento']);

//rutas sede -> revisar y optimizar
Route::resource('sedes', SedeController::class);
Route::get('sedes/ciudad/{id}', [SedeController::class, 'showByCiudad']);

//ruta de areas
Route::resource('areas', AreaController::class);

//rutas de infraestructura -> revisar y optimizar (crear un grupo de rutas como en ciudades)
Route::resource('infraestructuras', InfraestructuraController::class);
Route::get('infraestructuras/sede/{id}', [InfraestructuraController::class, 'showBySede']);
Route::get('infraestructuras/area/{id}', [InfraestructuraController::class, 'showByArea']);
Route::get('infraestructuras/sede/{idSede}/area/{idArea}', [InfraestructuraController::class, 'showBySedeArea']);


//jornadas
Route::resource('jornadas', JornadaController::class);
//dia
Route::resource('dias', DiaController::class);
//traer diaJornada
Route::get('diajornada/jornada/{id}', [DiaJornadaController::class, 'showByJornada']);

//grupos
// Get infraestructura and sede
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('grupos/infraestructura/{id}', [GrupoController::class, 'showByIdInfra']);
    Route::get('grupos/sede/{id}', [GrupoController::class, 'showByIdSede']);
});
Route::get('usuarios_instructores', [UserController::class, 'instructores']);
Route::resource('grupos', GrupoController::class);

Route::middleware(['auth:sanctum'])->group(function () {

    // Route::resource('grupos', GrupoController::class);

    Route::get('ficha_tipo_grupo', [TipoGrupoController::class, 'getTipoGrupoFicha']);

    Route::get('especial_tipo_grupo', [TipoGrupoController::class, 'getTipoGrupoEspecial']);

    Route::get('especiales_by_grupos', [GrupoController::class, 'getGruposByEspecial']);

    Route::get('fichas_by_grupos', [GrupoController::class, 'getGruposByFicha']);

    Route::resource('tipogrupos', TipoGrupoController::class);

    Route::resource('actividad_eventos', ActividadEventoController::class);

    Route::resource('gruposjornada', AsignacionJornadaGrupoController::class);

    Route::get('jornadagrupo/grupo/{id}', [AsignacionJornadaGrupoController::class, 'showByGrupo']);

    Route::resource('actividad_eventos_jornada', AsignacionJornadaActividadEventoController::class);

    Route::get('jornada_actividad/actividad_evento/{id}', [AsignacionJornadaActividadEventoController::class, 'showByActividadEventos']);

    Route::resource('tipo_formaciones', TipoFormacionController::class);

    Route::resource('estado_grupos', EstadoGrupoController::class);

    Route::resource('tipo_ofertas', TipoOfertaController::class);

    Route::resource('horario_infraestructura_grupo', HorarioInfraestructuraGrupoController::class);

    Route::get('horario_infraestructura_grupo/grupo/{id}', [HorarioInfraestructuraGrupoController::class, 'infraestructuraByGrupo']);

    // Querys searchs
    Route::get('tipo_grupos_by_parameter/{nombreTipoGrupo}', [GrupoController::class, 'getTipoGrupoByParameter']);
});

Route::resource('estados', EstadoController::class);






Route::resource('personas', PersonController::class);

//regional
Route::resource('regionales', RegionalController::class);

Route::resource('centroFormacion', CentroFormacionController::class);

Route::resource('matriculas', MatriculaController::class);

Route::get('personByIdentificacion/{identificacion}', [PersonController::class, 'personByIdentificacion']);









Route::resource('tipoPar', TipoParticipacionController::class);







////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

Route::get('asignacion_participante', [AsignacionParticipanteController::class, 'index']);

Route::get('usuarios_aprendices', [UserController::class, 'aprendicesActives']);    //usuarios que son aprendices



Route::post('buscarProgramas',  [ProgramaController::class, 'buscarProgramas']); // SE BUSCA PROGRAMA

// showByIdPrograma SE OBTIENE ES PROYECTOFORMATIVO

Route::get('grupos/programa/{id}', [GrupoController::class, 'showByIdProyectoFor']); // se encuentra en grupo POR MEDIO DE ESE PROYECTOF






Route::get('participantesPro', [AsignacionParticipanteController::class, 'obtenerAsignacionesParticipantes']);

Route::get('/asignacionParticipantes/grupos/{idGrupo}/aprendices', [AsignacionParticipanteController::class, 'obtenerAprendicesPorGrupo']);

Route::post('asignar-nuevo-tipo', [AsignacionParticipanteController::class, 'asignarNuevoTipo']);





///////////////////////////////////////////////////////////



Route::get('search/{table}/{query}', [QueryController::class, 'show']);





Route::post('aprendis', [AprendicesTmpController::class, 'importar']);
Route::post('prueba', [pruebaController::class, 'import']);



Route::post('importarexcel', [AprendicesTmpController::class, 'prueba']);

/////////////// asignacion roles
Route::post('asignation/{id}', [Gestion_usuarioUserController::class, 'asignation']);


Route::get('usuarios/{id}/roles', [Gestion_usuarioUserController::class, 'filtrarRolesAsignados']);
Route::post('usuarios/{id}/desasignar-roles', [Gestion_usuarioUserController::class, 'unassignRoles']);
Route::delete('/user/{id}', [Gestion_usuarioUserController::class, 'destroy']);


/////// criterios evaluacion
Route::get('criteriosEvalucaicon', [CriteriosEvaluacion::class, 'index']);
Route::delete('/criterio/delete/{id}', [CriteriosEvaluacion::class, 'delete']);
Route::post('/criterio/update/{id}', [CriteriosEvaluacion::class, 'update']);
Route::post('criteriosEvalucaiconsup', [CriteriosEvaluacion::class, 'store']);

Route::get('criteriosevaluacion/consulta/{id}', [CriteriosEvaluacion::class, 'consulta']);



/////////////////////////
Route::post('/guardar-registros', [AsignacionCompetenciaProyectoController::class, 'guardarRegistros']);
//////////////////////////////////competencias checks
Route::get('proyectos/{id}/Competencias', [ProyectoFormativoController::class,'filtrarCompetenciasAsignadas' ]);
Route::post('/proyecto-formativo/{id}/competencias', [ProyectoFormativoController::class, 'assignCompetences']);
Route::post('/proyectos/{id}/eliminarCompetencias', [ProyectoFormativoController::class, 'eliminarCompetencias']);
Route::delete('/proyectoFormativo/{idProyectoFormativo}/competencias', [ProyectoFormativoController::class, 'eliminarMultipleCompetences']);


///////////////////////


//////////////

Route::get('crear-historial', [AsignacionParticipanteController::class, 'crearHistorialDesdeRegistros']);
// Obtain consultation of hours that are lost due to raps that the competition has depending on the attendance of the instructor
Route::get('horas_raps_perdidos/{idInstructor}', [ConfiguracionRapController::class, 'getHoursLostForRapInCompetenciaByInstructor']);

Route::post('assig_instructor_to_ficha', [AsignacionParticipanteController::class, 'assignInstructorToFicha']);

Route::post('assig_aprendices_to_ficha', [AsignacionParticipanteController::class, 'assignAprendicesToFicha']);

Route::get('fichas_by_instructor/{idInstructor}', [AsignacionParticipanteController::class, 'getFichasByInstructorLider']);

Route::get('asignacion_fichas_by_id/{idFicha}', [AsignacionParticipanteController::class, 'getFichasById']);

Route::get('get_last_ficha/{idLastFicha}', [AsignacionParticipanteController::class, 'getLastFichaById']);

Route::get('get_register_ficha/{idFicha}', [GrupoController::class, 'createConfiguracionRapByGrupo']);
