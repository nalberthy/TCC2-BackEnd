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


Route::post('auth/login', 'Api\AuthController@login');
Route::get('auth/me', 'Api\AuthController@me');
Route::get('users', 'Api\UserController@index');

// Route::get('derivacao/teste', 'Api\CalculoProposicionalController@Teste');
Route::post('derivacao/inicializar', 'Api\CalculoProposicionalController@Derivacao');
Route::post('derivacao/derivar', 'Api\CalculoProposicionalController@Derivar');

Route::group(['middleware'=>['apiJwt']],function(){
    Route::post('auth/logout', 'Api\AuthController@logout');
    // Route::get('users', 'Api\UserController@index');


    Route::get('recompensas', 'Api\RecompensaController@index');


    Route::get('mvflp/niveis', 'Api\NivelMvflpController@index');
    Route::get('mvflp/niveis/listarTodos', 'Api\NivelMvflpController@all');
    Route::get('mvflp/niveis/{id}', 'Api\NivelMvflpController@show');
    Route::post('mvflp/niveis', 'Api\NivelMvflpController@store');
    Route::put('mvflp/niveis/{id}', 'Api\NivelMvflpController@update');
    Route::delete('mvflp/niveis/{id}', 'Api\NivelMvflpController@destroy');

    Route::get('mvflp/exercicio/nivel/{id}', 'Api\ExercicioMvflpController@byIdNivel');






});
