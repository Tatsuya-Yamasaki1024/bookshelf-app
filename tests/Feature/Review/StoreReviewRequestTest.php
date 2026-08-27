<?php

namespace Tests\Feature\Review;

use App\Http\Requests\StoreReviewRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreReviewRequestTest extends TestCase
{
    use RefreshDatabase;

    // レビュー投稿時にratingが選択されていない場合、バリデーションエラーになることを確認する。
    public function test_store_fails_when_rating_is_missing(): void
    {
        $request = new StoreReviewRequest;

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
        $request = new StoreReviewRequest;

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
        $request = new StoreReviewRequest;

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
}
