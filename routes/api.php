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

Route::prefix('v1')->middleware('jwt.auth')->group(function () {
    Route::apiResource('cliente', 'App\Http\Controllers\ClienteController');
    Route::apiResource('carro', 'App\Http\Controllers\CarroController');
    Route::apiResource('marca', 'App\Http\Controllers\MarcaController');
    Route::apiResource('modelo', 'App\Http\Controllers\ModeloController');
    Route::apiResource('locacao', 'App\Http\Controllers\LocacaoController');
    Route::post('me', '\App\Http\Controllers\JWTAuthController@me');
    Route::post('refresh', '\App\Http\Controllers\JWTAuthController@refresh');
    Route::post('logout', '\App\Http\Controllers\JWTAuthController@logout');
});


Route::post('login', '\App\Http\Controllers\JWTAuthController@login');
