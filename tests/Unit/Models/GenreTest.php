<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use RefreshDatabase;

    // Genre → Book
    public function test_genre_belongs_to_many_books(): void
    {
        $genre = Genre::factory()->create();

        $book1 = Book::factory()
            ->hasAttached($genre)
            ->create();

        Book::factory()->create();

        $this->assertCount(1, $genre->fresh()->books);
        $this->assertTrue($genre->books->first()->is($book1));
    }
}
