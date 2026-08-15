<?php

namespace Tests\Feature\Authentication\Book;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookAccessTest extends TestCase
{
    use RefreshDatabase;

    // ゲストが書籍登録画面にアクセスできず、ログイン画面にリダイレクトされる
    public function test_guest_cannot_access_book_create_page(): void
    {
        $response = $this->get(
            route('books.create'),
        );

        $response->assertRedirect(route('login'));
    }

    // ログインユーザーが他人の書籍を編集しようとすると拒否される
    public function test_authenticated_user_cannot_edit_another_users_book(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->get(
            route('books.edit', $book),
        );

        $response->assertForbidden();
    }

    // ログインユーザーが他人の書籍を削除しようとすると拒否される
    public function test_authenticated_user_cannot_delete_another_users_book(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->delete(
            route('books.destroy', $book),
        );

        $response->assertForbidden();

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
        ]);
    }
}
