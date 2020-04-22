<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/new', 'TestController@controllerMethod');

Route::any('{slug}', function () {
    return view('welcome');
});

Route::post('/app/create_tag', 'AdminController@createTag');
Route::post('/app/edit_tag', 'AdminController@edit_tag');
Route::post('/app/delete_tag', 'AdminController@delete_tag');
Route::get('/app/get_tags', 'AdminController@getTags');

Route::post('/app/upload', 'AdminController@upload');
Route::post('app/delete_image', 'AdminController@delete_image');
