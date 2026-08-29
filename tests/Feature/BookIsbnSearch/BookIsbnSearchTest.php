<?php

namespace Tests\Feature\BookIsbnSearch;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BookIsbnSearchTest extends TestCase
{
    use RefreshDatabase;

    // 一致する書籍があれば情報を取得して、取得した情報がフォームに入力される
    public function test_isbn_search_returns_book_information_when_book_exists(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([
                'totalItems' => 1,
                'items' => [
                    [
                        'volumeInfo' => [
                            'title' => 'テスト書籍',
                            'authors' => ['テスト著者'],
                            'publishedDate' => '2025-01-01',
                            'description' => 'テスト用の書籍です。',
                            'imageLinks' => [
                                'thumbnail' => 'https://example.com/test.jpg',
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->getJson('/books/isbn/9781234567890');

        $response->assertOk()
            ->assertJson([
                'title' => 'テスト書籍',
                'author' => 'テスト著者',
                'published_date' => '2025-01-01',
                'description' => 'テスト用の書籍です。',
                'image_url' => 'https://example.com/test.jpg',
                'isbn' => '9781234567890',
            ]);
    }

    // 一致する書籍がない場合「書籍情報が見つかりませんでした」
    public function test_isbn_search_returns_not_found_when_book_does_not_exist(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([
                'totalItems' => 0,
                'items' => [],
            ], 200),
        ]);

        $response = $this->getJson('/books/isbn/9781234567890');

        $response->assertNotFound()
            ->assertJson([
                'error' => '書籍情報が見つかりませんでした。',
            ]);
    }

    // 数字が13桁でない場合「ISBNは13桁で入力してください」
    public function test_isbn_search_rejects_isbn_that_is_not_13_digits(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->getJson('/books/isbn/123456789');

        $response->assertStatus(422);
    }

    // 通信が失敗した場合「通信エラーが発生しました」
    public function test_isbn_search_returns_503_when_api_request_fails(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([], 500),
        ]);

        $response = $this->getJson('/books/isbn/9781234567890');

        $response->assertStatus(503)
            ->assertJson([
                'error' => 'Google Books APIとの通信に失敗しました。',
            ]);
    }

    // APIの利用上限を超過した場合「Google Books APIの利用上限に達しています」
    public function test_isbn_search_returns_503_when_api_quota_is_exceeded(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Http::fake([
            'https://www.googleapis.com/books/v1/volumes*' => Http::response([], 429),
        ]);

        $response = $this->getJson('/books/isbn/9781234567890');

        $response->assertStatus(503)
            ->assertJson([
                'error' => 'Google Books APIの利用上限に達しています。',
            ]);
    }
}
