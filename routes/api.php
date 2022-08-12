<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['middleware' => 'auth:sanctum'], function(){
    Route::group(['prefix' => 'book'], function(){
        Route::get('/', [\App\Http\Controllers\BookController::class, 'index']);
        Route::get('/all', [\App\Http\Controllers\BookController::class, 'all']);
        Route::post('/create', [\App\Http\Controllers\BookController::class, 'store']);
        Route::get('/{id}', [\App\Http\Controllers\BookController::class, 'show']);
        Route::delete('/delete', [\App\Http\Controllers\BookController::class, 'destroy']);
        Route::patch('/update/{id}', [\App\Http\Controllers\BookController::class, 'update']);
    });
    Route::group(['prefix' => 'transaction'], function(){
        Route::get('/', [\App\Http\Controllers\OrderController::class, 'lists']);
        Route::get('/{id}', [\App\Http\Controllers\OrderController::class, 'show']);
        Route::patch('/update_status/{id}', [\App\Http\Controllers\OrderController::class, 'update_status']);
        Route::post('/create', [\App\Http\Controllers\OrderController::class, 'store']);
        Route::post('/add_order', [\App\Http\Controllers\OrderController::class, 'add_order']);
    });
});
