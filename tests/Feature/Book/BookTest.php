<?php

namespace Tests\Feature\Book;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    // ゲストが書籍一覧画面にアクセスでき、登録された書籍が10件ごとにページネートされている
    public function test_guest_can_access_book_index_page_with_pagination(): void
    {
        Book::factory()->count(13)->create();

        $response = $this->get('/books');

        $response->assertStatus(200);
        $response->assertViewHas('books', function ($books) {
            return $books->count() === 10
                && $books->total() === 13;
        });

        $response = $this->get('/books?page=2');
        $response->assertViewHas('books', function ($books) {
            return $books->count() === 3
                && $books->total() === 13;
        });
    }

    // ログインユーザーが書籍を登録でき、登録したユーザーのuser_idが設定され、書籍一覧に登録した書籍が追加される
    public function test_authenticated_user_can_create_book(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $response = $this->actingAs($user)->post('/books', [
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'published_date' => '2026-01-01',
            'description' => 'テスト説明',
            'image_url' => 'https://example.com/image.jpg',
            'genres' => [$genre->id],
        ]);

        $response->assertRedirect(route('books.index'));
        $this->assertDatabaseHas('books', [
            'user_id' => $user->id,
            'title' => 'テスト書籍',
        ]);

        $response = $this->actingAs($user)->get(
            route('books.index')
        );
        $response->assertSee('テスト書籍');
    }

    // ログインユーザーが自身の登録した書籍を編集でき、書籍詳細画面に変更内容が反映される
    public function test_authenticated_user_can_update_own_book(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $book->genres()->attach($genre);

        $response = $this->actingAs($user)->put(
            route('books.update', $book),
            [
                'title' => '更新後のテスト書籍',
                'author' => '更新後のテスト著者',
                'isbn' => '1234567890123',
                'published_date' => '2026-01-01',
                'description' => '更新後のテスト説明',
                'image_url' => 'https://example.com/image.jpg',
                'genres' => [$genre->id],
            ]
        );

        $response->assertRedirect(route('books.show', $book));

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'user_id' => $user->id,
            'title' => '更新後のテスト書籍',
        ]);

        $response = $this->actingAs($user)->get(
            route('books.show', $book)
        );

        $response->assertSee('更新後のテスト書籍');
        $response->assertSee('更新後のテスト著者');
        $response->assertSee('更新後のテスト説明');
    }

    // ログインユーザーが自身の登録した書籍を削除でき、書籍・レビュー・お気に入りから削除される
    public function test_authenticated_user_can_delete_own_book(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $book->genres()->attach($genre);

        Review::factory()->create([
            'book_id' => $book->id,
        ]);

        Favorite::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(
            route('books.destroy', $book),
        );

        $response->assertRedirect(route('books.index'));

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);

        $this->assertDatabaseMissing('reviews', [
            'book_id' => $book->id,
        ]);

        $this->assertDatabaseMissing('favorites', [
            'book_id' => $book->id,
        ]);

        $this->assertDatabaseMissing('book_genre', [
            'book_id' => $book->id,
        ]);
    }
}
