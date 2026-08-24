<?php

use App\Http\Controllers\Api\V1\BookController;
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

Route::apiResource('v1/books', BookController::class)
    ->except(['store', 'update', 'destroy']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/v1/books', [BookController::class, 'store']);
    Route::put('/v1/books/{book}', [BookController::class, 'update']);
    Route::delete('/v1/books/{book}', [BookController::class, 'destroy']);
});
