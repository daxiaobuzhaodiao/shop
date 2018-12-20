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

Auth::routes();

Route::redirect('/', '/products')->name('root');
Route::get('products', 'ProductsController@index')->name('products.index');

// 邮箱验证
Route::group(['middleware'=>'auth'], function(){
    Route::get('email_verify_notice', 'PagesController@emailVerifyNotice')->name('email_verify_notice');    // 提醒用户去验证邮箱
    Route::get('email_verification/verify', 'EmailVerificationController@verify')->name('email_verification.verify');   //验证url地址
    Route::get('email_verification/send', 'PagesController@send')->name('email_verification.send'); //用户主动验证邮箱
    Route::group(['middleware' => 'email_verify'], function () {
        Route::resource('user_address', 'UserAddressController');
    });
});




