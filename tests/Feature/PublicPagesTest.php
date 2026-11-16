<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_required_pages_are_public_and_share_navigation(): void
    {
        foreach (['/' => 'A good day starts here.', '/tasks' => 'Tasks for today.', '/profile' => 'Project profile.', '/about' => 'About Counter.'] as $path => $heading) {
            $response = $this->get($path)->assertOk()->assertSee($heading);
            foreach (['home', 'tasks.index', 'profile', 'about'] as $route) {
                $response->assertSee('href="'.route($route).'"', false);
            }
        }
        $this->assertGuest();
    }

    public function test_public_counts_exclude_archived_tasks(): void
    {
        Task::factory()->create(['status' => 'pending']);
        Task::factory()->create(['status' => 'done', 'completed_at' => now()]);
        Task::factory()->create(['status' => 'pending', 'is_archived' => true]);

        $this->get('/')->assertOk()->assertViewHas('taskCount', 2)
            ->assertViewHas('openCount', 1)->assertViewHas('doneCount', 1);
    }

    public function test_guests_do_not_see_task_management_forms(): void
    {
        $task = Task::factory()->create();
        $this->get('/tasks')->assertOk()
            ->assertDontSee(route('tasks.create'), false)
            ->assertDontSee(route('tasks.edit', $task), false)
            ->assertSee('Log in to manage tasks');
        $this->get(route('tasks.show', $task))->assertOk()->assertSee($task->title);
    }

    public function test_store_directories_require_login_and_use_database_records(): void
    {
        foreach (['/customers', '/users'] as $path) {
            $this->get($path)->assertRedirect(route('login'));
        }
        $user = User::factory()->create(['name' => 'Database User']);
        $this->actingAs($user)->get('/users')->assertOk()->assertSee('Database User');
        $this->get('/customers')->assertOk();
    }

    public function test_unknown_pages_and_tasks_return_404(): void
    {
        $this->get('/missing-page')->assertNotFound();
        $this->get('/tasks/999999')->assertNotFound();
    }
}
