<?php

use Illuminate\Http\Request;

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
Route::post('/auth/login', 'UserController@authenticate');


Route::group(['middleware' => ['jwt.auth']], function() {
    Route::get('/auth/list/{limit}', 'UserController@list');
    Route::get('/auth/show/{id}', 'UserController@show');
    Route::post('/auth/update', 'UserController@update');
    Route::post('/auth/logout', 'UserController@logout');
    Route::post('/auth/register', 'UserController@register');

    Route::get('/articles/{limit}', 'ArticleController@index');
    Route::get('/articles/{id}', 'ArticleController@show');
    Route::post('/articles/save', 'ArticleController@store');
    Route::post('/articles/update', 'ArticleController@update');
    Route::get('/articles/delete/{id}', 'ArticleController@delete');
});