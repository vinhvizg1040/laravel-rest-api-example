<?php

use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::prefix('api')->group(function () {
    Route::get('products', 'App\Http\Controllers\ProductController@index');
    Route::get('products/{id}', 'App\Http\Controllers\ProductController@show');
    Route::post('products', 'App\Http\Controllers\ProductController@store');
    Route::put('products/{id}', 'App\Http\Controllers\ProductController@update');
    Route::delete('products/{id}', 'App\Http\Controllers\ProductController@destroy');
// });
