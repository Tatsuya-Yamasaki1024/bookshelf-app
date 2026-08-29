<?php

namespace Tests\Feature\Apply\ReadingPlan;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanEditTest extends TestCase
{
    use RefreshDatabase;

    // ゲストがアクセスするとログイン画面に遷移する
    public function test_guest_is_redirected_to_login(): void
    {
        $readingPlan = ReadingPlan::factory()->create();

        $response = $this->get(
            route('reading-plans.edit', $readingPlan)
        );

        $response->assertRedirect(route('login'));
    }

    // ログインユーザーがアクセスできる
    public function test_authenticated_user_can_access_reading_plan_edit_page(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('reading-plans.edit', $readingPlan));

        $response->assertOk();
    }

    // 期日を変更して読書計画を更新できる
    public function test_authenticated_user_can_update_reading_plan(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $originalDate = Carbon::today()->addDays(7);
        $targetDate = Carbon::today()->addDays(14);

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => $originalDate,
            'status' => ReadingPlanStatus::InProgress,
        ]);

        $response = $this->actingAs($user)
            ->put(route('reading-plans.update', $readingPlan), [
                'target_date' => $targetDate->toDateString(),
            ]);

        $response->assertRedirect();

        $readingPlan->refresh();

        $this->assertEquals($book->id, $readingPlan->book_id);

        $this->assertEquals(
            $targetDate->toDateString(),
            $readingPlan->target_date->toDateString()
        );
    }

    // 期日が未入力の場合、バリデーションエラーになる
    public function test_target_date_is_required_when_updating_reading_plan(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
            'target_date' => Carbon::today()->addDays(7),
        ]);

        $response = $this->actingAs($user)
            ->put(route('reading-plans.update', $readingPlan), [
                'target_date' => '',
            ]);

        $response->assertSessionHasErrors([
            'target_date' => '期日を入力してください。',
        ]);
    }

    // 期日が正しい形式でない場合、バリデーションエラーになる
    public function test_target_date_must_be_a_valid_date_when_updating_reading_plan(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
            'target_date' => Carbon::today()->addDays(7),
        ]);

        $response = $this->actingAs($user)
            ->put(route('reading-plans.update', $readingPlan), [
                'target_date' => 'invalid-date',
            ]);

        $response->assertSessionHasErrors([
            'target_date' => '期日は正しい日付を入力してください。',
        ]);
    }

    // 編集時に登録時の期日を超過している場合、今日以降の日付に変更しないと更新できない
    public function test_expired_reading_plan_cannot_be_updated_to_a_past_date(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
            'target_date' => Carbon::yesterday(),
        ]);

        $response = $this->actingAs($user)
            ->put(route('reading-plans.update', $readingPlan), [
                'target_date' => Carbon::yesterday()->toDateString(),
            ]);

        $response->assertSessionHasErrors([
            'target_date' => '期日は今日以降の日付を指定してください。',
        ]);
    }

    // 既に進行中の計画がある場合、同じ書籍の期限切れの計画を進行中に戻せない
    public function test_cannot_update_expired_reading_plan_to_in_progress_when_another_in_progress_plan_exists(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        // 既に進行中の計画
        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
            'target_date' => Carbon::today()->addDays(7),
        ]);

        // 編集対象の期限切れ計画
        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::Expired,
            'target_date' => Carbon::yesterday(),
        ]);

        $response = $this->actingAs($user)
            ->put(route('reading-plans.update', $readingPlan), [
                'target_date' => Carbon::today()->addDays(7)->toDateString(),
            ]);

        $response->assertSessionHasErrors([
            'book_id' => 'この書籍には進行中の読書計画が既に存在します。',
        ]);
    }
}
