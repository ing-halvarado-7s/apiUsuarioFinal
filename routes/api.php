<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('iniciarsesion', 'App\Http\Controllers\UserController@iniciarsesion');

Route::group(['middleware' => ['jwt.verify']], function () {
    Route::post('cerrarsesion', 'App\Http\Controllers\UserController@cerrarSesion');

    Route::post('usuarios/crear', 'App\Http\Controllers\UserController@crear')
        ->middleware('permission:crear');
    Route::get('usuarios/listar', 'App\Http\Controllers\UserController@listar')
        ->middleware('permission:listar');
    Route::post('usuarios/actualizar/{id}', 'App\Http\Controllers\UserController@actualizar')
        ->middleware('permission:actualizar');
    Route::delete('usuarios/eliminar/{id}', 'App\Http\Controllers\UserController@eliminar')
        ->middleware('permission:eliminar');
    Route::get('usuarios/mostrardetalle/{id}', 'App\Http\Controllers\UserController@mostrarDetalle')
        ->middleware('permission:mostrarDetalle');

    Route::post('roles/asignarol', 'App\Http\Controllers\RoleController@asignarRol')
        ->middleware('permission:asignarol');
    Route::post('roles/quitarrol', 'App\Http\Controllers\RoleController@quitarRol')
        ->middleware('permission:quitarrol');
    Route::post('agregarpermisorol', 'App\Http\Controllers\RoleController@agregarPermisoRol')
        ->middleware('permission:agregarpermisorol');
    Route::post('quitarpermisorol', 'App\Http\Controllers\RoleController@quitarPermisoRol')
        ->middleware('permission:quitarpermisorol');
});
