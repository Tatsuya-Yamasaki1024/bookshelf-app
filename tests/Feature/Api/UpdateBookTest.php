<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateBookTest extends TestCase
{
    use RefreshDatabase;

    // PUT /api/v1/books/{book}で書籍が更新され、200が返る
    public function test_book_is_updated_and_returns_200()
    {
        $book = Book::factory()->create();

        $genre = Genre::factory()->create();

        $data = [
            'title' => '更新後のタイトル',
            'author' => '更新後の著者',
            'isbn' => '9784101010014',
            'published_date' => '2000-01-01',
            'description' => '更新後の説明',
            'image_url' => 'https://example.com/updated.jpg',
            'genres' => [$genre->id],
        ];

        $response = $this->putJson(
            "/api/v1/books/{$book->id}",
            $data
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => '更新後のタイトル',
            'author' => '更新後の著者',
            'isbn' => '9784101010014',
            'published_date' => '2000-01-01',
            'description' => '更新後の説明',
            'image_url' => 'https://example.com/updated.jpg',
        ]);
    }

    // 存在しないIDの場合、404が返る
    public function test_nonexistent_book_returns_404()
    {
        $response = $this->putJson('/api/v1/books/999999', [
            'title' => '更新後のタイトル',
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }

    // バリデーションエラー時に422が返る
    public function test_validation_error_returns_422()
    {
        $book = Book::factory()->create();

        $response = $this->putJson(
            "/api/v1/books/{$book->id}",
            [
                'title' => '',
                'author' => '更新後の著者',
                'isbn' => '9784101010014',
                'published_date' => '2000-01-01',
                'description' => '更新後の説明',
                'image_url' => 'https://example.com/updated.jpg',
                'genres' => [],
            ]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'title',
                'genres',
            ]);
    }
}
