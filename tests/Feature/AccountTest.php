<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_settings_require_authentication(): void
    {
        $this->get('/account')->assertRedirect(route('login'));
        $this->patch('/account', [])->assertRedirect(route('login'));
        $this->delete('/account', [])->assertRedirect(route('login'));
    }

    public function test_details_can_be_saved_without_failing_own_unique_values(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/account')->assertOk();
        $this->patch('/account', [
            'name' => 'Updated name', 'username' => $user->username, 'email' => $user->email,
            'role' => 'Administrator',
        ])->assertSessionHasNoErrors()->assertRedirect(route('account.edit'));
        $this->assertSame('Updated name', $user->fresh()->name);
        $this->assertSame('Staff', $user->fresh()->role);
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_email_change_clears_verification_and_duplicate_email_is_rejected(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user)->patch('/account', [
            'name' => $user->name, 'username' => $user->username, 'email' => $other->email,
        ])->assertSessionHasErrors('email');
        $this->patch('/account', [
            'name' => $user->name, 'username' => $user->username, 'email' => 'changed@example.test',
        ])->assertSessionHasNoErrors();
        $this->assertSame('changed@example.test', $user->fresh()->email);
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_password_change_requires_current_password(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->from('/account')->put('/password', [
            'current_password' => 'wrong', 'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSessionHasErrorsIn('updatePassword', 'current_password');
        $this->put('/password', [
            'current_password' => 'test-password', 'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_deleting_account_requires_password_and_retains_shared_tasks(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['created_by' => $user->id]);
        $this->actingAs($user)->delete('/account', ['password' => 'wrong'])
            ->assertSessionHasErrorsIn('userDeletion', 'password');
        $this->assertModelExists($user);
        $this->delete('/account', ['password' => 'test-password'])->assertRedirect(route('home'));
        $this->assertGuest();
        $this->assertModelMissing($user);
        $this->assertModelExists($task);
        $this->assertNull($task->fresh()->created_by);
        $this->get(route('tasks.show', $task))->assertOk()->assertSee('Former team member');
    }
}
