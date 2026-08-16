<?php

namespace Tests\Unit\Api;

use App\Http\Requests\Api\V1\StoreBookRequest;
use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreBookRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validator(array $data)
    {
        $request = new StoreBookRequest();

        return Validator::make(
            $data,
            $request->rules()
        );
    }

    private function validData(): array
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        return [
            'user_id' => $user->id,
            'title' => '吾輩は猫である',
            'author' => '夏目漱石',
            'isbn' => '9784101010014',
            'published_date' => '1905-01-01',
            'description' => '名作',
            'image_url' => 'https://example.com/book.jpg',
            'genres' => [$genre->id],
        ];
    }

    // user_idが正しい入力の場合、バリデーションを通過する
    public function test_user_id_is_valid()
    {
        $data = $this->validData();

        $validator = $this->validator($data);

        $this->assertTrue($validator->passes());
    }

    // user_idが未入力の場合、バリデーションエラーになる
    public function test_user_id_is_invalid_when_empty()
    {
        $data = $this->validData();
        unset($data['user_id']);

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // user_idが整数以外の場合、バリデーションエラーになる
    public function test_user_id_is_invalid_when_not_integer()
    {
        $data = $this->validData();
        $data['user_id'] = 'abc';

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // user_idが存在しない場合、バリデーションエラーになる
    public function test_user_id_is_invalid_when_user_does_not_exist()
    {
        $data = $this->validData();
        $data['user_id'] = 999999;

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // titleが正しい入力の場合、バリデーションを通過する
    public function test_title_is_valid()
    {
        $data = $this->validData();

        $validator = $this->validator($data);

        $this->assertTrue($validator->passes());
    }

    // titleが未入力の場合、バリデーションエラーになる
    public function test_title_is_invalid_when_empty()
    {
        $data = $this->validData();
        unset($data['title']);

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // titleが文字列以外の場合、バリデーションエラーになる
    public function test_title_is_invalid_when_not_string()
    {
        $data = $this->validData();
        $data['title'] = 123;

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // titleが256文字以上の場合、バリデーションエラーになる
    public function test_title_is_invalid_when_exceeds_255_characters()
    {
        $data = $this->validData();
        $data['title'] = str_repeat('あ', 256);

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // authorが正しい入力の場合、バリデーションを通過する
    public function test_author_is_valid()
    {
        $data = $this->validData();

        $validator = $this->validator($data);

        $this->assertTrue($validator->passes());
    }

    // authorが未入力の場合、バリデーションエラーになる
    public function test_author_is_invalid_when_empty()
    {
        $data = $this->validData();
        unset($data['author']);

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // authorが文字列以外の場合、バリデーションエラーになる
    public function test_author_is_invalid_when_not_string()
    {
        $data = $this->validData();
        $data['author'] = 123;

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // authorが256文字以上の場合、バリデーションエラーになる
    public function test_author_is_invalid_when_exceeds_255_characters()
    {
        $data = $this->validData();
        $data['author'] = str_repeat('あ', 256);

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // isbnが正しい入力の場合、バリデーションを通過する
    public function test_isbn_is_valid()
    {
        $data = $this->validData();

        $validator = $this->validator($data);

        $this->assertTrue($validator->passes());
    }

    // isbnが未入力の場合、バリデーションエラーになる
    public function test_isbn_is_invalid_when_empty()
    {
        $data = $this->validData();
        unset($data['isbn']);

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // isbnが13桁ではない場合、バリデーションエラーになる
    public function test_isbn_is_invalid_when_not_13_digits()
    {
        $data = $this->validData();
        $data['isbn'] = '123456789012';

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // isbnが13桁でも数字以外が含まれる場合、バリデーションエラーになる
    public function test_isbn_is_invalid_when_contains_non_numeric_characters()
    {
        $data = $this->validData();
        $data['isbn'] = '97841010100A4';

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // isbnが重複している場合、バリデーションエラーになる
    public function test_isbn_is_invalid_when_already_exists()
    {
        $data = $this->validData();

        Book::factory()->create([
            'isbn' => $data['isbn'],
        ]);

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // published_dateが正しい入力の場合、バリデーションを通過する
    public function test_published_date_is_valid()
    {
        $data = $this->validData();

        $validator = $this->validator($data);

        $this->assertTrue($validator->passes());
    }

    // published_dateが未入力の場合、バリデーションエラーになる
    public function test_published_date_is_invalid_when_empty()
    {
        $data = $this->validData();
        unset($data['published_date']);

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // published_dateが日付ではない場合、バリデーションエラーになる
    public function test_published_date_is_invalid_when_not_date()
    {
        $data = $this->validData();
        $data['published_date'] = 'invalid-date';

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // descriptionが正しい入力の場合、バリデーションを通過する
    public function test_description_is_valid()
    {
        $data = $this->validData();

        $validator = $this->validator($data);

        $this->assertTrue($validator->passes());
    }

    // descriptionが未入力の場合、バリデーションを通過する
    public function test_description_is_valid_when_empty()
    {
        $data = $this->validData();
        unset($data['description']);

        $validator = $this->validator($data);

        $this->assertTrue($validator->passes());
    }

    // descriptionがnullの場合、バリデーションを通過する
    public function test_description_is_valid_when_null()
    {
        $data = $this->validData();
        $data['description'] = null;

        $validator = $this->validator($data);

        $this->assertTrue($validator->passes());
    }

    // descriptionが文字列以外の場合、バリデーションエラーになる
    public function test_description_is_invalid_when_not_string()
    {
        $data = $this->validData();
        $data['description'] = 123;

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // descriptionが1001文字以上の場合、バリデーションエラーになる
    public function test_description_is_invalid_when_exceeds_1000_characters()
    {
        $data = $this->validData();
        $data['description'] = str_repeat('あ', 1001);

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // image_urlが正しい入力の場合、バリデーションを通過する
    public function test_image_url_is_valid()
    {
        $data = $this->validData();

        $validator = $this->validator($data);

        $this->assertTrue($validator->passes());
    }

    // image_urlが未入力の場合、バリデーションを通過する
    public function test_image_url_is_valid_when_empty()
    {
        $data = $this->validData();
        unset($data['image_url']);

        $validator = $this->validator($data);

        $this->assertTrue($validator->passes());
    }

    // image_urlがnullの場合、バリデーションを通過する
    public function test_image_url_is_valid_when_null()
    {
        $data = $this->validData();
        $data['image_url'] = null;

        $validator = $this->validator($data);

        $this->assertTrue($validator->passes());
    }

    // image_urlがURLではない場合、バリデーションエラーになる
    public function test_image_url_is_invalid_when_not_url()
    {
        $data = $this->validData();
        $data['image_url'] = 'invalid-url';

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // image_urlが文字列以外の場合、バリデーションエラーになる
    public function test_image_url_is_invalid_when_not_string()
    {
        $data = $this->validData();
        $data['image_url'] = 123;

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // image_urlが1001文字以上の場合、バリデーションエラーになる
    public function test_image_url_is_invalid_when_exceeds_1000_characters()
    {
        $data = $this->validData();
        $data['image_url'] = 'https://' . str_repeat('a', 990) . '.com';

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // genresが正しい入力の場合、バリデーションを通過する
    public function test_genres_is_valid()
    {
        $data = $this->validData();

        $validator = $this->validator($data);

        $this->assertTrue($validator->passes());
    }

    // genresが未入力の場合、バリデーションエラーになる
    public function test_genres_is_invalid_when_empty()
    {
        $data = $this->validData();
        unset($data['genres']);

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // genresが配列ではない場合、バリデーションエラーになる
    public function test_genres_is_invalid_when_not_array()
    {
        $data = $this->validData();
        $data['genres'] = '1';

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // genresが空配列の場合、バリデーションエラーになる
    public function test_genres_is_invalid_when_empty_array()
    {
        $data = $this->validData();
        $data['genres'] = [];

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // genresの要素が正しいジャンルIDの場合、バリデーションを通過する
    public function test_genres_item_is_valid()
    {
        $data = $this->validData();

        $validator = $this->validator($data);

        $this->assertTrue($validator->passes());
    }

    // genresの要素が整数ではない場合、バリデーションエラーになる
    public function test_genres_item_is_invalid_when_not_integer()
    {
        $data = $this->validData();
        $data['genres'] = ['abc'];

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }

    // genresの要素が存在しないジャンルIDの場合、バリデーションエラーになる
    public function test_genres_item_is_invalid_when_genre_does_not_exist()
    {
        $data = $this->validData();
        $data['genres'] = [999999];

        $validator = $this->validator($data);

        $this->assertTrue($validator->fails());
    }
}