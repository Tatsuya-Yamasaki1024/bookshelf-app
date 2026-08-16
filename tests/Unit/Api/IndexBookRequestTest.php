<?php

namespace Tests\Unit\Api;

use App\Http\Requests\Api\V1\IndexBookRequest;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class IndexBookRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validator(array $data)
    {
        $request = new IndexBookRequest();

        return Validator::make(
            $data,
            $request->rules()
        );
    }

    // 全てのパラメータが未指定の場合、バリデーションを通過する
    public function test_all_parameters_are_valid_when_empty()
    {
        $validator = $this->validator([]);

        $this->assertTrue($validator->passes());
    }

    // keywordが正しい入力の場合、バリデーションを通過する
    public function test_keyword_is_valid()
    {
        $validator = $this->validator([
            'keyword' => '夏目',
        ]);

        $this->assertTrue($validator->passes());
    }

    // keywordが255文字を超える場合、バリデーションエラーになる
    public function test_keyword_is_invalid_when_exceeds_255_characters()
    {
        $validator = $this->validator([
            'keyword' => str_repeat('あ', 256),
        ]);

        $this->assertTrue($validator->fails());
    }

    // genre_idが存在するジャンルのIDの場合、バリデーションを通過する
    public function test_genre_id_is_valid_when_genre_exists()
    {
        $genre = Genre::factory()->create();

        $validator = $this->validator([
            'genre_id' => $genre->id,
        ]);

        $this->assertTrue($validator->passes());
    }

    // genre_idが存在しないIDの場合、バリデーションエラーになる
    public function test_genre_id_is_invalid_when_genre_does_not_exist()
    {
        $validator = $this->validator([
            'genre_id' => 999999,
        ]);

        $this->assertTrue($validator->fails());
    }

    // pageが1以上の整数の場合、バリデーションを通過する
    public function test_page_is_valid_when_integer_greater_than_or_equal_to_1()
    {
        $validator = $this->validator([
            'page' => 2,
        ]);

        $this->assertTrue($validator->passes());
    }

    // pageが1未満の場合、バリデーションエラーになる
    public function test_page_is_invalid_when_less_than_1()
    {
        $validator = $this->validator([
            'page' => 0,
        ]);

        $this->assertTrue($validator->fails());
    }

    // per_pageが1〜100の場合、バリデーションを通過する
    public function test_per_page_is_valid_when_between_1_and_100()
    {
        $validator = $this->validator([
            'per_page' => 10,
        ]);

        $this->assertTrue($validator->passes());
    }

    // per_pageが1未満の場合、バリデーションエラーになる
    public function test_per_page_is_invalid_when_less_than_1()
    {
        $validator = $this->validator([
            'per_page' => 0,
        ]);

        $this->assertTrue($validator->fails());
    }

    // per_pageが100を超える場合、バリデーションエラーになる
    public function test_per_page_is_invalid_when_exceeds_100()
    {
        $validator = $this->validator([
            'per_page' => 101,
        ]);

        $this->assertTrue($validator->fails());
    }
}