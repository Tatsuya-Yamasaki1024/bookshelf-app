<?php

namespace Tests\Feature\Authentication\ReviewLike;

use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewLikeAccessTest extends TestCase
{
    use RefreshDatabase;

    // ゲストはレビューにいいねしようとすると、ログイン画面へ遷移される
    public function test_guest_cannot_like_review(): void
    {
        $review = Review::factory()->create();

        $response = $this->post(
            route('reviews.like', $review),
        );

        $response->assertRedirect(route('login'));
    }
}
