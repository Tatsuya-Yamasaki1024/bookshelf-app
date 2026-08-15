<?php

namespace Tests\Feature\Authentication\Favorite;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteAccessTest extends TestCase
{
    use RefreshDatabase;

    // ゲストはお気に入り一覧画面へアクセスすると、ログイン画面へ遷移される
    public function test_guest_cannot_access_favorite_index_page(): void
    {
        $response = $this->get(
            route('favorites.index'),
        );

        $response->assertRedirect(route('login'));
    }

    // ゲストは書籍をお気に入りしようとすると、ログイン画面へ遷移される
    public function test_guest_cannot_favorite_book(): void
    {
        $book = Book::factory()->create();

        $response = $this->post(
            route('favorites.toggle', $book),
        );

        $response->assertRedirect(route('login'));
    }
}
