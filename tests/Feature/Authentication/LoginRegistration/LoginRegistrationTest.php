<?php

namespace Tests\Feature\Authentication\Registration;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginRegistrationTest extends TestCase
{
    use RefreshDatabase;

    // ゲストが会員登録し、書籍一覧へリダイレクトされる
    public function test_guest_can_register_and_is_redirected_to_books_index(): void
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('books.index'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);
    }

    // ゲストがログインし、書籍一覧へリダイレクトされる
    public function test_guest_can_login_and_is_redirected_to_books_index(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('books.index'));
        $this->assertAuthenticatedAs($user);
    }

    // ログイン済みユーザーがログイン画面へアクセスすると書籍一覧へリダイレクトされる
    public function test_authenticated_user_is_redirected_to_books_index_when_accessing_login_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect(route('books.index'));
    }

    // バリデーション

    // 会員登録時に名前が未入力の場合、バリデーションエラーになることを確認する
    public function test_registration_rejects_empty_name(): void
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'name' => '名前を入力してください。',
        ]);
    }

    // 会員登録時に名前が256文字以上の場合、バリデーションエラーになることを確認する
    public function test_registration_rejects_name_over_255_characters(): void
    {
        $response = $this->post('/register', [
            'name' => str_repeat('あ', 256),
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'name' => '名前は255文字以内で入力してください。',
        ]);
    }

    // 会員登録時にメールアドレスが未入力の場合、バリデーションエラーになることを確認する
    public function test_registration_rejects_empty_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください。',
        ]);
    }

    // 会員登録時にメールアドレスが正しい形式でない場合、バリデーションエラーになることを確認する
    public function test_registration_rejects_invalid_email_format(): void
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'error-email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスは有効なメールアドレス形式で入力してください。',
        ]);
    }

    // 会員登録時に登録済みのメールアドレスを入力した場合、バリデーションエラーになることを確認する
    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'そのメールアドレスは既に登録されています。',
        ]);
    }

    // 会員登録時にパスワードが未入力の場合、バリデーションエラーになることを確認する
    public function test_registration_rejects_empty_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください。',
        ]);
    }

    // 会員登録時にパスワードが8文字未満の場合、バリデーションエラーになることを確認する
    public function test_registration_rejects_password_less_than_8_characters(): void
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください。',
        ]);
    }

    // 会員登録時にパスワード確認が一致しない場合、バリデーションエラーになることを確認する
    public function test_registration_rejects_password_confirmation_mismatch(): void
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードが一致しません。',
        ]);
    }

    // ログイン時にメールアドレスが未入力の場合、バリデーションエラーになることを確認する
    public function test_login_rejects_empty_email(): void
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください。',
        ]);
    }

    // ログイン時にパスワードが未入力の場合、バリデーションエラーになることを確認する
    public function test_login_rejects_empty_password(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください。',
        ]);
    }

    // ログイン時にメールアドレスが間違っている場合、エラーになることを確認する
    public function test_login_rejects_invalid_email(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスまたはパスワードが正しくありません。',
        ]);
    }

    // ログイン時にパスワードが間違っている場合、エラーになることを確認する
    public function test_login_rejects_invalid_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスまたはパスワードが正しくありません。',
        ]);
    }
}
