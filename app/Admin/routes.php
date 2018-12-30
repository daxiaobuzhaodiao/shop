<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

// laravel-admin默认提供的原始路由 
// Route::group([
//     'prefix'        => config('admin.route.prefix'),
//     'namespace'     => config('admin.route.namespace'),
//     'middleware'    => config('admin.route.middleware'),
// ], function (Router $router) {

//     $router->get('/', 'HomeController@index');
    
// });

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');  
    $router->get('users', 'UsersController@index'); // 用户列表
    $router->get('products', 'ProductsController@index');   // 商品列表
    $router->get('products/create', 'ProductsController@create');   // 返回添加商品页面
    $router->post('products', 'ProductsController@store');  // 添加商品
    $router->get('products/{id}/edit', 'ProductsController@edit');  // 返回编辑商品页面
    $router->put('products/{id}', 'ProductsController@update'); // 修改商品
    $router->delete('products/{id}', 'ProductsController@destroy'); // 删除商品
    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');   // 订单列表
    $router->get('orders/{order}', 'OrdersController@show')->name('admin.orders.show'); // 订单详情
    $router->post('orders/{order}/ship', 'OrdersController@ship')->name('admin.orders.ship');   // 发货
    $router->post('orders/{order}/refund', 'OrdersController@handleRefund')->name('admin.orders.handle_refund'); // 处理退款申请
    $router->get('coupons', 'CouponCodesController@index')->name('coupons.index');

    $router->resource('coupons', 'CouponCodesController');
    // $router->get('coupons/create', 'CouponCodesController@create')->name('coupons.create');
    // $router->post('coupons', 'CouponCodesController@store')->name('coupons.store');
    // $router->get('coupons/{coupons}/edit', 'CouponCodesController@edit')->name('coupons.edit');
    // $router->put('coupons/{coupons}', 'CouponCodesController@update')->name('coupons.update');
    // $router->delete('coupons/{coupons}', 'CouponCodesController@destroy')->name('coupons.destroy');
});
