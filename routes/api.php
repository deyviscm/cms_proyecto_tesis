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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

AdvancedRoute::controller('encuesta', 'Api\EncuestaController');

Route::group([
    'middleware' => ['api','auth:api'],
], function ($router) {
    AdvancedRoute::controller('menu', 'Api\MenuController');
    AdvancedRoute::controller('usuario', 'Api\UsuarioController');
    AdvancedRoute::controller('ordenes-compras', 'Api\OrdenesComprasController');
    AdvancedRoute::controller('categorias', 'Api\ProductosCategoriasController');
    AdvancedRoute::controller('productos', 'Api\ProductosController');
});

Route::group(['prefix' => 'archivos'], function(){
    Route::post('/guardarArchivo', 'Api\FileController@guardarArchivosUp');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('create', function(){
        \App\User::create([
            'name' => 'Brandon',
            'user' => 'test',
            'email' => 'test@gmail.com',
            'password' => bcrypt('123'),
        ]);
        return response()->json(['success' => true]);
    });
    Route::post('login', 'AuthController@login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});