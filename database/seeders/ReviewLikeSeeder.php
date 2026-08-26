<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = Review::with('user')->get();
        $users = User::all();

        // レビュー投稿者自身を除外していいね
        $reviews->each(function ($review) use ($users) {
            $availableUsers = $users->reject(
                fn ($user) => $user->id === $review->user_id
            );

            $likeCount = rand(0, min(3, $availableUsers->count()));

            $likedUserIds = $availableUsers
                ->random($likeCount)
                ->pluck('id');

            $review->likedByUsers()->syncWithoutDetaching($likedUserIds);
        });
    }
}
