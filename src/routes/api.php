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

Route::post('login', 'LoginController@login');

Route::post('create', 'CreateController@create');

Route::get('image', 'ImageController@index');

Route::get('goods', 'GoodsController@all');
Route::post('search', 'GoodsController@search');
Route::get('goods/{id}', 'GoodsController@detail');
Route::post('review', 'GoodsController@postReview');

Route::get('user', 'UserController@get');
Route::get('user/all', 'UserController@all');
Route::get('history', 'UserController@getPurchaseHistory');

Route::post('buy', 'BuyController@buy');
Route::get('drybuy', 'BuyController@dryBuy');

Route::post('forget/send', 'ForgetPasswordController@sendToken');
Route::post('forget/validate', 'ForgetPasswordController@validateToken');
Route::post('forget/resetpass', 'ForgetPasswordController@resetPassword');