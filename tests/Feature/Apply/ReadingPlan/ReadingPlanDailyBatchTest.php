<?php

namespace Tests\Feature\Apply\ReadingPlan;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanDailyBatchTest extends TestCase
{
    use RefreshDatabase;

    // 期日を過ぎた in_progress の計画が expired に変更される
    public function test_expired_reading_plan_is_automatically_changed_to_expired(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 9, 1));

        $user = User::factory()->create();
        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => Carbon::create(2026, 8, 31),
            'status' => ReadingPlanStatus::InProgress,
        ]);

        $this->artisan('reading-plans:process-reminders')
            ->assertSuccessful();

        $this->assertDatabaseHas('reading_plans', [
            'id' => $readingPlan->id,
            'status' => ReadingPlanStatus::Expired->value,
        ]);

        Carbon::setTestNow();
    }

    // 期日当日の in_progress の計画は expired に変更されない
    public function test_in_progress_reading_plan_on_target_date_is_not_expired(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 9, 1));

        $user = User::factory()->create();
        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => Carbon::create(2026, 9, 1),
            'status' => ReadingPlanStatus::InProgress,
        ]);

        $this->artisan('reading-plans:process-reminders')
            ->assertSuccessful();

        $this->assertDatabaseHas('reading_plans', [
            'id' => $readingPlan->id,
            'status' => ReadingPlanStatus::InProgress->value,
        ]);

        Carbon::setTestNow();
    }

    // completed の計画は期日を過ぎても expired に変更されない
    public function test_completed_reading_plan_is_not_changed_to_expired(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 9, 1));

        $user = User::factory()->create();
        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => Carbon::create(2026, 8, 31),
            'status' => ReadingPlanStatus::Completed,
        ]);

        $this->artisan('reading-plans:process-reminders')
            ->assertSuccessful();

        $this->assertDatabaseHas('reading_plans', [
            'id' => $readingPlan->id,
            'status' => ReadingPlanStatus::Completed->value,
        ]);

        Carbon::setTestNow();
    }
}
