<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ReviewLikeController;
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
// 認証必須
Route::middleware('auth')->group(function () {

    // 書籍登録・編集・削除
    Route::resource('books', BookController::class)
        ->except(['index', 'show']);

    // ジャンル管理
    Route::resource('genres', GenreController::class);

    // レビュー投稿・編集
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store'])
        ->name('reviews.store');

    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])
        ->name('reviews.edit');

    Route::put('/reviews/{review}', [ReviewController::class, 'update'])
        ->name('reviews.update');

    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])
        ->name('reviews.destroy');

    // お気に入り
    Route::get('/favorites', [FavoriteController::class, 'index'])
        ->name('favorites.index');

    Route::post('/books/{book}/favorites', [FavoriteController::class, 'store'])
        ->name('favorites.store');

    Route::delete('/books/{book}/favorites', [FavoriteController::class, 'destroy'])
        ->name('favorites.destroy');

    // レビューいいね
    Route::post('/reviews/{review}/like', [ReviewLikeController::class, 'store'])
        ->name('review_likes.store');

    Route::delete('/reviews/{review}/like', [ReviewLikeController::class, 'destroy'])
        ->name('review_likes.destroy');
});

// 書籍一覧・詳細（ゲストアクセス可）
Route::resource('books', BookController::class)
    ->only(['index', 'show']);

// ランキング
Route::get('/ranking', [RankingController::class, 'index'])
    ->name('ranking.index');
