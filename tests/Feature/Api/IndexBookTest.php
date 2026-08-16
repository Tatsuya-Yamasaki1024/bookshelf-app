<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexBookTest extends TestCase
{
    use RefreshDatabase;

    // GET /api/v1/booksで書籍一覧がJSON形式で返る
    public function test_books_are_returned_as_json()
    {
        $book = Book::factory()->create();
        $genre = Genre::factory()->create();

        $book->genres()->attach($genre);

        $response = $this->getJson('/api/v1/books');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'author',
                        'isbn',
                        'published_date',
                        'genres',
                        'average_rating',
                        'reviews_count',
                    ],
                ],
                'links',
                'meta',
            ]);
    }

    // keywordで書籍を検索できる
    public function test_books_can_be_searched_by_keyword()
    {
        $targetBook = Book::factory()->create([
            'title' => '吾輩は猫である',
            'author' => '夏目漱石',
        ]);

        Book::factory()->create([
            'title' => '人間失格',
            'author' => '太宰治',
        ]);

        $response = $this->getJson('/api/v1/books?keyword=猫');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $targetBook->id);
    }

    // genre_idで書籍を検索できる
    public function test_books_can_be_filtered_by_genre()
    {
        $targetGenre = Genre::factory()->create();
        $otherGenre = Genre::factory()->create();

        $targetBook = Book::factory()->create();
        $otherBook = Book::factory()->create();

        $targetBook->genres()->attach($targetGenre);
        $otherBook->genres()->attach($otherGenre);

        $response = $this->getJson(
            '/api/v1/books?genre_id='.$targetGenre->id
        );

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $targetBook->id);
    }

    // ページネーションが機能する
    public function test_books_are_paginated()
    {
        Book::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/books?per_page=2&page=2');

        $response->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.per_page', 2);
    }

    // genres、average_rating、reviews_countがレスポンスに含まれる
    public function test_response_contains_genres_average_rating_and_reviews_count()
    {
        $book = Book::factory()->create();
        $genre = Genre::factory()->create();

        $book->genres()->attach($genre);

        Review::factory()->create([
            'book_id' => $book->id,
            'rating' => 4,
        ]);

        Review::factory()->create([
            'book_id' => $book->id,
            'rating' => 5,
        ]);

        $response = $this->getJson('/api/v1/books');

        $response->assertOk()
            ->assertJsonPath('data.0.genres.0.id', $genre->id)
            ->assertJsonPath('data.0.average_rating', 4.5)
            ->assertJsonPath('data.0.reviews_count', 2);
    }

    // 正常なリクエストの場合、HTTPステータス200が返る
    public function test_successful_request_returns_200()
    {
        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200);
    }

    // バリデーションエラー時に422が返る
    public function test_validation_error_returns_422()
    {
        $response = $this->getJson('/api/v1/books?per_page=101');

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['per_page']);
    }
}
