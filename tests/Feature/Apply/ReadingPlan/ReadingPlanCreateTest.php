<?php

namespace Tests\Feature\Apply\ReadingPlan;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class ReadingPlanCreateTest extends TestCase
{
    use RefreshDatabase;

    // ゲストがアクセスするとログイン画面に遷移する
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('reading-plans.create'));

        $response->assertRedirect(route('login'));
    }

    // ログインユーザーがアクセスできる
    public function test_authenticated_user_can_access_reading_plan_create_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('reading-plans.create'));

        $response->assertOk();
    }

    // 書籍と期日を選択して読書計画を作成できる
    public function test_authenticated_user_can_create_a_reading_plan(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $targetDate = Carbon::today()->addDays(7);

        $response = $this->actingAs($user)
            ->post(route('reading-plans.store'), [
                'book_id' => $book->id,
                'target_date' => $targetDate->toDateString(),
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'target_date' => $targetDate->startOfDay()->toDateTimeString(),
            'status' => ReadingPlanStatus::InProgress->value,
        ]);
    }

    // 書籍が未選択の場合、バリデーションエラーになる
    public function test_book_id_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('reading-plans.store'), [
                'book_id' => '',
                'target_date' => Carbon::today()->addDays(7)->toDateString(),
            ]);

        $response->assertSessionHasErrors([
            'book_id' => '書籍を選択してください。',
        ]);
    }

    // 同じ書籍の進行中の読書計画が存在する場合、登録できない
    public function test_cannot_create_duplicate_in_progress_reading_plan(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
        ]);

        $response = $this->actingAs($user)
            ->post(route('reading-plans.store'), [
                'book_id' => $book->id,
                'target_date' => Carbon::today()->addDays(7)->toDateString(),
            ]);

        $response->assertSessionHasErrors([
            'book_id' => 'この書籍には進行中の読書計画が既に存在します。',
        ]);
    }

    // 読了・期限切れの読書計画が存在していても、同じ書籍の読書計画を再作成できる
    public function test_can_create_reading_plan_when_existing_plan_is_completed_or_expired(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::Completed,
        ]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::Expired,
        ]);


        $targetDate = Carbon::today()->addDays(7);

        $response = $this->actingAs($user)
            ->post(route('reading-plans.store'), [
                'book_id' => $book->id,
                'target_date' => $targetDate->toDateString(),
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('reading_plans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress->value,
        ]);
    }


    // 期日が未入力の場合、バリデーションエラーになる
    public function test_target_date_is_required(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('reading-plans.store'), [
                'book_id' => $book->id,
                'target_date' => '',
            ]);

        $response->assertSessionHasErrors([
            'target_date' => '期日を入力してください。',
        ]);
    }

    // 期日が正しい形式でない場合、バリデーションエラーになる
    public function test_target_date_must_be_a_valid_date(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $response = $this->actingAs($user)
            ->post(route('reading-plans.store'), [
                'book_id' => $book->id,
                'target_date' => 'errordate',
            ]);

        $response->assertSessionHasErrors([
            'target_date' => '期日は正しい日付を入力してください。',
        ]);
    }

    // 期日が過去の日付の場合、バリデーションエラーになる
    public function test_target_date_cannot_be_in_the_past(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create();

        $targetDate = Carbon::yesterday()->toDateString();

        $response = $this->actingAs($user)
            ->post(route('reading-plans.store'), [
                'book_id' => $book->id,
                'target_date' => $targetDate,
            ]);

        $response->assertSessionHasErrors([
            'target_date' => '期日は今日以降の日付を指定してください。',
        ]);
    }
}
