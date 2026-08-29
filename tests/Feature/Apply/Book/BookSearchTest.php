<?php

namespace Tests\Feature\Apply\Book;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookSearchTest extends TestCase
{
    use RefreshDatabase;

    // タイトルに部分一致する書籍が表示され、不一致の書籍は表示されない
    public function test_books_can_be_searched_by_title(): void
    {
        $targetBook = Book::factory()->create([
            'title' => '吾輩は猫である',
        ]);

        Book::factory()->create([
            'title' => '坊っちゃん',
        ]);

        $response = $this->get(route('books.index', [
            'keyword' => '猫',
        ]));

        $response->assertOk()
            ->assertSee($targetBook->title)
            ->assertDontSee('坊っちゃん');
    }

    // 著者に部分一致する書籍が表示され、不一致の書籍は表示されない
    public function test_books_can_be_searched_by_author(): void
    {
        $targetBook = Book::factory()->create([
            'author' => '夏目漱石',
        ]);

        Book::factory()->create([
            'author' => '太宰治',
        ]);

        $response = $this->get(route('books.index', [
            'keyword' => '夏目',
        ]));

        $response->assertOk()
            ->assertSee($targetBook->title)
            ->assertDontSee('太宰治');
    }

    // キーワードが空欄でも書籍一覧を表示できる（全件表示）
    public function test_books_are_displayed_when_keyword_is_empty(): void
    {
        $book1 = Book::factory()->create([
            'title' => '吾輩は猫である',
        ]);

        $book2 = Book::factory()->create([
            'title' => '坊っちゃん',
        ]);

        $response = $this->get(route('books.index', [
            'keyword' => '',
        ]));

        $response->assertOk()
            ->assertSee($book1->title)
            ->assertSee($book2->title);
    }

    // 指定したジャンルの書籍だけが表示される
    public function test_books_can_be_filtered_by_genre(): void
    {
        $genre = Genre::factory()->create([
            'name' => '表示用ジャンル',
        ]);

        $otherGenre = Genre::factory()->create();

        $targetBook = Book::factory()->create();
        $targetBook->genres()->attach($genre);

        $otherBook = Book::factory()->create();
        $otherBook->genres()->attach($otherGenre);

        $response = $this->get(route('books.index', [
            'genre' => $genre->id,
        ]));

        $response->assertOk()
            ->assertSee($targetBook->title)
            ->assertDontSee($otherBook->title);
    }

    // ジャンルを指定しない場合、全ジャンルの書籍が表示される
    public function test_books_are_displayed_from_all_genres_when_genre_is_not_specified(): void
    {
        $genre = Genre::factory()->create();
        $otherGenre = Genre::factory()->create();

        $book1 = Book::factory()->create();
        $book1->genres()->attach($genre);

        $book2 = Book::factory()->create();
        $book2->genres()->attach($otherGenre);

        $response = $this->get(route('books.index', [
            'genre' => '',
        ]));

        $response->assertOk()
            ->assertSee($book1->title)
            ->assertSee($book2->title);
    }

    // sort未指定の場合、登録日が新しい順で表示される
    public function test_books_are_sorted_by_newest_by_default(): void
    {
        $book1 = Book::factory()->create([
            'created_at' => '2026-01-01 00:00:00',
        ]);

        $book2 = Book::factory()->create([
            'created_at' => '2026-03-01 00:00:00',
        ]);
        $book3 = Book::factory()->create([
            'created_at' => '2026-02-01 00:00:00',
        ]);

        $response = $this->get(route('books.index'));

        $response->assertOk()
            ->assertSeeInOrder([
                $book2->title,
                $book3->title,
                $book1->title,
            ]);
    }

    // oldestを指定した場合、登録日が古い順で表示される
    public function test_books_are_sorted_by_oldest_when_oldest_is_specified(): void
    {
        $book1 = Book::factory()->create([
            'created_at' => '2026-01-01 00:00:00',
        ]);

        $book2 = Book::factory()->create([
            'created_at' => '2026-03-01 00:00:00',
        ]);
        $book3 = Book::factory()->create([
            'created_at' => '2026-02-01 00:00:00',
        ]);

        $response = $this->get(route('books.index', [
            'sort' => 'oldest',
        ]));
        $response->assertOk()
            ->assertSeeInOrder([
                $book1->title,
                $book3->title,
                $book2->title,
            ]);
    }

    // titleを指定した場合、タイトルの昇順で表示される
    public function test_books_are_sorted_by_title_ascending_when_title_is_specified(): void
    {
        $book1 = Book::factory()->create([
            'title' => 'あいうえお',
        ]);

        $book2 = Book::factory()->create([
            'title' => '吾輩は猫である',
        ]);
        $book3 = Book::factory()->create([
            'title' => 'かきくけこ',
        ]);

        $response = $this->get(route('books.index', [
            'sort' => 'title',
        ]));
        $response->assertOk()
            ->assertSeeInOrder([
                $book1->title,
                $book3->title,
                $book2->title,
            ]);
    }

    // ratingを指定した場合、評価が高い順で表示され、レビューがない書籍は最後に表示される
    public function test_books_are_sorted_by_rating_descending_when_rating_is_specified(): void
    {
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();
        $book3 = Book::factory()->create();

        Review::factory()->create([
            'book_id' => $book1->id,
            'rating' => 3,
        ]);

        Review::factory()->create([
            'book_id' => $book2->id,
            'rating' => 5,
        ]);

        $response = $this->get(route('books.index', [
            'sort' => 'rating',
        ]));
        $response->assertOk()
            ->assertSeeInOrder([
                $book2->title,
                $book1->title,
                $book3->title,
            ]);
    }

    // 検索条件を維持したまま指定したページに遷移できる
    public function test_pagination_preserves_search_conditions(): void
    {
        Book::factory()->count(10)->create([
            'title' => 'サナギの本',
        ]);

        $book1 = Book::factory()->create([
            'title' => '別の本',
        ]);

        $book2 = Book::factory()->create([
            'title' => 'サナギは生きる',
        ]);

        $book3 = Book::factory()->create([
            'title' => '蝶のサナギ',
        ]);

        $response = $this->get(route('books.index', [
            'keyword' => 'サナギ',
            'page' => 2,
        ]));

        $response->assertOk()
            ->assertSeeInOrder([
                $book2->title,
                $book3->title,
            ])
            ->assertDontSee($book1->title)
            ->assertSee(
                urlencode('サナギ'),
                false
            );
    }
}
