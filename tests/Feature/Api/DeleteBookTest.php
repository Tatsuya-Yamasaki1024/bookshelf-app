<?php

namespace Tests\Feature\Api;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteBookTest extends TestCase
{
    use RefreshDatabase;

    // DELETE /api/v1/books/{book}で書籍と関連データが削除され、204が返る
    public function test_book_is_deleted_with_related_data_and_returns_204()
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $genre = Genre::factory()->create();
        $book->genres()->attach($genre);

        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        $favorite = Favorite::create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson(
            "/api/v1/books/{$book->id}"
        );

        $response->assertStatus(204);

        // booksが削除されている
        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);

        // reviewsが削除されている
        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);

        // favoritesが削除されている
        $this->assertDatabaseMissing('favorites', [
            'id' => $favorite->id,
        ]);

        // book_genreの紐付けが削除されている
        $this->assertDatabaseMissing('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);
    }

    // 存在しないIDの場合、404が返る
    public function test_nonexistent_book_returns_404()
    {
        $response = $this->deleteJson('/api/v1/books/999999');

        $response->assertStatus(404)
            ->assertJsonStructure([
                'message',
            ]);
    }
}
