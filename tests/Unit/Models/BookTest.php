<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Genre;
use App\Models\ReadingPlan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    // Book → Genre
    public function test_book_belongs_to_many_genres(): void
    {
        $genre = Genre::factory()->create();

        $book = Book::factory()
            ->hasAttached($genre)
            ->create();

        $this->assertCount(1, $book->fresh()->genres);
        $this->assertTrue($book->genres->first()->is($genre));
    }

    // Book → User
    public function test_book_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $this->assertTrue($book->user->is($user));
    }

    // Book → Review
    public function test_book_has_many_reviews(): void
    {
        $book1 = Book::factory()->create();
        Review::factory()->count(2)->create([
            'book_id' => $book1->id,
        ]);

        $book2 = Book::factory()->create();
        Review::factory()->create([
            'book_id' => $book2->id,
        ]);

        $this->assertCount(2, $book1->fresh()->reviews);
    }

    // Book → Favorite
    public function test_book_has_many_favorites(): void
    {
        $book1 = Book::factory()->create();
        Favorite::factory()->count(2)->create([
            'book_id' => $book1->id,
        ]);

        $book2 = Book::factory()->create();
        Favorite::factory()->create([
            'book_id' => $book2->id,
        ]);

        $this->assertCount(2, $book1->fresh()->favorites);
    }

    // Book → FavoritedUser
    public function test_book_belongs_to_many_favorited_users(): void
    {
        $book = Book::factory()->create();

        $user1 = User::factory()->create();
        User::factory()->create();

        $book->favorites()->create([
            'user_id' => $user1->id,
        ]);

        $this->assertCount(1, $book->fresh()->favoritedByUsers);
        $this->assertTrue(
            $book->fresh()->favoritedByUsers->first()->is($user1)
        );
    }

    // Book→ReadingPlan
    public function test_book_has_many_reading_plans(): void
    {
        $book = Book::factory()->create();

        ReadingPlan::factory()->count(2)->create([
            'book_id' => $book->id,
        ]);

        $this->assertCount(2, $book->fresh()->readingPlans);
    }
}
