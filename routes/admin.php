<?php

use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\ProductController;
use Illuminate\Support\Facades\Route;


Route::middleware("admin")->prefix("admin")->group(function () {

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
