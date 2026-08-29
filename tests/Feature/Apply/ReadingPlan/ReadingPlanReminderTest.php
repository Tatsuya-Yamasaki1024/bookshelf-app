<?php

namespace Tests\Feature\Apply\ReadingPlan;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use App\Notifications\ReadingPlanReminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class ReadingPlanReminderTest extends TestCase
{
    use RefreshDatabase;

    // 期限3日前 ＋ in_progress の計画にリマインダー通知が送信される
    public function test_sends_reminder_three_days_before_target_date_for_in_progress_plan(): void
    {
        Notification::fake();

        Carbon::setTestNow(Carbon::create(2026, 9, 1));

        $user = User::factory()->create();
        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => Carbon::today()->addDays(3),
            'status' => ReadingPlanStatus::InProgress,
        ]);

        $this->artisan('reading-plans:process-reminders')
            ->assertSuccessful();

        Notification::assertSentTo(
            $user,
            ReadingPlanReminder::class
        );

        Carbon::setTestNow();
    }

    // 期限4日前 ＋ in_progress の計画にはリマインダー通知が送信されない
    public function test_does_not_send_reminder_four_days_before_target_date(): void
    {
        Notification::fake();

        Carbon::setTestNow(Carbon::create(2026, 9, 1));

        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => Carbon::today()->addDays(4),
            'status' => ReadingPlanStatus::InProgress,
        ]);

        $this->artisan('reading-plans:process-reminders')
            ->assertSuccessful();

        Notification::assertNothingSent();

        Carbon::setTestNow();
    }

    // 期限3日前でも completed の計画にはリマインダー通知が送信されない
    public function test_does_not_send_reminder_three_days_before_target_date_for_completed_plan(): void
    {
        Notification::fake();

        Carbon::setTestNow(Carbon::create(2026, 9, 1));

        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => Carbon::today()->addDays(3),
            'status' => ReadingPlanStatus::Completed,
        ]);

        $this->artisan('reading-plans:process-reminders')
            ->assertSuccessful();

        Notification::assertNothingSent();

        Carbon::setTestNow();
    }

    // 期日当日 ＋ in_progress の計画にリマインダー通知が送信される
    public function test_sends_reminder_on_target_date_for_in_progress_plan(): void
    {
        Notification::fake();

        Carbon::setTestNow(Carbon::create(2026, 9, 1));

        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => Carbon::today(),
            'status' => ReadingPlanStatus::InProgress,
        ]);

        $this->artisan('reading-plans:process-reminders')
            ->assertSuccessful();

        Notification::assertSentTo(
            $user,
            ReadingPlanReminder::class
        );

        Carbon::setTestNow();
    }

    // 期日当日でも completed の計画にはリマインダー通知が送信されない
    public function test_does_not_send_reminder_on_target_date_for_completed_plan(): void
    {
        Notification::fake();

        Carbon::setTestNow(Carbon::create(2026, 9, 1));

        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => Carbon::today(),
            'status' => ReadingPlanStatus::Completed,
        ]);

        $this->artisan('reading-plans:process-reminders')
            ->assertSuccessful();

        Notification::assertNothingSent();

        Carbon::setTestNow();
    }

    // 期限3日後 ＋ expired の計画にリマインダー通知が送信される
    public function test_sends_reminder_three_days_after_target_date_for_expired_plan(): void
    {
        Notification::fake();

        Carbon::setTestNow(Carbon::create(2026, 9, 1));

        $user = User::factory()->create();
        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => Carbon::today()->subDays(3),
            'status' => ReadingPlanStatus::Expired,
        ]);

        $this->artisan('reading-plans:process-reminders')
            ->assertSuccessful();

        Notification::assertSentTo(
            $user,
            ReadingPlanReminder::class
        );

        Carbon::setTestNow();
    }
}
