<?php

use App\Http\Controllers\Api\SocialLoginController;
use Illuminate\Support\Facades\Route;

/*Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__.'/auth.php';*/


/**
 * FOR_SOCIAL_LOGIN
 * http://localhost:8000/auth/login/kakao
 * http://localhost:8000/auth/callback/kakao
 */
Route::group(['prefix' => 'auth', 'middleware' => 'guest'], function () {
    Route::group(['controller' => SocialLoginController::class], function () {
        Route::get('login/{service}', 'login');
        Route::get('callback/{service}', 'callback');
    });
});


//FOR_TEST
if (app()->environment()) {
    require __DIR__ . '/test.php';
}
