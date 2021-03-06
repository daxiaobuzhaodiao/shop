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

Auth::routes(['verify' => true]);     // 邮箱验证的中间件

// Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index'); // 这个暂时不需要了！使用了laravel-admin的log插件

Route::get('/', 'ProductController@index')->name('root');
Route::resource('product', 'ProductController');

// 邮箱验证
Route::group(['middleware'=>['auth', 'verified']], function(){
    Route::resource('user_address', 'UserAddressController');
    Route::post('product/{product}/favorite', 'ProductController@favorite')->name('product.favorite'); // 收藏
    Route::delete('product/{product}/disfavorite', 'ProductController@disfavorite')->name('product.disfavorite');   // 取消收藏
    Route::get('product/favorites/list', 'ProductController@favorites')->name('product.favorites'); // 收藏列表
    Route::resource('cart', 'CartController');
    Route::post('order/{order}/apply_refund', 'OrderController@applyRefund')->name('order.apply_refund');
    Route::get('order/{order}/review', 'OrderController@review')->name('order.review.show');    // 评价页面
    Route::post('order/{order}/review', 'OrderController@sendReview')->name('order.review.store');  // 评价
    Route::post('order/{order}/received', 'OrderController@received')->name('order.received');//确认收获
    Route::resource('order', 'OrderController');
    Route::get('payment/{order}/alipay', 'PaymentController@payByAlipay')->name('payment.alipay');  //使用支付宝付款
    Route::get('payment/alipay/return', 'PaymentController@alipayReturn')->name('payment.alipay.return');//支付宝前端回调地址
    Route::get('coupon/{coupon}', 'CouponController@check')->name('coupon.check');
});

// 服务器端回调的路由不能放到带有 auth 中间件的路由组中，因为支付宝的服务器请求不会带有认证信息
Route::post('payment/alipay/notify', 'PaymentController@alipayNotify')->name('payment.alipay.notify'); // 支付宝服务器回调地址





