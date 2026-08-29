<?php

namespace Tests\Feature\Apply\ReadingReport;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\Genre;
use App\Models\ReadingPlan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingReportTest extends TestCase
{
    use RefreshDatabase;

    // ゲストがマイ読書レポートにアクセスするとログイン画面に遷移する
    public function test_guest_is_redirected_to_login_when_accessing_reading_report(): void
    {
        $response = $this->get(route('reports.index'));

        $response->assertRedirect(route('login'));
    }

    // ログインユーザーの総レビュー数、ユニークな読了冊数、平均評価点が正しく表示される
    public function test_reading_report_displays_authenticated_users_review_count_unique_book_count_and_average_rating(): void
    {
        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();
        $book3 = Book::factory()->create();

        $user = User::factory()->create();
        $otherUser = User::factory()->create();


        Review::factory()->count(2)->create([
            'user_id' => $user->id,
            'rating' => 1,
        ]);

        Review::factory()->count(2)->create([
            'user_id' => $user->id,
            'rating' => 5,
        ]);

        Review::factory()->count(10)->create([
            'user_id' => $otherUser->id,
        ]);

        ReadingPlan::factory()->count(3)->create([
            'book_id' => $book1->id,
            'user_id' => $user->id,
            'status' => ReadingPlanStatus::Completed,
        ]);

        ReadingPlan::factory()->create([
            'book_id' => $book2->id,
            'user_id' => $user->id,
            'status' => ReadingPlanStatus::InProgress,
        ]);

        ReadingPlan::factory()->create([
            'book_id' => $book3->id,
            'user_id' => $user->id,
            'status' => ReadingPlanStatus::Completed,
        ]);

        $response = $this->actingAs($user)
            ->get('/reports');

        // 総レビュー数4件、ユニークな読了冊数2冊、平均評価点3.0になっているかチェック
        $response->assertOk()
            ->assertSeeInOrder([
                '4',
                '2',
                '3.0',
            ]);
    }

    // 高評価書籍TOP5が評価の高い順に最大5件、書籍詳細リンク付きで表示される
    // 星4未満の書籍は表示されない
    public function test_reading_report_displays_top_5_high_rated_books_in_rating_order(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        $book1 = Book::factory()->create(['title' => '評価5の本']);
        $book2 = Book::factory()->create(['title' => '評価4.5の本']);
        $book3 = Book::factory()->create(['title' => '評価4の本']);
        $book4 = Book::factory()->create(['title' => '評価4の本2']);
        $book5 = Book::factory()->create(['title' => '評価4の本3']);
        $book6 = Book::factory()->create(['title' => '評価3.5の本']);
        $lowRatedBook = Book::factory()->create(['title' => '評価3の本']);

        // Book1 → 平均5.0
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book1->id,
            'rating' => 5,
        ]);

        // Book2 → 平均4.5
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book2->id,
            'rating' => 5,
        ]);

        Review::factory()->create([
            'user_id' => $user2->id,
            'book_id' => $book2->id,
            'rating' => 4,
        ]);

        // Book3 → 平均4.0
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book3->id,
            'rating' => 4,
        ]);

        // Book4 → 平均4.0
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book4->id,
            'rating' => 5,
        ]);

        Review::factory()->create([
            'user_id' => $user2->id,
            'book_id' => $book4->id,
            'rating' => 3,
        ]);

        // Book5 → 平均4.0
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book5->id,
            'rating' => 4,
        ]);

        // Book6 → 平均3.5
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book6->id,
            'rating' => 4,
        ]);

        Review::factory()->create([
            'user_id' => $user2->id,
            'book_id' => $book6->id,
            'rating' => 3,
        ]);

        // LowRatedBook → 平均3.0
        Review::factory()->create([
            'user_id' => $user3->id,
            'book_id' => $lowRatedBook->id,
            'rating' => 3,
        ]);

        $response = $this->actingAs($user)
            ->get('/reports');

        $response->assertOk()
            ->assertSeeInOrder([
                $book1->title,
                $book2->title,
            ])
            ->assertSee($book3->title)
            ->assertSee($book4->title)
            ->assertSee($book5->title)
            ->assertDontSee($book6->title)
            ->assertDontSee($lowRatedBook->title)
            ->assertSee(route('books.show', $book1))
            ->assertSee(route('books.show', $book2))
            ->assertSee(route('books.show', $book3))
            ->assertSee(route('books.show', $book4))
            ->assertSee(route('books.show', $book5))
            ->assertDontSee(route('books.show', $book6));
    }

    // ジャンル別評価傾向TOP5が平均評価の高い順に最大5件、ジャンル詳細リンク付きで表示される
    public function test_reading_report_displays_top_5_genres_in_average_rating_order(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $genre1 = Genre::factory()->create(['name' => '評価5のジャンル']);
        $genre2 = Genre::factory()->create(['name' => '評価4.5のジャンル']);
        $genre3 = Genre::factory()->create(['name' => '評価4のジャンル']);
        $genre4 = Genre::factory()->create(['name' => '評価4のジャンル2']);
        $genre5 = Genre::factory()->create(['name' => '評価4のジャンル3']);
        $genre6 = Genre::factory()->create(['name' => '評価3.5のジャンル']);

        $book1 = Book::factory()->create();
        $book2 = Book::factory()->create();
        $book3 = Book::factory()->create();
        $book4 = Book::factory()->create();
        $book5 = Book::factory()->create();
        $book6 = Book::factory()->create();

        $book1->genres()->attach($genre1);
        $book2->genres()->attach($genre2);
        $book3->genres()->attach($genre3);
        $book4->genres()->attach($genre4);
        $book5->genres()->attach($genre5);
        $book6->genres()->attach($genre6);

        // genre1 → 平均5.0
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book1->id,
            'rating' => 5,
        ]);

        // genre2 → 平均4.5
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book2->id,
            'rating' => 5,
        ]);

        Review::factory()->create([
            'user_id' => $user2->id,
            'book_id' => $book2->id,
            'rating' => 4,
        ]);

        // genre3 → 平均4.0
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book3->id,
            'rating' => 4,
        ]);

        // genre4 → 平均4.0
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book4->id,
            'rating' => 5,
        ]);

        Review::factory()->create([
            'user_id' => $user2->id,
            'book_id' => $book4->id,
            'rating' => 3,
        ]);

        // genre5 → 平均4.0
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book5->id,
            'rating' => 4,
        ]);

        // genre6 → 平均3.5
        Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book6->id,
            'rating' => 4,
        ]);

        Review::factory()->create([
            'user_id' => $user2->id,
            'book_id' => $book6->id,
            'rating' => 3,
        ]);

        $response = $this->actingAs($user)
            ->get('/reports');

        $response->assertOk()
            ->assertSeeInOrder([
                $genre1->name,
                $genre2->name,
                $genre3->name,
            ])
            ->assertSee($genre4->name)
            ->assertSee($genre5->name)
            ->assertDontSee($genre6->name)
            ->assertSee(route('genres.show', $genre1))
            ->assertSee(route('genres.show', $genre2))
            ->assertSee(route('genres.show', $genre3))
            ->assertSee(route('genres.show', $genre4))
            ->assertSee(route('genres.show', $genre5))
            ->assertDontSee(route('genres.show', $genre6));
    }
}
