<?php

namespace Tests\Feature\Authentication\Genre;

use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreAccessTest extends TestCase
{
    use RefreshDatabase;

    // ゲストはジャンル一覧画面にアクセスできず、ログイン画面へ遷移される
    public function test_guest_cannot_access_genre_index_page(): void
    {
        $response = $this->get(
            route('genres.index'),
        );

        $response->assertRedirect(route('login'));
    }

    // ゲストはジャンル登録画面にアクセスできず、ログイン画面へ遷移される
    public function test_guest_cannot_access_genre_create_page(): void
    {
        $response = $this->get(
            route('genres.create'),
        );

        $response->assertRedirect(route('login'));
    }

    // ゲストはジャンル編集画面にアクセスできず、ログイン画面へ遷移される
    public function test_guest_cannot_access_genre_edit_page(): void
    {
        $genre = Genre::factory()->create();

        $response = $this->get(
            route('genres.edit', $genre),
        );

        $response->assertRedirect(route('login'));
    }
}
