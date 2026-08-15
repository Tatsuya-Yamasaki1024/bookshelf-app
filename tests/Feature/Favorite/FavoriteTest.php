<?php

namespace Tests\Feature\Favorite;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    // ログインユーザーがお気に入り一覧を表示でき、自身のお気に入り書籍のみが10件ごとに表示される
    public function test_authenticated_user_can_view_favorites_and_only_own_favorites_are_paginated(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $books = Book::factory()->count(15)->create();

        // ログインユーザーが15冊のうち12冊をお気に入りに登録
        $favoriteBooks = $books->take(12);
        foreach ($favoriteBooks as $book) {
            Favorite::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
            ]);
        }

        // お気に入りされていない書籍が3冊あることを確認
        $this->assertSame(
            3,
            Book::doesntHave('favorites')->count()
        );

        // 他ユーザーがお気に入りに登録した書籍を作成
        $otherBook = Book::factory()->create();
        Favorite::create([
            'user_id' => $otherUser->id,
            'book_id' => $otherBook->id,
        ]);

        // お気に入り一覧画面にアクセス
        $response = $this->actingAs($user)->get(
            route('favorites.index'),
        );

        $response->assertStatus(200);

        // 1ページ目に10件表示され、全12件が対象であることを確認
        $response->assertViewHas('books', function ($favorites) {
            return $favorites->count() === 10
                && $favorites->total() === 12;
        });

        // 自身のお気に入り書籍が表示されることを確認
        $response->assertSee($favoriteBooks->first()->title);

        // 他ユーザーのお気に入り書籍が表示されないことを確認
        $response->assertDontSee($otherBook->title);

        // 2ページ目にアクセス
        $response = $this->actingAs($user)->get(
            route('favorites.index').'?page=2',
        );

        // 2ページ目に残り2件が表示されることを確認
        $response->assertViewHas('books', function ($favorites) {
            return $favorites->count() === 2
                && $favorites->total() === 12;
        });
    }

    // ログインユーザーが書籍をお気に入りでき、お気に入りした本がお気に入り一覧に加わっている
    public function test_authenticated_user_can_favorite_book_and_book_appears_in_favorites(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        // お気に入り登録前のお気に入り一覧を確認
        $response = $this->actingAs($user)->get(
            route('favorites.index'),
        );

        $response->assertViewHas('books', function ($books) {
            return $books->count() === 0
                && $books->total() === 0;
        });

        // 書籍をお気に入り登録
        $response = $this->actingAs($user)->post(
            route('favorites.toggle', $book),
        );

        // お気に入り登録後のお気に入り一覧を確認
        $response = $this->actingAs($user)->get(
            route('favorites.index'),
        );

        $response->assertViewHas('books', function ($books) {
            return $books->count() === 1
                && $books->total() === 1;
        });

        // お気に入りした書籍が一覧に表示されることを確認
        $response->assertSee($book->title);
    }

    // ログインユーザーが書籍をお気に入り解除でき、お気に入り解除した本がお気に入り一覧から削除されている
    public function test_authenticated_user_can_unfavorite_book_and_book_is_removed_from_favorites(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        // 書籍をお気に入りに登録しておく
        Favorite::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        // お気に入り解除前のお気に入り一覧を確認
        $response = $this->actingAs($user)->get(
            route('favorites.index'),
        );

        $response->assertViewHas('books', function ($books) {
            return $books->count() === 1
                && $books->total() === 1;
        });

        // お気に入りを解除
        $response = $this->actingAs($user)->post(
            route('favorites.toggle', $book),
        );

        // お気に入り解除後のお気に入り一覧を確認
        $response = $this->actingAs($user)->get(
            route('favorites.index'),
        );

        $response->assertViewHas('books', function ($books) {
            return $books->count() === 0
                && $books->total() === 0;
        });

        // お気に入り解除した書籍が一覧に表示されないことを確認
        $response->assertDontSee($book->title);
    }
}
