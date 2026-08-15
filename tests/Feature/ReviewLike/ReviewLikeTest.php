<?php

namespace Tests\Feature\ReviewLike;

use App\Models\Book;
use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewLikeTest extends TestCase
{
    use RefreshDatabase;

    // ログインユーザーがレビューにいいねでき、いいね数が1増えている
    public function test_authenticated_user_can_like_review_and_like_count_increases_by_one(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);

        // いいね前のいいね数を確認
        $this->assertSame(
            0,
            ReviewLike::where('review_id', $review->id)->count()
        );

        // レビューにいいね
        $response = $this->actingAs($user)->post(
            route('reviews.like', $review),
        );

        // いいね後に1件増えていることを確認
        $this->assertSame(
            1,
            ReviewLike::where('review_id', $review->id)->count()
        );
    }

    // ログインユーザーが自身のいいねを解除でき、いいね数が1減少する
    public function test_authenticated_user_can_unlike_review_and_like_count_decreases_by_one(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $review = Review::factory()->create([
            'book_id' => $book->id,
        ]);

        // あらかじめいいねしておく
        ReviewLike::create([
            'user_id' => $user->id,
            'review_id' => $review->id,
        ]);

        // いいね解除前のいいね数を確認
        $this->assertSame(
            1,
            ReviewLike::where('review_id', $review->id)->count()
        );

        // レビューのいいねを解除
        $response = $this->actingAs($user)->post(
            route('reviews.like', $review),
        );

        // いいね解除後に1件減っていることを確認
        $this->assertSame(
            0,
            ReviewLike::where('review_id', $review->id)->count()
        );
    }
}
