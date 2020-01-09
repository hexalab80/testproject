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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('signUp', 'Api\LoginController@signUp');
Route::post('signIn', 'Api\LoginController@signIn');
Route::post('socialLogin', 'Api\LoginController@socialLogin');
Route::post('forgotPassword', 'Api\LoginController@forgotPassword');
Route::post('resetPassword', 'Api\LoginController@resetPassword');

Route::group(['middleware' => 'auth:api'], function(){
Route::post('settingInfo', 'Api\UserController@getSetting');
Route::post('changePassword', 'Api\UserController@change_password');
Route::put('users/{id}', 'Api\UserController@update');
Route::put('users/{id}/image', 'Api\UserController@updateImage');
Route::post('user/updateFcmToken', 'Api\UserController@updateFcmToken');
Route::post('add_list', 'Api\StepController@add_steps');
Route::get('fetchlist', 'Api\StepController@fetchStep');
Route::get('allfetchlist','Api\StepController@fetchAllListByUser');
Route::get('rewardlist', 'Api\StepController@rewardlist');
Route::post('amtScratch','Api\StepController@getAmountScratch');
Route::post('coinclaim', 'Api\StepController@getCoinClaim');
Route::post('rewardHistory', 'Api\StepController@rewardHistory');
Route::post('walletHistory', 'Api\StepController@walletHistory');
Route::post('paytmRequest', 'Api\StepController@paytmRequest');
Route::get('reward_ads', 'Api\StepController@rewardads');
Route::get('user_info', 'Api\StepController@getUser');
Route::get('todaySteps', 'Api\StepController@getPerdayStepsByuser');
Route::get('checkTest','Api\StepController@checkTest');
Route::get('testImage', 'Api\StepController@helloImage');

Route::post('coupons','Api\StepController@lucky_coupons');
Route::get('latest_user','Api\StepController@toptenUser');
/*Route::post('add_list', 'Api\ListController@add_list');
Route::get('fetchlist', 'Api\ListController@fetchList');
Route::get('allfetchlist', 'Api\ListController@fetchAllListByUser');
Route::get('monthlist/{num}', 'Api\ListController@getMonthList'); */
});