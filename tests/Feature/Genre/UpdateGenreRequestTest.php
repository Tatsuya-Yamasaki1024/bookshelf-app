<?php

namespace Tests\Feature\Genre;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateGenreRequestTest extends TestCase
{
    use RefreshDatabase;

    // ジャンル更新時にジャンル名が未入力の場合、バリデーションエラーになることを確認する。
    public function test_update_genre_rejects_empty_name(): void
    {
        $user = User::factory()->create();

        $genre = Genre::factory()->create([
            'name' => '更新前ジャンル',
        ]);

        $response = $this->actingAs($user)->put(
            route('genres.update', $genre),
            [
                'name' => '',
            ]
        );

        $response->assertSessionHasErrors([
            'name' => 'ジャンル名を入力してください。',
        ]);

        // ジャンル名が更新されていないことを確認
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => '更新前ジャンル',
        ]);
    }

    // ジャンル更新時にジャンル名が255文字を超えた場合、バリデーションエラーになることを確認する。
    public function test_update_genre_rejects_name_exceeding_max_length(): void
    {
        $user = User::factory()->create();

        $genre = Genre::factory()->create([
            'name' => '更新前ジャンル',
        ]);

        $response = $this->actingAs($user)->put(
            route('genres.update', $genre),
            [
                'name' => str_repeat('あ', 256),
            ]
        );

        $response->assertSessionHasErrors([
            'name' => 'ジャンル名は255文字以内で入力してください。',
        ]);

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => '更新前ジャンル',
        ]);
    }

    // ジャンル更新時に既に存在するジャンル名を入力した場合、バリデーションエラーになることを確認する。
    public function test_update_genre_rejects_duplicate_name(): void
    {
        $user = User::factory()->create();

        $genre = Genre::factory()->create([
            'name' => '更新対象ジャンル',
        ]);

        Genre::factory()->create([
            'name' => '既存ジャンル',
        ]);

        $response = $this->actingAs($user)->put(
            route('genres.update', $genre),
            [
                'name' => '既存ジャンル',
            ]
        );

        $response->assertSessionHasErrors([
            'name' => 'そのジャンルは既に登録されています。',
        ]);

        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => '更新対象ジャンル',
        ]);
    }

    // ジャンル更新時に更新前と同じジャンル名を入力した場合、バリデーションエラーにならないことを確認する。
    public function test_update_genre_allows_unchanged_name(): void
    {
        $user = User::factory()->create();

        $genre = Genre::factory()->create([
            'name' => '更新対象ジャンル',
        ]);

        $response = $this->actingAs($user)->put(
            route('genres.update', $genre),
            [
                'name' => '更新対象ジャンル',
            ]
        );

        $response->assertSessionDoesntHaveErrors();
        $this->assertDatabaseHas('genres', [
            'id' => $genre->id,
            'name' => '更新対象ジャンル',
        ]);
    }
}
