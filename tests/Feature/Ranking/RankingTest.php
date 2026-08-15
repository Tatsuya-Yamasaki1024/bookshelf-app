<?php

namespace Tests\Feature\Ranking;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RankingTest extends TestCase
{
    use RefreshDatabase;

    // ゲストがランキング画面にアクセスでき、評価平均が高い順に最大10冊の書籍が表示される
    public function test_guest_can_view_ranking_and_books_are_sorted_by_average_rating(): void
    {
        $ratings = [1, 2, 3, 4, 5, 1, 2, 3, 4, 5, 1, 2];

        $books = Book::factory()->count(12)->create();

        foreach ($books as $index => $book) {
            Review::factory()->create([
                'book_id' => $book->id,
                'rating' => $ratings[$index],
            ]);
        }

        $response = $this->get(
            route('ranking.index'),
        );

        $response->assertStatus(200);

        $response->assertViewHas('rankedBooks', function ($books) {
            if ($books->count() !== 10) {
                return false;
            }

            $ratings = $books->pluck('reviews_avg_rating')->toArray();

            return $ratings === collect($ratings)
                ->sortDesc()
                ->values()
                ->toArray();
        });
    }

    // レビューのない書籍に★5のレビューを追加すると、その書籍がランキングに加わり、表示件数が10冊のままである
    public function test_book_with_new_five_star_review_is_added_to_ranking_and_ranking_remains_at_ten_books(): void
    {
        $ratings = [1, 2, 3, 4, 1, 2, 3, 4, 1, 2];

        $books = Book::factory()->count(10)->create();

        foreach ($books as $index => $book) {
            Review::factory()->create([
                'book_id' => $book->id,
                'rating' => $ratings[$index],
            ]);
        }

        // レビューのない書籍を作成
        $newBook = Book::factory()->create();

        // レビュー追加前にランキング画面へアクセス
        $response = $this->get(
            route('ranking.index'),
        );

        // レビューのない書籍がランキングに含まれていないことを確認
        $response->assertViewHas('rankedBooks', function ($rankedBooks) use ($newBook) {
            return $rankedBooks->count() === 10
                && ! $rankedBooks->contains('id', $newBook->id);
        });

        // レビューのない書籍に★5のレビューを追加
        Review::factory()->create([
            'book_id' => $newBook->id,
            'rating' => 5,
        ]);

        // レビュー追加後にランキング画面へアクセス
        $response = $this->get(
            route('ranking.index'),
        );

        // ★5の書籍がランキングに加わり、表示件数が10冊のままであることを確認
        $response->assertViewHas('rankedBooks', function ($rankedBooks) use ($newBook) {
            return $rankedBooks->count() === 10
                && $rankedBooks->contains('id', $newBook->id);
        });
    }
}
