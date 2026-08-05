<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\GenreController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// 書籍一覧・詳細（ゲストアクセス可）
Route::resource('books', BookController::class)
    ->only(['index', 'show']);

// 認証必須
Route::middleware('auth')->group(function () {

    // 書籍登録・編集・削除
    Route::resource('books', BookController::class)
        ->except(['index', 'show']);

    // ジャンル管理
    Route::resource('genres', GenreController::class);
});
