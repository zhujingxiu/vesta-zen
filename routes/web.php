<?php

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

Route::get('/test', 'TestController@index')->name('test');
Route::get('/test-site', 'TestController@site')->name('test-site');
Route::get('/test-db', 'TestController@database')->name('test-db');
Route::get('/test-cf', 'TestController@cloudflare')->name('test-cf');
Route::get('/test-v-pkg', 'TestController@package')->name('test-v-pkg');
Route::get('/test-restore', 'TestController@restore')->name('test-restore');

Route::get('/test-zc', 'TestController@zc')->name('test-zc');