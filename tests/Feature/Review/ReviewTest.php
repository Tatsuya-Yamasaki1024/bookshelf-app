<?php

namespace Tests\Feature\Review;

use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    // ログインユーザーがレビューを投稿できることを確認する。
    // 投稿したレビューが自身のuser_idで登録され、書籍詳細画面にレビューが増えていることを確認する。
    public function test_authenticated_user_can_create_review(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $book->genres()->attach($genre);

        $response = $this->actingAs($user)->post(
            route('reviews.store', $book),
            [
                'rating' => '3',
                'comment' => 'テスト投稿',
            ]
        );

        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 3,
            'comment' => 'テスト投稿',
        ]);

        $response = $this->actingAs($user)->get(
            route('books.show', $book)
        );
        $response->assertSee('テスト投稿');
        $response->assertSee('3');
    }

    // ログインユーザーが自身のレビューを編集できることを確認する。
    // 編集したレビューの内容が反映されていることを確認する。
    public function test_authenticated_user_can_update_own_review(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $book->genres()->attach($genre);

        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put(
            route('reviews.update', $review),
            [
                'rating' => '3',
                'comment' => 'テスト投稿更新',
            ]
        );

        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 3,
            'comment' => 'テスト投稿更新',
        ]);
    }

    // ログインユーザーが自身のレビューを削除できることを確認する。
    // 削除したレビューが書籍詳細画面から消えていることを確認する。
    public function test_authenticated_user_can_delete_own_review(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $book->genres()->attach($genre);

        $review = Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'comment' => '削除するレビュー',
        ]);

        $response = $this->actingAs($user)->delete(
            route('reviews.destroy', $review),
        );

        $response->assertRedirect(route('books.show', $book));
        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);

        $response = $this->actingAs($user)->get(
            route('books.show', $book)
        );
        $response->assertDontSee('削除するレビュー');
    }

    // 一人のユーザーが同じ書籍に複数のレビューを投稿できないことを確認する。
    public function test_user_cannot_create_multiple_reviews_for_same_book(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $book->genres()->attach($genre);

        Review::factory()->create([
            'book_id' => $book->id,
            'user_id' => $user->id,
            'rating' => '3',
            'comment' => 'テスト投稿',
        ]);

        $response = $this->actingAs($user)->post(
            route('reviews.store', $book),
            [
                'rating' => '1',
                'comment' => 'テスト再投稿',
            ]
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 3,
            'comment' => 'テスト投稿',
        ]);

        $this->assertDatabaseMissing('reviews', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => 1,
            'comment' => 'テスト再投稿',
        ]);
    }
}
