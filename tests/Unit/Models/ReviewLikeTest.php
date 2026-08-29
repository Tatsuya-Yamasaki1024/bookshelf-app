<?php

namespace Tests\Unit\Models;

use App\Models\Review;
use App\Models\ReviewLike;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewLikeTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_like_belongs_to_review(): void
    {
        $review = Review::factory()->create();

        $reviewLike = ReviewLike::factory()->create([
            'review_id' => $review->id,
        ]);

        $this->assertTrue($reviewLike->review->is($review));
    }

    public function test_review_like_belongs_to_user(): void
    {
        $user = User::factory()->create();

        $reviewLike = ReviewLike::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($reviewLike->user->is($user));
    }
}
