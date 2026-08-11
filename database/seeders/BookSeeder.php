<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        // 1. 吾輩は猫である
        $book = Book::firstOrCreate(
            ['isbn' => '9784101010014'],
            [
                'user_id' => $user->id,
                'title' => '吾輩は猫である',
                'author' => '夏目漱石',
                'published_date' => '1905-01-01',
                'description' => '夏目漱石による有名小説。',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=1',
            ]
        );

        $book->genres()->sync(
            Genre::whereIn('name', ['小説'])->pluck('id')
        );

        // 2. 人を動かす
        $book = Book::firstOrCreate(
            ['isbn' => '9784422100524'],
            [
                'user_id' => $user->id,
                'title' => '人を動かす',
                'author' => 'D・カーネギー',
                'published_date' => '1936-10-01',
                'description' => 'カーネギーによる代表的な自己啓発書。',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=2',
            ]
        );

        $book->genres()->sync(
            Genre::whereIn('name', ['ビジネス', '自己啓発'])->pluck('id')
        );

        // 3. リーダブルコード
        $book = Book::firstOrCreate(
            ['isbn' => '9784873115658'],
            [
                'user_id' => $user->id,
                'title' => 'リーダブルコード',
                'author' => 'Dustin Boswell',
                'published_date' => '2012-06-23',
                'description' => '読みやすく保守しやすいコードの書籍。',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=3',
            ]
        );

        $book->genres()->sync(
            Genre::whereIn('name', ['技術書'])->pluck('id')
        );

        // 4. 7つの習慣
        $book = Book::firstOrCreate(
            ['isbn' => '9784863940246'],
            [
                'user_id' => $user->id,
                'title' => '7つの習慣',
                'author' => 'スティーブン・R・コヴィー',
                'published_date' => '2013-08-30',
                'description' => '習慣について解説した有名書籍。',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=4',
            ]
        );

        $book->genres()->sync(
            Genre::whereIn('name', ['ビジネス', '自己啓発'])->pluck('id')
        );

        // 5. 坊っちゃん
        $book = Book::firstOrCreate(
            ['isbn' => '9784101010021'],
            [
                'user_id' => $user->id,
                'title' => '坊っちゃん',
                'author' => '夏目漱石',
                'published_date' => '1906-04-01',
                'description' => '夏目漱石の代表的な小説。',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=5',
            ]
        );

        $book->genres()->sync(
            Genre::whereIn('name', ['小説'])->pluck('id')
        );

        // 6. サピエンス全史
        $book = Book::firstOrCreate(
            ['isbn' => '9784309226712'],
            [
                'user_id' => $user->id,
                'title' => 'サピエンス全史',
                'author' => 'ユヴァル・ノア・ハラリ',
                'published_date' => '2016-09-08',
                'description' => '人類の歴史を具体的に考察した歴史書。',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=6',
            ]
        );

        $book->genres()->sync(
            Genre::whereIn('name', ['歴史', '科学'])->pluck('id')
        );

        // 7. Clean Code
        $book = Book::firstOrCreate(
            ['isbn' => '9784048930598'],
            [
                'user_id' => $user->id,
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'published_date' => '2017-12-18',
                'description' => 'ソフトウェアコードを書くための書籍。',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=7',
            ]
        );

        $book->genres()->sync(
            Genre::whereIn('name', ['技術書'])->pluck('id')
        );

        // 8. 嫌われる勇気
        $book = Book::firstOrCreate(
            ['isbn' => '9784478025819'],
            [
                'user_id' => $user->id,
                'title' => '嫌われる勇気',
                'author' => '岸見一郎・古賀史健',
                'published_date' => '2013-12-13',
                'description' => 'アドラー心理学を対話形式で紹介した自己啓発書。',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=8',
            ]
        );

        $book->genres()->sync(
            Genre::whereIn('name', ['自己啓発'])->pluck('id')
        );

        // 9. 火花
        $book = Book::firstOrCreate(
            ['isbn' => '9784163902302'],
            [
                'user_id' => $user->id,
                'title' => '火花',
                'author' => '又吉直樹',
                'published_date' => '2015-03-11',
                'description' => '芸人又吉直樹渾身の力作小説。',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=9',
            ]
        );

        $book->genres()->sync(
            Genre::whereIn('name', ['小説'])->pluck('id')
        );

        // 10. FACTFULNESS
        $book = Book::firstOrCreate(
            ['isbn' => '9784822289607'],
            [
                'user_id' => $user->id,
                'title' => 'FACTFULNESS',
                'author' => 'ハンス・ロスリング',
                'published_date' => '2019-01-11',
                'description' => 'データをもとに世界を正しく理解するための考え方を解説した書籍。',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=10',
            ]
        );

        $book->genres()->sync(
            Genre::whereIn('name', ['ビジネス', '科学'])->pluck('id')
        );

        // 11. コンテナ物語
        $book = Book::firstOrCreate(
            ['isbn' => '9784822251468'],
            [
                'user_id' => $user->id,
                'title' => 'コンテナ物語',
                'author' => 'マルク・レビンソン',
                'published_date' => '2007-01-18',
                'description' => 'コンテナ輸送が世界の物流や経済に与えた影響を描いた歴史書。',
                'image_url' => 'https://placehold.co/200x300/e2e8f0/475569?text=11',
            ]
        );

        $book->genres()->sync(
            Genre::whereIn('name', ['ビジネス', '歴史'])->pluck('id')
        );
    }
}
