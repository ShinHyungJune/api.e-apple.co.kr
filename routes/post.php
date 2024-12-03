<?php

use App\Http\Controllers\Api\Post\PostController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'boards'], function () {
    Route::get('{id}/init', [PostController::class, 'init']);
    Route::get('{id}', [PostController::class, 'index']);
});

Route::group(['prefix' => 'posts'], function () {
    Route::post('', [PostController::class, 'store']);
    /*Route::get('{id}', [PostController::class, 'show']);
    Route::put('{id}', [PostController::class, 'update']);
    Route::delete('{id}', [PostController::class, 'destroy']);*/

    /*Route::group(['prefix' => '{postId}/comments'], function () {
        Route::post('', [PostCommentController::class, 'store']);
        Route::delete('{id}', [PostCommentController::class, 'destroy']);
    });*/

    /*Route::group(['prefix' => 'likes/{likeableType}/{id}/{type}'], function () {
        Route::post('', [PostLikeController::class, 'store']);
    });*/

    /*Route::group(['prefix' => 'files'], function () {
        Route::get('{fileId}', [PostController::class, 'showFile'])->where(['fileId' => '[0-9]+']);
        Route::delete('{fileId}', [PostController::class, 'destroyFile'])->where(['fileId' => '[0-9]+']);
    });*/

    /*Route::get('{boardId}/create/{id?}', [PostController::class, 'create'])->where(['boardId' => '[0-9]+']);
    Route::post('{boardId}/{id}', [PostController::class, 'update']);
    Route::get('{boardId}/files/{fileId}', [PostController::class, 'showFile'])->where(['fileId' => '[0-9]+']);
    Route::delete('{boardId}/files/{fileId}', [PostController::class, 'destroyFile'])->where(['fileId' => '[0-9]+']);
    Route::post('file', [PostController::class, 'storeFile']); */
});


/*Route::group(['prefix' => 'stories'], function () {
    //Route::get('', 'index');
    Route::get('', fn(Request $request) => (new PostController(1))->index($request));
    Route::get('{post}', fn(Post $post) => (new PostController(1))->show($post));
});*/

