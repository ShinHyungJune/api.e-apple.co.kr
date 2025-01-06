<?php

use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'admin'], function () {


    Route::group(['prefix' => 'auth', 'controller' => AuthController::class], function () {
        /**
         * FOR JWT
         */
        Route::middleware('auth:api')->get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('login', 'login');
    });


    Route::group(['middleware' => 'admin'], function () {

        Route::group(['prefix' => 'users', 'controller' => UserController::class],
            function () {
                Route::get('', 'index');
            });

        Route::group(['prefix' => 'products', 'controller' => ProductController::class],
            function () {
                Route::get('', 'index');
                Route::post('', 'store');
                /*Route::put('{product}', 'update');
                Route::get('{product}', 'show');
                Route::delete('{product}', 'destroy');*/
            });

        Route::group(['prefix' => 'orders', 'controller' => OrderController::class],
            function () {
                Route::post('test', 'test');
            });
    });

});
