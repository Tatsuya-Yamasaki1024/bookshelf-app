<?php

namespace Tests\Feature\Book;

use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateBookRequestTest extends TestCase
{
    use RefreshDatabase;

    private function validator(array $data, ?Book $book = null)
    {
        $book ??= Book::factory()->create();

        $request = new UpdateBookRequest;

        $request->setRouteResolver(function () use ($book) {
            return new class($book)
            {
                public function __construct(private Book $book) {}

                public function parameter($param = null, $default = null)
                {
                    return $param === 'book'
                    ? $this->book
                    : $default;
                }
            };
        });

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

    // 書籍更新時にtitleが未入力の場合はバリデーションエラーになる
    public function test_update_book_requires_title(): void
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

    // 書籍更新時にtitleが255文字を超える場合はバリデーションエラーになる
    public function test_update_book_rejects_title_over_255_characters(): void
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

    // 書籍更新時にauthorが未入力の場合はバリデーションエラーになる
    public function test_update_book_requires_author(): void
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

    // 書籍更新時にauthorが255文字を超える場合はバリデーションエラーになる
    public function test_update_book_rejects_author_over_255_characters(): void
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

    // 書籍更新時にisbnが未入力の場合はバリデーションエラーになる
    public function test_update_book_requires_isbn(): void
    {
        $genre = Genre::factory()->create();

        $validator = $this->validator(
            $this->basePayload($genre, [
                'isbn' => '',
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            '13桁のISBNを入力してください。',
            $validator->errors()->first('isbn')
        );
    }

    // 書籍更新時にisbnが13桁でない場合はバリデーションエラーになる
    public function test_update_book_rejects_invalid_isbn_length(): void
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

    // 書籍更新時にisbnが数字以外を含む場合はバリデーションエラーになる
    public function test_update_book_rejects_non_numeric_isbn(): void
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

    // 書籍更新時に他の書籍ですでに登録されているisbnを入力した場合はバリデーションエラーになる
    public function test_update_book_rejects_duplicate_isbn(): void
    {
        $book = Book::factory()->create([
            'isbn' => '1234567890123',
        ]);

        Book::factory()->create([
            'isbn' => '9876543210123',
        ]);

        $genre = Genre::factory()->create();

        $validator = $this->validator(
            $this->basePayload($genre, [
                'isbn' => '9876543210123',
            ]),
            $book
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            'そのISBNは既に登録されています。',
            $validator->errors()->first('isbn')
        );
    }

    // 書籍更新時に自身のisbnをそのまま入力した場合はバリデーションエラーにならない
    public function test_update_book_allows_own_isbn(): void
    {
        $book = Book::factory()->create([
            'isbn' => '1234567890123',
        ]);

        $genre = Genre::factory()->create();

        $validator = $this->validator(
            $this->basePayload($genre, [
                'isbn' => $book->isbn,
            ]),
            $book
        );

        $this->assertFalse($validator->fails());
    }

    // 書籍更新時にpublished_dateが未入力の場合はバリデーションエラーになる
    public function test_update_book_requires_published_date(): void
    {
        $genre = Genre::factory()->create();

        $validator = $this->validator(
            $this->basePayload($genre, [
                'published_date' => '',
            ])
        );

        $this->assertTrue($validator->fails());
        $this->assertSame(
            '出版日を入力してください。',
            $validator->errors()->first('published_date')
        );
    }

    // 書籍更新時にpublished_dateが日付でない場合はバリデーションエラーになる
    public function test_update_book_rejects_invalid_published_date(): void
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

    // 書籍更新時にdescriptionが1000文字を超える場合はバリデーションエラーになる
    public function test_update_book_rejects_description_over_1000_characters(): void
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

    // 書籍更新時にimage_urlがURL形式でない場合はバリデーションエラーになる
    public function test_update_book_rejects_invalid_image_url(): void
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

    // 書籍更新時にimage_urlが1000文字を超える場合はバリデーションエラーになる
    public function test_update_book_rejects_image_url_over_255_characters(): void
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

    // 書籍更新時にgenresが未入力の場合はバリデーションエラーになる
    public function test_update_book_requires_genres(): void
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

    // 書籍更新時に存在しないgenre_idを指定した場合はバリデーションエラーになる
    public function test_update_book_rejects_nonexistent_genre_id(): void
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
