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

Route::get('/', 'TopController@index');
Route::get('admin', 'AdminController@index');

Route::get('login', 'LoginController@index');

Route::get('create', 'CreateController@index');

Route::get('logout', 'LogoutController@index');

Route::get('forget', 'ForgetPasswordController@index');

Route::get('goods/{id}', 'GoodsController@index');

Route::get('buy/{id}', 'BuyController@index');

Route::get('user', 'UserController@index');