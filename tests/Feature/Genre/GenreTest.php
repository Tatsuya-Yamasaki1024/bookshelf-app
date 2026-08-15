<?php

namespace Tests\Feature\Genre;

use App\Models\Book;
use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    // ジャンル名を押すとジャンル詳細画面へ遷移し、そのジャンルに紐づく書籍が10件ごとに表示される
    public function test_genre_name_redirects_to_detail_and_displays_books_paginated(): void
    {
        $user = User::factory()->create();

        $genre = Genre::factory()->create();
        $otherGenre = Genre::factory()->create();

        // 対象ジャンルに紐づく書籍を13冊作成
        $books = Book::factory()
            ->count(13)
            ->hasAttached($genre)
            ->create();

        // 対象ジャンルに紐付かない書籍を3冊作成
        $otherBooks = Book::factory()
            ->count(3)
            ->hasAttached($otherGenre)
            ->create();

        // ジャンル詳細画面にアクセス
        $response = $this->actingAs($user)->get(
            route('genres.show', $genre)
        );

        $response->assertStatus(200);

        // 1ページ目に10冊表示され、全13冊が対象であることを確認
        $response->assertViewHas('books', function ($books) {
            return $books->count() === 10
                && $books->total() === 13;
        });

        // 2ページ目にアクセス

        $response = $this->actingAs($user)->get(
            route('genres.show', $genre).'?page=2'
        );

        // 2ページ目に残り3冊が表示されることを確認
        $response->assertViewHas('books', function ($books) {
            return $books->count() === 3
                && $books->total() === 13;
        });

        // 2ページ目でも対象外の書籍が表示されていないことを確認
        $response->assertDontSee($otherBooks->first()->title);
    }

    // ログインユーザーがジャンルを登録でき、ジャンル一覧画面にそのジャンルが追加される
    public function test_authenticated_user_can_create_genre_and_genre_appears_in_index(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/genres', [
            'name' => 'テストジャンル',
        ]);

        $response->assertRedirect(route('genres.index'));
        $this->assertDatabaseHas('genres', [
            'name' => 'テストジャンル',
        ]);

        $response = $this->actingAs($user)->get(
            route('genres.index')
        );
        $response->assertSee('テストジャンル');
    }

    // ログインユーザーがジャンルを削除でき、ジャンル一覧画面へ遷移し、そのジャンルが一覧から削除される
    public function test_authenticated_user_can_delete_genre_and_redirects_to_index(): void
    {
        $user = User::factory()->create();

        $genre = Genre::factory()->create([
            'name' => '削除用ジャンル',
        ]);

        $response = $this->actingAs($user)->delete(
            route('genres.destroy', $genre),
        );

        $response->assertRedirect(route('genres.index'));

        $this->assertDatabaseMissing('genres', [
            'id' => $genre->id,
            'name' => '削除用ジャンル',
        ]);

        $response = $this->actingAs($user)->get(
            route('genres.index'),
        );
        $response->assertDontSee('削除用ジャンル');
    }

    // 書籍が紐付いているジャンルは削除できない
    public function test_genre_with_books_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $genre = Genre::factory()->create([
            'name' => '削除不可ジャンル',
        ]);

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $book->genres()->attach($genre);

        $response = $this->actingAs($user)->delete(
            route('genres.destroy', $genre),
        );

        $response->assertRedirect(route('genres.index'));
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => '削除不可ジャンル',
        ]);

        $this->assertDatabaseHas('book_genre', [
            'book_id' => $book->id,
            'genre_id' => $genre->id,
        ]);

        $response->assertSessionHas(
            'error',
            'このジャンルは書籍に紐付いているため削除できません。'
        );
    }
}
