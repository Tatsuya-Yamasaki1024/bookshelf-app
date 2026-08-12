<?php

namespace Tests\Feature\ScreenAccess;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScreenAccessTest extends TestCase
{
    use RefreshDatabase;

    // ゲストがログイン画面にアクセスできる
    public function test_guest_can_access_login_page(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    // ゲストが書籍詳細画面にアクセスできる
    public function test_guest_can_access_book_detail_page(): void
    {
        $book = Book::factory()->create();

        $response = $this->get("/books/{$book->id}");
        $response->assertStatus(200);
    }

    // ログインユーザーが書籍登録画面にアクセスできる
    public function test_authenticated_user_can_access_book_create_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/books/create');
        $response->assertStatus(200);
    }

    // ログインユーザーが自身の登録した書籍編集画面にアクセスできる
    public function test_authenticated_user_can_access_own_book_edit_page(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get("/books/{$book->id}/edit");
        $response->assertStatus(200);
    }

    // ログインユーザーが自身のレビューの編集画面にアクセスできる
    public function test_authenticated_user_can_access_own_review_edit_page(): void
    {
        $user = User::factory()->create();
        $review = Review::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get("/reviews/{$review->id}/edit");
        $response->assertStatus(200);
    }

    // ログインユーザーがジャンル一覧画面にアクセスできる
    public function test_authenticated_user_can_access_genre_index_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/genres');
        $response->assertStatus(200);
    }

    // ログインユーザーがジャンル登録画面にアクセスできる
    public function test_authenticated_user_can_access_genre_create_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/genres/create');
        $response->assertStatus(200);
    }

    // ログインユーザーがジャンル編集画面にアクセスできる
    public function test_authenticated_user_can_access_genre_edit_page(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->get("/genres/{$genre->id}/edit");
        $response->assertStatus(200);
    }

    // ログインユーザーがログアウトを実行し、認証が解除され、ログイン画面へリダイレクトされる
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}
