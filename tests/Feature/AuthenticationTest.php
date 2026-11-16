<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authentication_screens_render(): void
    {
        foreach (['/login', '/register', '/forgot-password', '/reset-password/test-token?email=test@example.test'] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_registration_hashes_password_and_ignores_role_or_status_injection(): void
    {
        Notification::fake();
        $this->post('/register', [
            'name' => 'New Teammate', 'username' => 'new_teammate',
            'email' => 'NEW@example.test',
            'password' => 'secure-password', 'password_confirmation' => 'secure-password',
            'role' => 'Administrator', 'status' => 'Inactive',
        ])->assertSessionHasNoErrors()->assertRedirect(route('tasks.index'));
        $user = User::where('email', 'new@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('secure-password', $user->password));
        $this->assertSame('Staff', $user->role);
        $this->assertSame('Active', $user->status);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_registration_rejects_duplicate_identity_and_unconfirmed_password(): void
    {
        User::factory()->create(['email' => 'used@example.test', 'username' => 'used_name']);
        $this->post('/register', [
            'name' => '', 'username' => 'used_name', 'email' => 'used@example.test',
            'password' => 'short', 'password_confirmation' => 'different',
        ])->assertSessionHasErrors(['name', 'username', 'email', 'password']);
        $this->assertDatabaseCount('users', 1);
        $this->assertGuest();
    }

    public function test_active_user_can_login_with_normalized_email_and_logout(): void
    {
        $user = User::factory()->create(['email' => 'login@example.test']);
        $this->post('/login', ['email' => ' LOGIN@example.test ', 'password' => 'test-password'])
            ->assertSessionHasNoErrors()->assertRedirect(route('tasks.index'));
        $this->assertAuthenticatedAs($user);
        $this->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_invalid_password_and_inactive_account_cannot_login(): void
    {
        $user = User::factory()->create();
        $this->post('/login', ['email' => $user->email, 'password' => 'incorrect'])
            ->assertSessionHasErrors('email');
        $user->update(['status' => 'Inactive']);
        $this->post('/login', ['email' => $user->email, 'password' => 'test-password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_is_throttled_after_five_failed_attempts(): void
    {
        $user = User::factory()->create();
        $key = $user->email.'|127.0.0.1';
        RateLimiter::clear($key);
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'incorrect'])
                ->assertSessionHasErrors('email');
        }
        $this->post('/login', ['email' => $user->email, 'password' => 'test-password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertTrue(RateLimiter::tooManyAttempts($key, 5));
    }

    public function test_password_reset_link_is_generated_for_an_existing_user(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $this->post('/forgot-password', ['email' => $user->email])->assertSessionHas('status');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_reset_accepts_a_valid_token_and_rejects_an_invalid_one(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);
        $payload = [
            'token' => $token, 'email' => $user->email,
            'password' => 'replacement-password', 'password_confirmation' => 'replacement-password',
        ];
        $this->post('/reset-password', array_replace($payload, ['token' => 'invalid-token']))
            ->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('test-password', $user->fresh()->password));
        $this->post('/reset-password', $payload)->assertSessionHasNoErrors()->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('replacement-password', $user->fresh()->password));
    }

    public function test_email_verification_requires_a_valid_signed_link(): void
    {
        $user = User::factory()->create(['email_verified_at' => null]);
        $this->actingAs($user)->get('/verify-email')->assertOk();
        $this->get("/verify-email/{$user->id}/".sha1($user->email))->assertForbidden();
        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(30), [
            'id' => $user->id, 'hash' => sha1($user->email),
        ]);
        $this->get($url)->assertRedirect(route('tasks.index'));
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_unverified_logged_in_user_can_manage_tasks_as_required(): void
    {
        $this->actingAs(User::factory()->create(['email_verified_at' => null]))
            ->get('/tasks/create')->assertOk();
    }
}
