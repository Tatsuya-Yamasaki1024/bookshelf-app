<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreBookTest extends TestCase
{
    use RefreshDatabase;

    // POST /api/v1/booksで書籍が作成され、ジャンルが紐付く
    public function test_book_is_created_and_genres_are_attached()
    {
        $user = User::factory()->create();
        $genre1 = Genre::factory()->create();
        $genre2 = Genre::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => '吾輩は猫である',
            'author' => '夏目漱石',
            'isbn' => '9784101010014',
            'published_date' => '1905-01-01',
            'description' => '猫を主人公とした小説。',
            'image_url' => 'https://example.com/book.jpg',
            'genres' => [
                $genre1->id,
                $genre2->id,
            ],
        ];

        $response = $this->postJson('/api/v1/books', $data);


        $response->dump();
        $response->assertStatus(201);

        $this->assertDatabaseHas('books', [
            'user_id' => $user->id,
            'title' => '吾輩は猫である',
            'author' => '夏目漱石',
            'isbn' => '9784101010014',
            'published_date' => '1905-01-01',
        ]);

        $book = Book::where('isbn', '9784101010014')->first();

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre1->id,
        ]);

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre2->id,
        ]);
    }

    // 登録成功時にHTTPステータス201が返る
    public function test_successful_request_returns_201()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => '吾輩は猫である',
            'author' => '夏目漱石',
            'isbn' => '9784101010014',
            'published_date' => '1905-01-01',
            'description' => '猫を主人公とした小説。',
            'image_url' => 'https://example.com/book.jpg',
            'genres' => [$genre->id],
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(201);
    }

    // バリデーションエラー時にHTTPステータス422が返る
    public function test_validation_error_returns_422()
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $data = [
            'user_id' => $user->id,
            'title' => '',
            'author' => '夏目漱石',
            'isbn' => '9784101010014',
            'published_date' => '1905-01-01',
            'description' => '猫を主人公とした小説。',
            'image_url' => 'https://example.com/book.jpg',
            'genres' => [$genre->id],
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }
}