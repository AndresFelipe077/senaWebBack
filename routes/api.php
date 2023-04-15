<?php

use App\Http\Controllers\gestion_empresa\CompanyController;
use App\Http\Controllers\gestion_rol\RolController;
use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\auth\UserController;
use App\Http\Controllers\gestion_mediopago\MedioPagoController;
use App\Http\Controllers\gestion_notificacion\NotificacionController;
use App\Http\Controllers\gestion_proceso\ProcesoController;
use App\Http\Controllers\gestion_rol_permisos\AsignacionRolPermiso;
use App\Http\Controllers\gestion_tipo_documento\TipoDocumentoController;
use App\Http\Controllers\gestion_tipopago\TipoPagoController;
use App\Http\Controllers\gestion_tipotransaccion\TipoTransaccionController;
use App\Http\Controllers\gestion_usuario\UserController as Gestion_usuarioUserController;
<<<<<<< HEAD
use App\Http\Controllers\gestion_jornada\JornadaController;
use App\Http\Controllers\gestion_dia\DiaController;
use App\Http\Controllers\gestion_dia_jornada\DiaJornadaController;
=======
>>>>>>> e3805782842a9940c72d8b2e0d8bbc0a2af666ae
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use App\Http\Controllers\gestion_grupo\GrupoController;
use App\Http\Controllers\gestion_grupo\TipoGrupoController;
use Illuminate\Support\Facades\DB;


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
Route::get('lista_usuarios', [Gestion_usuarioUserController::class, 'getUsers']);

Route::resource('usuarios', Gestion_usuarioUserController::class);

Route::put('asignar_roles', [Gestion_usuarioUserController::class, 'asignation']);

<<<<<<< HEAD
//jornadas
Route::resource('jornadas', JornadaController::class);
//dia
Route::resource('dias', DiaController::class);
//traer diaJornada
Route::get('diajornada/jornada/{id}', [DiaJornadaController::class,'showByJornada']);


=======
//grupos
Route::resource('grupos', GrupoController::class);
//buscador para el controlador grupos
Route::get('obtenergrupos', [GrupoController::class, 'buscarGrupos']);
//tipo de grupos
Route::resource('tipogrupos', TipoGrupoController::class);
>>>>>>> e3805782842a9940c72d8b2e0d8bbc0a2af666ae
