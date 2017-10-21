<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('/users/register','UserController@register');
Route::post('/users/login','UserController@login');
Route::post('/users/follow','UserController@follow');
Route::post('/users/unFollow','UserController@unFollow');
Route::post('/users/profile','UserController@profile');
Route::post('/tweet','TweetController@insert');
Route::post('/tweet/stream','TweetController@stream');
// Route::post('/home','UserController@login');
// Route::post('/following','UserController@login');
// Route::post('/followers','UserController@login');
// Route::post('/tweets','UserController@login');
