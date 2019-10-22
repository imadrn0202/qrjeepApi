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


Route::post('checkMobileNumber', 'Api\UserController@checkMobileNumber')->name('checkMobileNumber');

Route::post('validateLogin', 'Api\UserController@validateLogin')->name('validateLogin');



Route::group(['middleware' => 'auth:api'], function(){
Route::post('addBalance', 'Api\UserController@addBalance')->name('addBalance');
Route::post('checkBalance', 'Api\UserController@checkBalance')->name('checkBalance');
Route::post('checkPin', 'Api\UserController@checkPin')->name('checkPin');
});