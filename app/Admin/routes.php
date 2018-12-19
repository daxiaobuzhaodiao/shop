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
    $router->get('users', 'UsersController@index');
    $router->get('products', 'ProductsController@index');
});
