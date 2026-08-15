<?php

namespace Tests\Feature\Authentication\Review;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewAccessTest extends TestCase
{
    use RefreshDatabase;

    // ログインユーザーが他人のレビューを編集しようとすると拒否される
    public function test_authenticated_user_cannot_edit_another_users_review(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $book->genres()->attach($genre);

        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->get(
            route('reviews.edit', $review),
        );

        $response->assertForbidden();
    }

    // ログインユーザーが他人のレビューを削除しようとすると拒否される
    public function test_authenticated_user_cannot_delete_another_users_review(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $book->genres()->attach($genre);

        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->delete(
            route('reviews.destroy', $review),
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
        ]);
    }
}
