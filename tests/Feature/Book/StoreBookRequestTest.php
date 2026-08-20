<?php

namespace Tests\Feature\Book;

use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreBookRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validator(array $data)
    {
        $request = new StoreBookRequest;

        return Validator::make(
            $data,
            $request->rules(),
            $request->messages()
        );
    }

    private function basePayload(Genre $genre, array $overrides = []): array
    {
        return array_merge([
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'published_date' => '2026-01-01',
            'description' => 'テスト説明',
            'image_url' => 'https://example.com/image.jpg',
            'genres' => [$genre->id],
        ], $overrides);
    }

    // 書籍登録時にtitleが未入力の場合はバリデーションエラーになる
    public function test_store_book_requires_title(): void
    {
        $genre = Genre::factory()->create();

        $validator = $this->validator(
            $this->basePayload($genre, [
                'title' => '',
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            'タイトルを入力してください。',
            $validator->errors()->first('title')
        );
    }

    // 書籍登録時にtitleが255文字を超える場合はバリデーションエラーになる
    public function test_store_book_rejects_title_over_255_characters(): void
    {
        $genre = Genre::factory()->create();

        $validator = $this->validator(
            $this->basePayload($genre, [
                'title' => str_repeat('a', 256),
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            'タイトルは255文字以内で入力してください。',
            $validator->errors()->first('title')
        );
    }

    // 書籍登録時にauthorが未入力の場合はバリデーションエラーになる
    public function test_store_book_requires_author(): void
    {
        $genre = Genre::factory()->create();

        $validator = $this->validator(
            $this->basePayload($genre, [
                'author' => '',
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            '著者を入力してください。',
            $validator->errors()->first('author')
        );
    }

    // 書籍登録時にauthorが255文字を超える場合はバリデーションエラーになる
    public function test_store_book_rejects_author_over_255_characters(): void
    {
        $genre = Genre::factory()->create();

        $validator = $this->validator(
            $this->basePayload($genre, [
                'author' => str_repeat('a', 256),
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            '著者名は255文字以内で入力してください。',
            $validator->errors()->first('author')
        );
    }

    // 書籍登録時にisbnが13桁でない場合はバリデーションエラーになる
    public function test_store_book_rejects_invalid_isbn_length(): void
    {
        $genre = Genre::factory()->create();

        $validator = $this->validator(
            $this->basePayload($genre, [
                'isbn' => '12345678901234',
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            '13桁のISBNを入力してください。',
            $validator->errors()->first('isbn')
        );
    }

    // 書籍登録時にisbnが数字以外を含む場合はバリデーションエラーになる

    public function test_store_book_rejects_non_numeric_isbn(): void
    {
        $genre = Genre::factory()->create();

        $validator = $this->validator(
            $this->basePayload($genre, [
                'isbn' => '12345678901a3',
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            '13桁のISBNを入力してください。',
            $validator->errors()->first('isbn')
        );
    }

    // 書籍登録時に登録済みのisbnを入力した場合はバリデーションエラーになる
    public function test_store_book_rejects_duplicate_isbn(): void
    {
        $genre = Genre::factory()->create();

        Book::factory()->create([
            'isbn' => '1234567890123',
        ]);

        $validator = $this->validator(
            $this->basePayload($genre, [
                'isbn' => '1234567890123',
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            'そのISBNは既に登録されています。',
            $validator->errors()->first('isbn')
        );
    }

    // 書籍登録時にpublished_dateが日付でない場合はバリデーションエラーになる
    public function test_store_book_rejects_invalid_published_date(): void
    {
        $genre = Genre::factory()->create();

        $validator = $this->validator(
            $this->basePayload($genre, [
                'published_date' => '20261a21',
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            '出版日は正しい日付を入力してください。',
            $validator->errors()->first('published_date')
        );
    }

    // 書籍登録時にdescriptionが1000文字を超える場合はバリデーションエラーになる
    public function test_store_book_rejects_description_over_1000_characters(): void
    {
        $genre = Genre::factory()->create();

        $validator = $this->validator(
            $this->basePayload($genre, [
                'description' => str_repeat('a', 1001),
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            '説明は1000文字以内で入力してください。',
            $validator->errors()->first('description')
        );
    }

    // 書籍登録時にimage_urlがURL形式でない場合はバリデーションエラーになる
    public function test_store_book_rejects_invalid_image_url(): void
    {
        $genre = Genre::factory()->create();

        $validator = $this->validator(
            $this->basePayload($genre, [
                'image_url' => 'imagesample',
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            '画像URLは正しいURL形式で入力してください。',
            $validator->errors()->first('image_url')
        );
    }

    // 書籍登録時にimage_urlが1000文字を超える場合はバリデーションエラーになる
    public function test_store_book_rejects_image_url_over_255_characters(): void
    {
        $genre = Genre::factory()->create();

        $validator = $this->validator(
            $this->basePayload($genre, [
                'image_url' => 'https://'.str_repeat('a', 996),
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            '画像URLは1000文字以内で入力してください。',
            $validator->errors()->first('image_url')
        );
    }

    // 書籍登録時にgenresが未入力の場合はバリデーションエラーになる
    public function test_store_book_requires_genres(): void
    {
        $validator = $this->validator([
            'title' => 'テスト書籍',
            'author' => 'テスト著者',
            'isbn' => '1234567890123',
            'published_date' => '2026-01-01',
            'description' => 'テスト説明',
            'image_url' => 'https://example.com/image.jpg',
        ]);

        $this->assertTrue($validator->fails());
        $this->assertSame(
            'ジャンルを1つ以上選択してください。',
            $validator->errors()->first('genres')
        );
    }

    // 書籍登録時に存在しないgenre_idを指定した場合はバリデーションエラーになる
    public function test_store_book_rejects_nonexistent_genre_id(): void
    {
        $genre = Genre::factory()->create();

        $validator = $this->validator(
            $this->basePayload($genre, [
                'genres' => [9999],
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            '指定されたジャンルは存在しません。',
            $validator->errors()->first('genres.0')
        );
    }
}
