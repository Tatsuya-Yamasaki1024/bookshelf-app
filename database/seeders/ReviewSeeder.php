<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $books = Book::all();

        // コメントを評価別日本語テンプレート5段階に変更
        $comments = [
            5 => [
                '素晴らしい本でした！',
                '人生が変わりました。',
                '何度も読み返しています。',
            ],
            4 => [
                'とても参考になりました。',
                '読みやすくておすすめです。',
                '期待通りの内容でした。',
            ],
            3 => [
                '普通でした。',
                '可もなく不可もなく。',
                '期待したほどではなかった。',
            ],
            2 => [
                '少し期待外れでした。',
                '内容が薄い印象。',
                'もう少し深掘りしてほしかった。',
            ],
            1 => [
                '残念ながら合いませんでした。',
                '期待と違いました。',
            ],
        ];

        // 各書籍に2〜4件のレビューをランダムに作成
        $reviews = $books->flatMap(function ($book) use ($users, $comments) {
            $reviewCount = rand(2, 4);

            // 評価を1〜5、投稿者をランダムに設定
            return $users->random($reviewCount)->map(function ($user) use ($book, $comments) {
                $rating = rand(1, 5);

                return [
                    'user_id' => $user->id,
                    'book_id' => $book->id,
                    'rating' => $rating,
                    'comment' => fake()->randomElement($comments[$rating]),
                ];
            });
        });

        $reviews->each(fn ($review) => Review::create($review));
    }
}
