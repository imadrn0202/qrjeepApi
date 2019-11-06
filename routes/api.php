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
//balance
Route::post('addBalance', 'Api\UserController@addBalance')->name('addBalance');
Route::post('checkBalance', 'Api\UserController@checkBalance')->name('checkBalance');

//pin
Route::post('checkPin', 'Api\PinController@checkPin')->name('checkPin');
Route::post('updatePin', 'Api\PinController@updatePin')->name('updatePin');
Route::post('hasPin', 'Api\PinController@hasPin')->name('hasPin');

//email verification
Route::post('addEmail', 'Api\EmailVerificationController@addEmail')->name('addEmail');
Route::post('verifyEmail', 'Api\EmailVerificationController@verifyEmail')->name('verifyEmail');
Route::post('hasVerifiedEmail', 'Api\EmailVerificationController@checkIfUserHasVerifiedEmail')->name('checkIfUserHasVerifiedEmail');




//fare
Route::post('payFare', 'Api\FareMatrixController@payFare')->name('payFare');
Route::post('getDestinations', 'Api\FareMatrixController@getDestination')->name('destination');
Route::get('getOrigins', 'Api\FareMatrixController@getOrigin')->name('origin');
Route::post('getFare', 'Api\FareMatrixController@getFare')->name('getFare');



});