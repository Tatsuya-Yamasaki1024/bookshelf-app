<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\Reviewlike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    // User → Book
    public function test_user_has_many_books(): void
    {
        $user = User::factory()->create();
        $book1 = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        Book::factory()->create();

        $this->assertCount(1, $user->fresh()->books);
        $this->assertTrue($user->books->first()->is($book1));

    }

    // User → Review
    public function test_user_has_many_reviews(): void
    {
        $user = User::factory()->create();
        $review1 = Review::factory()->create([
            'user_id' => $user->id,
        ]);
        Review::factory()->create();

        $this->assertCount(1, $user->fresh()->reviews);
        $this->assertTrue($user->reviews->first()->is($review1));

    }

    // User → Favorite
    public function test_user_has_many_favorites(): void
    {
        $user = User::factory()->create();

        $favorite1 = Favorite::factory()->create([
            'user_id' => $user->id,
        ]);

        Favorite::factory()->create();

        $this->assertCount(1, $user->fresh()->favorites);
        $this->assertTrue($user->favorites->first()->is($favorite1));
    }

    // User → ReviewLikes
    public function test_user_has_many_review_likes(): void
    {
        $user = User::factory()->create();

        Reviewlike::factory()->count(2)->create([
            'user_id' => $user->id,
        ]);

        $this->assertCount(2, $user->fresh()->reviewLikes);
    }

    // User → FavoriteBooks
    public function test_user_belongs_to_many_favorite_books(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $user->favoriteBooks()->attach($book);

        $this->assertCount(1, $user->fresh()->favoriteBooks);
    }
}
