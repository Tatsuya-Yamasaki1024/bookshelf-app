<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_favorite_belongs_to_book(): void
    {
        $book = Book::factory()->create();

        $favorite = Favorite::factory()->create([
            'book_id' => $book->id,
        ]);

        $this->assertTrue($favorite->book->is($book));
    }

    public function test_favorite_belongs_to_user(): void
    {
        $user = User::factory()->create();

        $favorite = Favorite::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertTrue($favorite->user->is($user));
    }
}
