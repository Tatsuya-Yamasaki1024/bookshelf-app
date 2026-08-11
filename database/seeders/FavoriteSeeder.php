<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        // 山田太郎：5冊
        $users[0]->favoriteBooks()->syncWithoutDetaching([
            $books[0]->id,
            $books[1]->id,
            $books[2]->id,
            $books[5]->id,
            $books[8]->id,
        ]);

        // 鈴木花子：3冊
        $users[1]->favoriteBooks()->syncWithoutDetaching([
            $books[4]->id,
            $books[6]->id,
            $books[9]->id,
        ]);

        // 田中一郎：4冊
        $users[2]->favoriteBooks()->syncWithoutDetaching([
            $books[0]->id,
            $books[3]->id,
            $books[7]->id,
            $books[10]->id,
        ]);

        // 佐藤美咲：4冊
        $users[3]->favoriteBooks()->syncWithoutDetaching([
            $books[1]->id,
            $books[5]->id,
            $books[8]->id,
            $books[10]->id,
        ]);

        // 高橋健太：4冊
        $users[4]->favoriteBooks()->syncWithoutDetaching([
            $books[2]->id,
            $books[3]->id,
            $books[6]->id,
            $books[9]->id,
        ]);
    }
}
