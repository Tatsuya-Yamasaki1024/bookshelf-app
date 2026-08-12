<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Review;
use App\Models\Reviewlike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    // レビュー → 書籍
    public function test_review_belongs_to_book(): void
    {
        $book = Book::factory()->create();
        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);

        $this->assertTrue($review->book->is($book));
    }

    // レビュー → 投稿したユーザー
    public function test_review_belongs_to_user(): void
    {
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($review->user->is($user));
    }

    // レビュー → レビューいいね
    public function test_review_has_many_review_likes(): void
    {
        $review = Review::factory()->create();

        Reviewlike::factory()->count(2)->create([
            'review_id' => $review->id,
        ]);

        $this->assertCount(2, $review->fresh()->reviewLikes);
    }
}
