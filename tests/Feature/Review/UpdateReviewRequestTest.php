<?php

namespace Tests\Feature\Review;

use App\Http\Requests\UpdateReviewRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateReviewRequestTest extends TestCase
{
    // レビュー更新時にratingが選択されていない場合、バリデーションエラーになることを確認する。
    public function test_update_fails_when_rating_is_missing(): void
    {
        $data = [
            'rating' => '',
            'comment' => '更新後のコメント',
        ];

        $request = new UpdateReviewRequest;

        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());

        $this->assertSame(
            '評価を選択してください。',
            $validator->errors()->first('rating')
        );
    }

    // レビュー更新時にコメントが未記入の場合、バリデーションエラーになることを確認する。
    public function test_update_fails_when_comment_is_missing(): void
    {
        $data = [
            'rating' => 2,
            'comment' => '',
        ];

        $request = new UpdateReviewRequest;

        $validator = Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());

        $this->assertSame(
            'コメントを入力してください。',
            $validator->errors()->first('comment')
        );
    }

    // レビュー更新時にコメントが1000文字を超えた場合、バリデーションエラーになることを確認する。
    public function test_update_fails_when_comment_is_too_long(): void
    {
        $data = [
            'rating' => 2,
            'comment' => str_repeat('a', 1001),
        ];

        $request = new UpdateReviewRequest;

        $validator = Validator::make(
            $data,
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
