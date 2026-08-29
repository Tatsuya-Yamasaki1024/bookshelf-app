<?php

namespace Tests\Feature\Apply\Notification;

use App\Models\User;
use App\Notifications\ReadingPlanReminder;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class NotificationTest extends TestCase
{
    use RefreshDatabase;

    // 自分の通知を既読にできる
    public function test_authenticated_user_can_mark_own_notification_as_read(): void
    {
        $user = User::factory()->create();

        $notification = $user->notifications()->create([
            'id' => Str::uuid(),
            'type' => ReadingPlanReminder::class,
            'data' => json_encode([
                'message' => '読書計画の期限が近づいています。',
            ]),
            'read_at' => null,
        ]);

        $response = $this->actingAs($user)
            ->post(route('notifications.read', $notification));

        $response->assertRedirect();

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
        ]);

        $this->assertNotNull(
            $notification->fresh()->read_at
        );
    }

    // 他人の通知は既読にできない
    public function test_authenticated_user_cannot_mark_another_users_notification_as_read(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $notification = $otherUser->notifications()->create([
            'id' => Str::uuid(),
            'type' => ReadingPlanReminder::class,
            'data' => json_encode([
                'message' => '読書計画の期限が近づいています。',
            ]),
            'read_at' => null,
        ]);

        $response = $this->actingAs($user)
            ->post(route('notifications.read', $notification));

        $response = $this->actingAs($user)
            ->post(route('notifications.read', $notification));

        $response->assertNotFound();

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'read_at' => null,
        ]);
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'read_at' => null,
        ]);
    }
}
