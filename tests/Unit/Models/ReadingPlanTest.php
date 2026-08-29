<?php

namespace Tests\Unit\Models;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanTest extends TestCase
{
    use RefreshDatabase;

    // ReadingPlan→User
    public function test_reading_plan_belongs_to_user(): void
    {
        $user = User::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $readingPlan->user);
        $this->assertEquals($user->id, $readingPlan->user->id);
    }

    // ReadingPlan→Book
    public function test_reading_plan_belongs_to_book(): void
    {
        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'book_id' => $book->id,
        ]);

        $this->assertInstanceOf(Book::class, $readingPlan->book);
        $this->assertEquals($book->id, $readingPlan->book->id);
    }
}
