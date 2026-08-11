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

        // 1. 吾輩は猫である：3件
        // レビュー1
        Review::create([
            'user_id' => $users[0]->id,
            'book_id' => $books[0]->id,
            'rating' => 5,
            'comment' => '猫の視点から描かれる独特の世界観が面白かったです。',
        ]);

        // レビュー2
        Review::create([
            'user_id' => $users[1]->id,
            'book_id' => $books[0]->id,
            'rating' => 4,
            'comment' => '夏目漱石らしいユーモアがあり、楽しく読めました。',
        ]);

        // レビュー3
        Review::create([
            'user_id' => $users[2]->id,
            'book_id' => $books[0]->id,
            'rating' => 4,
            'comment' => '昔の作品ですが、今読んでも楽しめる内容でした。',
        ]);

        // 2. 人を動かす：4件
        // レビュー4
        Review::create([
            'user_id' => $users[0]->id,
            'book_id' => $books[1]->id,
            'rating' => 5,
            'comment' => '人との接し方について多くのことを学べる一冊です。',
        ]);

        // レビュー5
        Review::create([
            'user_id' => $users[2]->id,
            'book_id' => $books[1]->id,
            'rating' => 5,
            'comment' => '家族の為にもう一冊購入しました。名著です。',
        ]);

        // レビュー6
        Review::create([
            'user_id' => $users[3]->id,
            'book_id' => $books[1]->id,
            'rating' => 4,
            'comment' => '仕事や日常の人間関係にも活かせる内容でした。',
        ]);

        Review::create([
            'user_id' => $users[4]->id,
            'book_id' => $books[1]->id,
            'rating' => 5,
            'comment' => '何度も読み返したくなる実践的な本だと思います。',
        ]);

        // 3. リーダブルコード：3件
        Review::create([
            'user_id' => $users[1]->id,
            'book_id' => $books[2]->id,
            'rating' => 5,
            'comment' => '読みやすいコードを書くための考え方が分かりやすかったです。',
        ]);

        Review::create([
            'user_id' => $users[2]->id,
            'book_id' => $books[2]->id,
            'rating' => 4,
            'comment' => 'プログラミング初心者にも参考になる内容でした。',
        ]);

        Review::create([
            'user_id' => $users[4]->id,
            'book_id' => $books[2]->id,
            'rating' => 5,
            'comment' => '実際のコードを書くときに役立つ考え方が多かったです。',
        ]);

        // 4. 7つの習慣：3件
        Review::create([
            'user_id' => $users[0]->id,
            'book_id' => $books[3]->id,
            'rating' => 5,
            'comment' => '自分の考え方や行動を見直すきっかけになりました。',
        ]);

        Review::create([
            'user_id' => $users[2]->id,
            'book_id' => $books[3]->id,
            'rating' => 4,
            'comment' => '仕事だけでなく普段の生活にも役立つ内容でした。',
        ]);

        Review::create([
            'user_id' => $users[3]->id,
            'book_id' => $books[3]->id,
            'rating' => 5,
            'comment' => '長く読み継がれている理由が分かる本でした。',
        ]);

        // 5. 坊っちゃん：3件
        Review::create([
            'user_id' => $users[1]->id,
            'book_id' => $books[4]->id,
            'rating' => 4,
            'comment' => '主人公のまっすぐな性格が印象に残りました。',
        ]);

        Review::create([
            'user_id' => $users[3]->id,
            'book_id' => $books[4]->id,
            'rating' => 5,
            'comment' => 'テンポが良く、最後まで楽しく読むことができました。',
        ]);

        Review::create([
            'user_id' => $users[4]->id,
            'book_id' => $books[4]->id,
            'rating' => 4,
            'comment' => '登場人物のやり取りが面白かったです。',
        ]);

        // 6. サピエンス全史：3件
        Review::create([
            'user_id' => $users[0]->id,
            'book_id' => $books[5]->id,
            'rating' => 5,
            'comment' => '人類の歴史を大きな視点から考えられる本でした。',
        ]);

        Review::create([
            'user_id' => $users[2]->id,
            'book_id' => $books[5]->id,
            'rating' => 4,
            'comment' => '歴史について新しい視点を得ることができました。',
        ]);

        Review::create([
            'user_id' => $users[4]->id,
            'book_id' => $books[5]->id,
            'rating' => 5,
            'comment' => '壮大なテーマですが、興味深く読み進められました。',
        ]);

        // 7. Clean Code：3件
        Review::create([
            'user_id' => $users[1]->id,
            'book_id' => $books[6]->id,
            'rating' => 5,
            'comment' => '保守しやすいコードを書くための基本を学べました。',
        ]);

        Review::create([
            'user_id' => $users[2]->id,
            'book_id' => $books[6]->id,
            'rating' => 4,
            'comment' => 'コードを書く際に意識したいポイントが多かったです。',
        ]);

        Review::create([
            'user_id' => $users[3]->id,
            'book_id' => $books[6]->id,
            'rating' => 5,
            'comment' => 'チーム開発でも役立つ考え方が多く参考になりました。',
        ]);

        // 8. 嫌われる勇気：3件
        Review::create([
            'user_id' => $users[0]->id,
            'book_id' => $books[7]->id,
            'rating' => 5,
            'comment' => '自分の生き方について考えるきっかけになりました。',
        ]);

        Review::create([
            'user_id' => $users[3]->id,
            'book_id' => $books[7]->id,
            'rating' => 4,
            'comment' => '対話形式なので読みやすく、内容も理解しやすかったです。',
        ]);

        Review::create([
            'user_id' => $users[4]->id,
            'book_id' => $books[7]->id,
            'rating' => 3,
            'comment' => '人間関係について考え方を変えるきっかけになりました。',
        ]);

        // 9. 火花：3件
        Review::create([
            'user_id' => $users[1]->id,
            'book_id' => $books[8]->id,
            'rating' => 5,
            'comment' => '芸人の世界をリアルに感じられる作品でした。',
        ]);

        Review::create([
            'user_id' => $users[2]->id,
            'book_id' => $books[8]->id,
            'rating' => 4,
            'comment' => '登場人物の生き方が印象に残りました。',
        ]);

        Review::create([
            'user_id' => $users[4]->id,
            'book_id' => $books[8]->id,
            'rating' => 5,
            'comment' => '最後まで引き込まれるストーリーでした。',
        ]);

        // 10. FACTFULNESS：2件
        Review::create([
            'user_id' => $users[0]->id,
            'book_id' => $books[9]->id,
            'rating' => 5,
            'comment' => 'データをもとに物事を見る大切さを学べました。',
        ]);

        Review::create([
            'user_id' => $users[2]->id,
            'book_id' => $books[9]->id,
            'rating' => 4,
            'comment' => '思い込みを見直すきっかけになる内容でした。',
        ]);

        // 11. コンテナ物語：2件
        Review::create([
            'user_id' => $users[1]->id,
            'book_id' => $books[10]->id,
            'rating' => 4,
            'comment' => 'コンテナが世界の物流を変えた歴史が興味深かったです。',
        ]);

        Review::create([
            'user_id' => $users[4]->id,
            'book_id' => $books[10]->id,
            'rating' => 5,
            'comment' => '物流の仕組みを歴史的な視点から知ることができました。',
        ]);
    }
}
