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

Route::post('/auth/login', 'UserController@authenticate');
Route::get('/auth/personal_menu', 'UserController@personal_menu');

Route::group(['middleware' => ['jwt.auth']], function() {
    Route::post('/auth/logout', 'UserController@logout');
    Route::post('/auth/register', 'UserController@register');
    Route::get('/auth/list/{limit}', 'UserController@list');
    Route::get('/auth/{id}', 'UserController@single');
    Route::post('/auth/update', 'UserController@update');

    Route::get('/articles/{limit}', 'ArticleController@index');
    Route::get('/articles/{id}', 'ArticleController@single');
    Route::post('/articles/save', 'ArticleController@store');
    Route::post('/articles/update', 'ArticleController@update');
    Route::get('/articles/delete/{id}', 'ArticleController@delete');
});