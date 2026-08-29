<?php

namespace Tests\Feature\Apply\ReadingPlan;

use App\Enums\ReadingPlanStatus;
use App\Models\Book;
use App\Models\ReadingPlan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadingPlanIndexTest extends TestCase
{
    use RefreshDatabase;

    // ゲストがアクセスするとログイン画面に遷移する
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('reading-plans.index'));

        $response->assertRedirect(route('login'));
    }

    // ログインユーザーがアクセスするとそのログインユーザーの読書計画を一覧表示する
    public function test_authenticated_user_can_see_own_reading_plans(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $book1 = Book::factory()->create([
            'title' => '自分の読書計画',
        ]);

        $book2 = Book::factory()->create([
            'title' => '他人の読書計画',
        ]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book1->id,
        ]);

        ReadingPlan::factory()->create([
            'user_id' => $otherUser->id,
            'book_id' => $book2->id,
        ]);

        $response = $this->actingAs($user)
            ->get(route('reading-plans.index'));

        $response->assertOk()
            ->assertSee($book1->title)
            ->assertDontSee($book2->title);
    }

    // 状態の絞り込みをすることで選択した状態の計画のみ表示される
    public function test_reading_plans_can_be_filtered_by_status(): void
    {
        $user = User::factory()->create();

        $inProgressBook = Book::factory()->create([
            'title' => '読書中の本',
        ]);

        $completedBook = Book::factory()->create([
            'title' => '読了した本',
        ]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $inProgressBook->id,
            'status' => ReadingPlanStatus::InProgress,
        ]);

        ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $completedBook->id,
            'status' => ReadingPlanStatus::Completed,
        ]);

        $response = $this->actingAs($user)
            ->get(route('reading-plans.index', [
                'status' => ReadingPlanStatus::InProgress->value,
            ]));

        $response->assertOk()
            ->assertSee($inProgressBook->title)
            ->assertDontSee($completedBook->title);
    }

    // 「読了する」ボタンを押すとその書籍のステータスが「読了」に変化する
    public function test_reading_plan_status_changes_to_completed_when_marked_as_read(): void
    {
        $user = User::factory()->create();

        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
        ]);

        $response = $this->actingAs($user)
            ->post(route('reading-plans.complete', $readingPlan));

        $response->assertRedirect();

        $this->assertDatabaseHas('reading_plans', [
            'id' => $readingPlan->id,
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::Completed->value,
        ]);
    }

    // 所有者じゃないとステータスを「読了」に変更できない
    public function test_user_cannot_mark_another_users_reading_plan_as_completed(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();

        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
            'status' => ReadingPlanStatus::InProgress,
        ]);

        $response = $this->actingAs($user)
            ->post(route('reading-plans.complete', $readingPlan));

        $response->assertForbidden();

        $this->assertDatabaseHas('reading_plans', [
            'id' => $readingPlan->id,
            'user_id' => $owner->id,
            'status' => ReadingPlanStatus::InProgress->value,
        ]);
    }

    // 所有者じゃないと計画を削除できない
    public function test_user_cannot_delete_another_users_reading_plan(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();

        $book = Book::factory()->create();

        $readingPlan = ReadingPlan::factory()->create([
            'user_id' => $owner->id,
            'book_id' => $book->id,
        ]);

        $response = $this->actingAs($user)
            ->delete(route('reading-plans.destroy', $readingPlan));

        $response->assertForbidden();

        $this->assertDatabaseHas('reading_plans', [
            'id' => $readingPlan->id,
            'user_id' => $owner->id,
            'book_id' => $book->id,
        ]);
    }
}
