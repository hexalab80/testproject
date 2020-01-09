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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::group(['middleware' => 'auth'], function(){
  Route::get('/', function () {
      return view('welcome');
  });

Route::get('broadcasts','BroadcastController@index');
Route::get('broadcasts/create','BroadcastController@create');
Route::post('broadcasts', 'BroadcastController@store');
Route::get('users', 'UserDetailController@index');
Route::get('users/{id}', 'UserDetailController@show');
Route::put('users/{id}', 'UserDetailController@update');
Route::get('change_password', 'UserDetailController@change_password');
Route::post('change_password', 'UserDetailController@update_pass');
Route::post('users/serverProcessing', 'UserDetailController@serverProcessing');
Route::post('addMsg', 'MsgController@sendMsgByUser');

Route::get('paytm_requests', 'PaytmController@index');
Route::get('hold_requests', 'PaytmController@holdRequest');
Route::get('paytm_requests/{id}', 'PaytmController@show');
Route::put('paytm_requests/{id}', 'PaytmController@update');
Route::get('transactions', 'PaytmController@transactions');
Route::get('paidAmount', 'PaytmController@getPerDayAmount');

Route::get('pendingAmount', 'PaytmController@getPerDayPendingAmount');
Route::get('transactions/download', 'PaytmController@transactions');
Route::get('export', 'PaytmController@export')->name('export');
Route::get('settings/{id}', 'SettingController@edit');
Route::put('settings/{id}', 'SettingController@update');
Route::put('settings/bucket/{id}', 'SettingController@bucketUpdate');
Route::get('updateImage', 'PaytmController@updateImage');
Route::get('updateoldVersion', 'PaytmController@updateoldVersion');

Route::get('user_list', 'UserDetailController@user_list');
Route::resource('challenges','ChallengeController');
Route::get('chart', 'ChartController@index');
Route::resource('predictions', 'PredictionController');
Route::post('predictions/sentotp', 'PredictionController@sentotp');
Route::get('ajax-pagination','AjaxController@ajaxPagination');
Route::get('filter','AjaxController@filter' );
});

Route::get('/verifyemail/{id}', 'UserDetailController@verifyemail');

Route::get('/android-privacy', function(){
  return view('privacy.android');
});

Route::get('/ios-privacy', function(){
  return view('privacy.ios');
});

Route::get('/android-terms', function(){
  return view('terms.terms');
});

Route::get('/ios-terms', function(){
  return view('terms.iosterms');
});

Route::get('/lucky_coupon_cron', 'UserDetailController@lucky_coupon_cron');
Route::get('sendMail', 'UserDetailController@sendMail');

Auth::routes();

// \DB::listen(function($sql) {

// 	\Log::info('Queries:==');
//     \Log::info($sql->sql);
//     \Log::info($sql->time); //in milliseconds
//     \Log::info($sql->bindings);
// });

// Route::get('/home', 'HomeController@index')->name('home');

// Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');