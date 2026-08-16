<?php

namespace Tests\Unit\Api;

use App\Http\Requests\Api\V1\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateBookRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validator(array $data, Book $book)
    {
        $request = new UpdateBookRequest;

        $request->setRouteResolver(function () use ($book) {
            return new class($book)
            {
                public function __construct(private Book $book) {}

                public function parameter($param)
                {
                    return $param === 'book' ? $this->book : null;
                }
            };
        });

        return Validator::make(
            $data,
            $request->rules()
        );
    }

    private function validData(): array
    {
        $book = Book::factory()->create();
        $genre = Genre::factory()->create();

        return [
            'book' => $book,
            'data' => [
                'title' => '吾輩は猫である',
                'author' => '夏目漱石',
                'isbn' => '9784101010014',
                'published_date' => '1905-01-01',
                'description' => '名作',
                'image_url' => 'https://example.com/book.jpg',
                'genres' => [$genre->id],
            ],
        ];
    }

    // titleが正しい入力の場合、バリデーションを通過する
    public function test_title_is_valid()
    {
        $validData = $this->validData();

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->passes());
    }

    // titleが未入力の場合、バリデーションエラーになる
    public function test_title_is_invalid_when_empty()
    {
        $validData = $this->validData();
        unset($validData['data']['title']);

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // titleが文字列以外の場合、バリデーションエラーになる
    public function test_title_is_invalid_when_not_string()
    {
        $validData = $this->validData();
        $validData['data']['title'] = 123;

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // titleが256文字以上の場合、バリデーションエラーになる
    public function test_title_is_invalid_when_exceeds_255_characters()
    {
        $validData = $this->validData();
        $validData['data']['title'] = str_repeat('あ', 256);

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // authorが正しい入力の場合、バリデーションを通過する
    public function test_author_is_valid()
    {
        $validData = $this->validData();

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->passes());
    }

    // authorが未入力の場合、バリデーションエラーになる
    public function test_author_is_invalid_when_empty()
    {
        $validData = $this->validData();
        unset($validData['data']['author']);

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // authorが文字列以外の場合、バリデーションエラーになる
    public function test_author_is_invalid_when_not_string()
    {
        $validData = $this->validData();
        $validData['data']['author'] = 123;

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // authorが256文字以上の場合、バリデーションエラーになる
    public function test_author_is_invalid_when_exceeds_255_characters()
    {
        $validData = $this->validData();
        $validData['data']['author'] = str_repeat('あ', 256);

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // isbnが正しい入力の場合、バリデーションを通過する
    public function test_isbn_is_valid()
    {
        $validData = $this->validData();

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->passes());
    }

    // isbnが未入力の場合、バリデーションエラーになる
    public function test_isbn_is_invalid_when_empty()
    {
        $validData = $this->validData();
        unset($validData['data']['isbn']);

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // isbnが13桁ではない場合、バリデーションエラーになる
    public function test_isbn_is_invalid_when_not_13_digits()
    {
        $validData = $this->validData();
        $validData['data']['isbn'] = '123456789012';

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // isbnが13桁でも数字以外が含まれる場合、バリデーションエラーになる
    public function test_isbn_is_invalid_when_contains_non_numeric_characters()
    {
        $validData = $this->validData();
        $validData['data']['isbn'] = '97841010100A4';

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // isbnが自身の現在のISBNと同じ場合、バリデーションを通過する
    public function test_isbn_is_valid_when_same_as_current_book()
    {
        $validData = $this->validData();
        $validData['data']['isbn'] = $validData['book']->isbn;

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->passes());
    }

    // isbnが他の書籍と重複している場合、バリデーションエラーになる
    public function test_isbn_is_invalid_when_already_exists_on_another_book()
    {
        $validData = $this->validData();

        $otherBook = Book::factory()->create();

        $validData['data']['isbn'] = $otherBook->isbn;

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // published_dateが正しい入力の場合、バリデーションを通過する
    public function test_published_date_is_valid()
    {
        $validData = $this->validData();

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->passes());
    }

    // published_dateが未入力の場合、バリデーションエラーになる
    public function test_published_date_is_invalid_when_empty()
    {
        $validData = $this->validData();
        unset($validData['data']['published_date']);

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // published_dateが日付ではない場合、バリデーションエラーになる
    public function test_published_date_is_invalid_when_not_date()
    {
        $validData = $this->validData();
        $validData['data']['published_date'] = 'invalid-date';

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // descriptionが正しい入力の場合、バリデーションを通過する
    public function test_description_is_valid()
    {
        $validData = $this->validData();

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->passes());
    }

    // descriptionが未入力の場合、バリデーションを通過する
    public function test_description_is_valid_when_empty()
    {
        $validData = $this->validData();
        unset($validData['data']['description']);

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->passes());
    }

    // descriptionがnullの場合、バリデーションを通過する
    public function test_description_is_valid_when_null()
    {
        $validData = $this->validData();
        $validData['data']['description'] = null;

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->passes());
    }

    // descriptionが文字列以外の場合、バリデーションエラーになる
    public function test_description_is_invalid_when_not_string()
    {
        $validData = $this->validData();
        $validData['data']['description'] = 123;

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // descriptionが1001文字以上の場合、バリデーションエラーになる
    public function test_description_is_invalid_when_exceeds_1000_characters()
    {
        $validData = $this->validData();
        $validData['data']['description'] = str_repeat('あ', 1001);

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // image_urlが正しい入力の場合、バリデーションを通過する
    public function test_image_url_is_valid()
    {
        $validData = $this->validData();

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->passes());
    }

    // image_urlが未入力の場合、バリデーションを通過する
    public function test_image_url_is_valid_when_empty()
    {
        $validData = $this->validData();
        unset($validData['data']['image_url']);

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->passes());
    }

    // image_urlがnullの場合、バリデーションを通過する
    public function test_image_url_is_valid_when_null()
    {
        $validData = $this->validData();
        $validData['data']['image_url'] = null;

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->passes());
    }

    // image_urlがURLではない場合、バリデーションエラーになる
    public function test_image_url_is_invalid_when_not_url()
    {
        $validData = $this->validData();
        $validData['data']['image_url'] = 'invalid-url';

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // image_urlが文字列以外の場合、バリデーションエラーになる
    public function test_image_url_is_invalid_when_not_string()
    {
        $validData = $this->validData();
        $validData['data']['image_url'] = 123;

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // image_urlが1001文字以上の場合、バリデーションエラーになる
    public function test_image_url_is_invalid_when_exceeds_255_characters()
    {
        $validData = $this->validData();
        $validData['data']['image_url'] = 'https://example.com/'.str_repeat('a', 986);

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // genresが正しい入力の場合、バリデーションを通過する
    public function test_genres_is_valid()
    {
        $validData = $this->validData();

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->passes());
    }

    // genresが未入力の場合、バリデーションエラーになる
    public function test_genres_is_invalid_when_empty()
    {
        $validData = $this->validData();
        unset($validData['data']['genres']);

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // genresが配列ではない場合、バリデーションエラーになる
    public function test_genres_is_invalid_when_not_array()
    {
        $validData = $this->validData();
        $validData['data']['genres'] = '1';

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // genresが空配列の場合、バリデーションエラーになる
    public function test_genres_is_invalid_when_empty_array()
    {
        $validData = $this->validData();
        $validData['data']['genres'] = [];

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // genresの要素が正しいジャンルIDの場合、バリデーションを通過する
    public function test_genres_item_is_valid()
    {
        $validData = $this->validData();

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->passes());
    }

    // genresの要素が整数ではない場合、バリデーションエラーになる
    public function test_genres_item_is_invalid_when_not_integer()
    {
        $validData = $this->validData();
        $validData['data']['genres'] = ['abc'];

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }

    // genresの要素が存在しないジャンルIDの場合、バリデーションエラーになる
    public function test_genres_item_is_invalid_when_genre_does_not_exist()
    {
        $validData = $this->validData();
        $validData['data']['genres'] = [999999];

        $validator = $this->validator(
            $validData['data'],
            $validData['book']
        );

        $this->assertTrue($validator->fails());
    }
}
