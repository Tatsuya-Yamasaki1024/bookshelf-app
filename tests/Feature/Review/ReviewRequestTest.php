<?php

namespace Tests\Feature\Review;

use App\Http\Requests\ReviewRequest;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ReviewRequestTest extends TestCase
{
    use RefreshDatabase;

    // レビュー投稿時にratingが選択されていない場合、バリデーションエラーになることを確認する。
    public function test_store_fails_when_rating_is_missing(): void
    {
        $request = new ReviewRequest;

        $validator = Validator::make(
            [
                'rating' => '',
                'comment' => 'テスト投稿',
            ],
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            '評価を選択してください。',
            $validator->errors()->first('rating')
        );
    }

    // レビュー投稿時にコメントが未記入の場合、バリデーションエラーになることを確認する。
    public function test_store_fails_when_comment_is_missing(): void
    {
        $request = new ReviewRequest;

        $validator = Validator::make(
            [
                'rating' => '3',
                'comment' => '',
            ],
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            'コメントを入力してください。',
            $validator->errors()->first('comment')
        );
    }

    // レビュー投稿時にコメントが1000文字を超えた場合、バリデーションエラーになることを確認する。
    public function test_store_fails_when_comment_is_too_long(): void
    {
        $request = new ReviewRequest;

        $validator = Validator::make(
            [
                'rating' => '3',
                'comment' => str_repeat('a', 1001),
            ],
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            'コメントは1000文字以内で入力してください。',
            $validator->errors()->first('comment')
        );
    }

    // レビュー更新時にratingが選択されていない場合、バリデーションエラーになることを確認する。
    public function test_update_fails_when_rating_is_missing(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $book->genres()->attach($genre);

        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 3,
            'comment' => 'テスト投稿',
        ]);

        $response = $this->actingAs($user)->put(
            route('reviews.update', $review),
            [
                'rating' => '',
                'comment' => '更新後のコメント',
            ]
        );

        $response->assertSessionHasErrors([
            'rating' => '評価を選択してください。',
        ]);
    }

    // レビュー更新時にコメントが未記入の場合、バリデーションエラーになることを確認する。
    public function test_update_fails_when_comment_is_missing(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $book->genres()->attach($genre);

        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 3,
            'comment' => 'テスト投稿',
        ]);

        $response = $this->actingAs($user)->put(
            route('reviews.update', $review),
            [
                'rating' => '2',
                'comment' => '',
            ]
        );

        $response->assertSessionHasErrors([
            'comment' => 'コメントを入力してください。',
        ]);
    }

    // レビュー更新時にコメントが1000文字を超えた場合、バリデーションエラーになることを確認する。
    public function test_update_fails_when_comment_is_too_long(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $book->genres()->attach($genre);

        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => 3,
            'comment' => 'テスト投稿',
        ]);

        $response = $this->actingAs($user)->put(
            route('reviews.update', $review),
            [
                'rating' => '2',
                'comment' => str_repeat('a', 1001),
            ]
        );

        $response->assertSessionHasErrors([
            'comment' => 'コメントは1000文字以内で入力してください。',
        ]);
    }
}
