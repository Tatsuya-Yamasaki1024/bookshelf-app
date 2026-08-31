<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    // GET /api/v1/books/{book}で書籍詳細がJSON形式で返る
    public function test_book_detail_is_returned_as_json()
    {
        $book = Book::factory()->create();
        $genre = Genre::factory()->create();

        $book->genres()->attach($genre);

        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'author',
                    'isbn',
                    'published_date',
                    'genres',
                    'average_rating',
                    'reviews_count',
                ],
            ]);
    }

    // 成功時にHTTPステータス200が返る
    public function test_successful_request_returns_200()
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200);
    }

    // 平均評価、レビューが正しく返る
    public function test_successful_request_returns_book_with_average_rating_and_reviews()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $book = Book::factory()->create();

        Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user1->id,
            'rating' => 4,
            'comment' => '面白い本でした。',
        ]);

        Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user2->id,
            'rating' => 5,
            'comment' => 'とても良かったです。',
        ]);

        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $book->id)
            ->assertJsonPath('data.average_rating', 4.5)
            ->assertJsonCount(2, 'data.reviews');

        $response->assertJsonFragment([
            'rating' => 4,
            'comment' => '面白い本でした。',
        ]);

        $response->assertJsonFragment([
            'rating' => 5,
            'comment' => 'とても良かったです。',
        ]);
    }

    // 存在しないIDの場合、404のJSONが返る
    public function test_nonexistent_book_returns_404_json()
    {
        $response = $this->getJson('/api/v1/books/999999');

        $response->assertStatus(404)
            ->assertJsonStructure([
                'error',
            ]);
    }
}
