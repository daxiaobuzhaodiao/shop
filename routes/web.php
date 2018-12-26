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

Route::get('/', 'ProductController@index')->name('root');
Route::resource('product', 'ProductController');

// 邮箱验证
Route::group(['middleware'=>'auth'], function(){
    Route::get('email_verification_notice', 'PageController@emailVerifyNotice')->name('email_verification_notice');    // 提醒用户去验证邮箱
    Route::get('email_verification/verify', 'PageController@verify')->name('email_verification.verify');   //验证url地址
    Route::get('email_verification/send', 'PageController@send')->name('email_verification.send'); //用户主动验证邮箱
    Route::group(['middleware' => 'email_verify'], function () {
        Route::resource('user_address', 'UserAddressController');
        Route::post('product/{product}/favorite', 'ProductController@favorite')->name('product.favorite');
        Route::delete('product/{product}/disfavorite', 'ProductController@disfavorite')->name('product.disfavorite');
        Route::get('product/favorites/list', 'ProductController@favorites')->name('product.favorites');
        Route::resource('cart', 'CartController');
        Route::resource('order', 'OrderController');
        Route::get('payment/{order}/alipay', 'PaymentController@payByAlipay')->name('payment.alipay');
        Route::get('payment/alipay/return', 'PaymentController@alipayReturn')->name('payment.alipay.return');
    });
});

// 服务器端回调的路由不能放到带有 auth 中间件的路由组中，因为支付宝的服务器请求不会带有认证信息
Route::post('payment/alipay/notify', 'PaymentController@alipayNotify')->name('payment.alipay.notify');





