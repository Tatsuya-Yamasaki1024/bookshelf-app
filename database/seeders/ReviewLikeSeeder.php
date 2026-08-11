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
        $reviews = Review::orderBy('id')->get();
        $users = User::orderBy('id')->get();

        // レビュー1：山田太郎(users[0])のレビューに2人がいいね
        $reviews[0]->likedByUsers()->syncWithoutDetaching([
            $users[1]->id,
            $users[2]->id,
        ]);

        // レビュー2：鈴木花子(users[1])のレビューに3人がいいね
        $reviews[1]->likedByUsers()->syncWithoutDetaching([
            $users[0]->id,
            $users[2]->id,
            $users[3]->id,
        ]);

        // レビュー3：田中一郎(users[2])のレビューに1人がいいね
        $reviews[2]->likedByUsers()->syncWithoutDetaching([
            $users[0]->id,
        ]);

        // レビュー4：山田太郎(users[0])のレビューに2人がいいね
        $reviews[3]->likedByUsers()->syncWithoutDetaching([
            $users[1]->id,
            $users[3]->id,
        ]);

        // レビュー5：0人
        // 何もしない

        // レビュー6：田中一郎(users[2])のレビューに3人がいいね
        $reviews[5]->likedByUsers()->syncWithoutDetaching([
            $users[0]->id,
            $users[1]->id,
            $users[4]->id,
        ]);

        // レビュー7~31：0人がいいね
        // 何もしない

        // レビュー32：高橋健太(users[4])のレビューに3人がいいね
        $reviews[31]->likedByUsers()->syncWithoutDetaching([
            $users[0]->id,
            $users[1]->id,
            $users[2]->id,
        ]);
    }
}
