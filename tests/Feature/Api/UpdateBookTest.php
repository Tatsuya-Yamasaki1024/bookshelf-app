<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateBookTest extends TestCase
{
    use RefreshDatabase;

    // PUT /api/v1/books/{book}で書籍が更新され、200が返る(作成者本人)
    public function test_book_is_updated_and_returns_200()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $genre = Genre::factory()->create();

        $data = [
            'title' => '更新後のタイトル',
            'author' => '更新後の著者',
            'isbn' => '9784101010014',
            'published_date' => '2000-01-01 00:00:00',
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
            'published_date' => '2000-01-01 00:00:00',
            'description' => '更新後の説明',
            'image_url' => 'https://example.com/updated.jpg',
        ]);
    }

    // 存在しないIDの場合、404が返る
    public function test_nonexistent_book_returns_404()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/books/999999', [
            'title' => '更新後のタイトル',
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure([
                'error',
            ]);
    }

    // バリデーションエラー時に422が返る
    public function test_validation_error_returns_422()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

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

    // 作成者本人以外は書籍を更新できず、403が返る
    public function test_non_owner_cannot_update_book()
    {
        $owner = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $owner->id,
        ]);

        $otherUser = User::factory()->create();
        Sanctum::actingAs($otherUser);

        $genre = Genre::factory()->create();

        $response = $this->putJson(
            "/api/v1/books/{$book->id}",
            [
                'title' => '更新後のタイトル',
                'author' => '更新後の著者',
                'isbn' => '9784101010014',
                'published_date' => '2000-01-01',
                'description' => '更新後の説明',
                'image_url' => 'https://example.com/updated.jpg',
                'genres' => [$genre->id],
            ]
        );

        $response->assertStatus(403);
    }

    // ゲストは書籍を更新できず、401が返る
    public function test_guest_cannot_update_book()
    {
        $owner = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $owner->id,
        ]);

        $response = $this->putJson(
            "/api/v1/books/{$book->id}",
            [
                'title' => '更新後のタイトル',
            ]
        );

        $response->assertStatus(401);
    }
}
