<?php

namespace Tests\Feature\Genre;

use App\Http\Requests\StoreGenreRequest;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreGenreRequestTest extends TestCase
{
    use RefreshDatabase;

    // ジャンル登録時にジャンル名が未入力の場合、バリデーションエラーになることを確認する。
    public function test_store_genre_rejects_empty_name(): void
    {
        $request = new StoreGenreRequest;

        $validator = Validator::make(
            [
                'name' => '',
            ],
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            'ジャンル名を入力してください。',
            $validator->errors()->first('name')
        );
    }

    // ジャンル登録時にジャンル名が255文字を超えた場合、バリデーションエラーになることを確認する。
    public function test_store_genre_rejects_name_exceeding_max_length(): void
    {
        $request = new StoreGenreRequest;

        $validator = Validator::make(
            [
                'name' => str_repeat('a', 256),
            ],
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            'ジャンル名は255文字以内で入力してください。',
            $validator->errors()->first('name')
        );
    }

    // ジャンル登録時に既に存在するジャンル名を入力した場合、バリデーションエラーになることを確認する。
    public function test_store_genre_rejects_duplicate_name(): void
    {
        $request = new StoreGenreRequest;

        Genre::factory()->create([
            'name' => '重複ジャンル',
        ]);

        $validator = Validator::make(
            [
                'name' => '重複ジャンル',
            ],
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            'そのジャンルは既に登録されています。',
            $validator->errors()->first('name')
        );
    }
}
