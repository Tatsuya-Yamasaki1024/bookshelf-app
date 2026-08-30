<?php

namespace Tests\Feature\Apply\ReadingPlan;

use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Tests\TestCase;

class ReadingPlanNotificationTest extends TestCase
{
    use RefreshDatabase;

    // 読書計画を削除すると、その読書計画に紐づく通知も削除される
    public function test_deleting_reading_plan_also_deletes_related_notifications(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $plan = ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => now()->addDays(3)->toDateString(),
            'status' => 'in_progress',
        ]);

        $notification = DatabaseNotification::create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\ReadingPlanReminder',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => [
                'timing' => 'three_days_before',
                'title' => '読書計画の期限が近づいています。',
                'body' => 'テスト通知',
                'reading_plan_id' => $plan->id,
                'book_id' => $book->id,
            ],
        ]);

        $response = $this
            ->actingAs($user)
            ->delete(route('reading-plans.destroy', $plan));

        $response->assertRedirect(route('reading-plans.index'));

        $this->assertDatabaseMissing('reading_plans', [
            'id' => $plan->id,
        ]);

        $this->assertDatabaseMissing('notifications', [
            'id' => $notification->id,
        ]);
    }

    // 読書計画を読了にすると、その読書計画に紐づく通知も削除される
    public function test_completing_reading_plan_also_deletes_related_notifications(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $plan = ReadingPlan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => now()->addDays(3)->toDateString(),
            'status' => 'in_progress',
        ]);

        $notification = DatabaseNotification::create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\ReadingPlanReminder',
            'notifiable_type' => User::class,
            'notifiable_id' => $user->id,
            'data' => [
                'timing' => 'three_days_before',
                'title' => '読書計画の期限が近づいています。',
                'body' => 'テスト通知',
                'reading_plan_id' => $plan->id,
                'book_id' => $book->id,
            ],
        ]);

        $response = $this
            ->actingAs($user)
            ->post(route('reading-plans.complete', $plan));

        $response->assertRedirect();

        $this->assertDatabaseHas('reading_plans', [
            'id' => $plan->id,
            'status' => 'completed',
        ]);

        $this->assertDatabaseMissing('notifications', [
            'id' => $notification->id,
        ]);
    }
}
