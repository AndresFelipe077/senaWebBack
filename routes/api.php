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
use App\Http\Controllers\TipoRapsController;
use App\Http\Controllers\configuracionRapController;

use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

use App\Http\Controllers\gestion_grupo\AsignacionJornadaGrupoController;
use App\Http\Controllers\gestion_grupo\EstadoGrupoController;
use App\Http\Controllers\gestion_grupo\GrupoController;
use App\Http\Controllers\gestion_grupo\NivelFormacionController;
use App\Http\Controllers\gestion_grupo\TipoFormacionController;
use App\Http\Controllers\gestion_grupo\TipoGrupoController;
use App\Http\Controllers\gestion_grupo\TipoOfertaController;
use App\Http\Controllers\HorarioInfraestructuraGrupoController;
use App\Models\AsignacionParticipante;

use App\Http\Controllers\gestion_infraestructuras\AreaController;
use App\Http\Controllers\gestion_infraestructuras\InfraestructuraController;
use App\Http\Controllers\gestion_infraestructuras\SedeController;
use App\Http\Controllers\MatriculaController;
use App\Http\Controllers\TipoParticipacionController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\AprendicesTmpController;
use App\Http\Controllers\AsignacionFaseProyFormativoController;
use App\Http\Controllers\EstadoController;
use App\Http\Controllers\pruebaController;
use App\Models\asignacionFaseProyFormativo;

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
Route::get('competencias/actividad_proyecto/{id}', [CompetenciasController::class,'showByIdActividadP']);

//rutas para resultado aprendizaje 2 vanesa
Route::resource('resultadoAprendizaje', resultadoAprendizajeController::class);
Route::get('resultadoAprendizaje/competencia/{id}', [resultadoAprendizajeController::class,'showByIdCompetencia']);

//asignacion competencias raps
Route::resource('competenciaRap', asignacionCompetenciaRapController::class);
Route::get('competenciaRap/competencia/{id}', [asignacionCompetenciaRapController::class, 'showByCompetencia']);
//rutas para tipo resultados aprendizaje
Route::resource('tipo_resultados',  TipoRapsController::class);
//rutas para actividad aprendizaje 3 vanesa
Route::resource('actividadAprendizaje', actividadAprendizajeController::class);
Route::get('actividadAprendizaje/rap/{id}', [actividadAprendizajeController::class,'showByIdRap']);

Route::resource('asignacionFaseP', AsignacionFaseProyFormativoController::class);
Route::get('asignacionFaseP/proyecto/{id}', [AsignacionFaseProyFormativoController::class,'showByIdProyecto']);


//ruta tipo_programas
Route::resource('tipo_programas',  TipoProgramasController::class);
//ruta para programas
Route::resource('programas',  ProgramaController::class);
//ruta asignar y guardar competencias raps
Route::post('resultados', [resultadoAprendizajeController::class, 'store'])->name('resultados.store');
//ruta para proyecto formativo
Route::resource('proyecto_formativo', ProyectoFormativoController::class);
Route::get('proyecto_formativo/programa/{id}', [ProyectoFormativoController::class,'showByIdPrograma']);
//ruta para fases
Route::resource('fases', FaseController::class);
Route::get('fases/proyecto/{id}', [FaseController::class,'showByIdProyecto']);

//ruta para actividades de proyecto
Route::resource('actividad_proyecto', ActividadProyectoController::class);
Route::get('actividad_proyecto/fase/{id}', [ActividadProyectoController::class,'showByIdFase']);
//ruta para configuracion de rap
Route::resource('configuracion_rap', configuracionRapController::class);
//ruta para transferir participantes de fichas
Route::post('transferir-ficha', [configuracionRapController::class, 'transferirFicha']);
//ruta para optener los resultados de un participante
Route::get('participantes/{participante_id}/resultados', [configuracionRapController::class , 'obtenerResultados']);


//rutas para ciudad y departamento
Route::resource('departamentos', CountryController::class);
Route::resource('ciudades', CityController::class);
Route::get('ciudades/departamento/{id}', [CityController::class, 'showByDepartamento']);

//rutas sede -> revisar y optimizar
Route::resource('sedes',SedeController::class);
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
Route::resource('grupos', GrupoController::class);
//buscador para el controlador grupos
Route::get('obtenergrupos', [GrupoController::class, 'buscarGrupos']);
Route::get('grupos/infraestructura/{id}', [GrupoController::class,'showByIdInfra']);
Route::get('grupos/sede/{id}', [GrupoController::class,'showByIdSede']);

Route::get('usuarios_instructores', [UserController::class, 'instructores']);





Route::get('usuarios_aprendices', [UserController::class, 'aprendicesActives']);



//tipo de grupos
Route::resource('tipogrupos', TipoGrupoController::class);

Route::resource('gruposjornada', AsignacionJornadaGrupoController::class);

Route::get('jornadagrupo/grupo/{id}', [AsignacionJornadaGrupoController::class, 'showByGrupo']);

Route::resource('niveles_formacion', NivelFormacionController::class);

Route::resource('tipo_formaciones', TipoFormacionController::class);

Route::resource('estado_grupos', EstadoGrupoController::class);

Route::resource('tipo_ofertas', TipoOfertaController::class);

Route::resource('horario_infraestructura_grupo', HorarioInfraestructuraGrupoController::class);

Route::get('horario_infraestructura_grupo/grupo/{id}', [HorarioInfraestructuraGrupoController::class, 'infraestructuraByGrupo']);


Route::resource('estados', EstadoController::class);



// ttttttttttttttttttttttt

Route::resource('asignacion_participante', AsignacionParticipante::class);
// gggggggggggggggg



Route::resource('personas', PersonController::class);

//regional
Route::resource('regionales', RegionalController::class);

Route::resource('centroFormacion', CentroFormacionController::class);

Route::resource('matriculas', MatriculaController::class);

Route::get('personByIdentificacion/{identificacion}', [PersonController::class, 'personByIdentificacion']);



Route::resource('asignacionParticipantes', AsignacionParticipanteController::class);

Route::resource('asignacionParticipantes', AsignacionParticipanteController::class);




Route::resource('tipoPar', TipoParticipacionController::class);




Route::get('/asignacionParticipantes/programas/{idPrograma}/grupos', [AsignacionParticipanteController::class, 'obtenerGruposPorPrograma']);

Route::get('participantesPro', [AsignacionParticipanteController::class, 'obtenerAsignacionesParticipantes']);

Route::get('/asignacionParticipantes/grupos/{idGrupo}/aprendices', [AsignacionParticipanteController::class, 'obtenerAprendicesPorGrupo']);


//



Route::get('search/{table}/{query}',[QueryController::class,'show']);





Route::post('aprendis',[AprendicesTmpController::class,'importar']);
Route::post('prueba',[pruebaController::class,'import']);



Route::post('importarexcel', [AprendicesTmpController::class, 'prueba']);

/////////////// asignacion roles 
Route::post('asignation/{id}', [Gestion_usuarioUserController::class ,'asignation']);
