<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TaskCrudTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'title' => 'Check the register',
            'description' => 'Count the opening float.',
            'due_date' => today()->toDateString(),
            'priority' => 'high',
            'status' => 'pending',
        ], $overrides);
    }

    public function test_all_management_endpoints_reject_guests_without_mutating_data(): void
    {
        Mail::fake();
        $task = Task::factory()->create(['title' => 'Original task']);
        $this->get('/tasks/create')->assertRedirect(route('login'));
        $this->post('/tasks', $this->payload())->assertRedirect(route('login'));
        $this->get("/tasks/{$task->id}/edit")->assertRedirect(route('login'));
        $this->put("/tasks/{$task->id}", $this->payload(['status' => 'done']))->assertRedirect(route('login'));
        $this->patch("/tasks/{$task->id}", $this->payload())->assertRedirect(route('login'));
        $this->delete("/tasks/{$task->id}")->assertRedirect(route('login'));
        $this->assertDatabaseCount('tasks', 1);
        $this->assertSame('Original task', $task->fresh()->title);
        $this->assertFalse($task->fresh()->is_archived);
        Mail::assertNothingSent();
    }

    public function test_logged_in_users_can_open_create_and_edit_forms(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create();
        $this->actingAs($user)->get('/tasks/create')->assertOk()->assertSee('Create a task.');
        $this->get("/tasks/{$task->id}/edit")->assertOk()->assertSee('Edit your task.');
    }

    public function test_create_uses_only_validated_fields_and_sets_server_metadata(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $this->actingAs($user)->post('/tasks', $this->payload([
            'title' => '  Check the register  ',
            'is_archived' => true, 'created_by' => 99999, 'completed_at' => '2000-01-01',
        ]))->assertSessionHasNoErrors()->assertRedirect();
        $task = Task::firstOrFail();
        $this->assertSame('Check the register', $task->title);
        $this->assertSame($user->id, $task->created_by);
        $this->assertFalse($task->is_archived);
        $this->assertNull($task->completed_at);
        Mail::assertNothingSent();
    }

    public function test_optional_description_may_be_empty(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/tasks', $this->payload(['description' => '']))
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseCount('tasks', 1);
    }

    #[DataProvider('invalidTaskData')]
    public function test_invalid_create_is_rejected(array $changes, string $field): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/tasks', $this->payload($changes))->assertSessionHasErrors($field);
        $this->assertDatabaseCount('tasks', 0);
    }

    public static function invalidTaskData(): array
    {
        return [
            'missing title' => [['title' => ''], 'title'],
            'whitespace title' => [['title' => '   '], 'title'],
            'long title' => [['title' => str_repeat('a', 256)], 'title'],
            'array title' => [['title' => ['bad']], 'title'],
            'multiline title' => [['title' => "Title\nSecond line"], 'title'],
            'long description' => [['description' => str_repeat('a', 5001)], 'description'],
            'array description' => [['description' => ['bad']], 'description'],
            'missing date' => [['due_date' => ''], 'due_date'],
            'bad date' => [['due_date' => '2026-02-30'], 'due_date'],
            'wrong date format' => [['due_date' => '16/11/2026'], 'due_date'],
            'bad priority' => [['priority' => 'urgent'], 'priority'],
            'missing priority' => [['priority' => ''], 'priority'],
            'bad status' => [['status' => 'complete'], 'status'],
            'missing status' => [['status' => ''], 'status'],
        ];
    }

    public function test_invalid_update_does_not_change_task_or_send_email(): void
    {
        Mail::fake();
        $task = Task::factory()->create();
        $this->actingAs(User::factory()->create())
            ->put("/tasks/{$task->id}", $this->payload(['title' => '', 'status' => 'done']))
            ->assertSessionHasErrors('title');
        $this->assertSame('pending', $task->fresh()->status);
        $this->assertNull($task->fresh()->completed_at);
        Mail::assertNothingSent();
    }

    public function test_array_input_errors_can_be_redisplayed_without_breaking_the_form(): void
    {
        $this->actingAs(User::factory()->create())->from('/tasks/create')
            ->post('/tasks', $this->payload([
                'title' => ['bad'], 'description' => ['bad'], 'due_date' => ['bad'],
            ]))->assertRedirect('/tasks/create')->assertSessionHasErrors(['title', 'description', 'due_date']);
        $this->get('/tasks/create')->assertOk()->assertSee('Please check the following:');
    }

    public function test_update_changes_all_form_fields_without_email_for_unfinished_status(): void
    {
        Mail::fake();
        $task = Task::factory()->create();
        $this->actingAs(User::factory()->create())->patch("/tasks/{$task->id}", $this->payload([
            'status' => 'in_progress', 'priority' => 'low', 'is_archived' => true,
        ]))->assertSessionHasNoErrors()->assertRedirect(route('tasks.show', $task));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id, 'title' => 'Check the register',
            'description' => 'Count the opening float.', 'priority' => 'low',
            'status' => 'in_progress', 'is_archived' => false,
        ]);
        Mail::assertNothingSent();
    }

    public function test_archive_retains_row_and_hides_it_from_all_active_routes(): void
    {
        Mail::fake();
        $task = Task::factory()->create(['title' => 'Private archived example']);
        $this->actingAs(User::factory()->create())->delete("/tasks/{$task->id}")
            ->assertRedirect(route('tasks.index'));
        $this->assertDatabaseCount('tasks', 1);
        $this->assertTrue($task->fresh()->is_archived);
        $this->get('/tasks')->assertOk()->assertDontSee('Private archived example');
        $this->get("/tasks/{$task->id}")->assertNotFound();
        $this->get("/tasks/{$task->id}/edit")->assertNotFound();
        $this->put("/tasks/{$task->id}", $this->payload())->assertNotFound();
        $this->delete("/tasks/{$task->id}")->assertNotFound();
        Mail::assertNothingSent();
    }

    public function test_search_status_priority_and_today_filters_are_combined(): void
    {
        Task::factory()->create(['title' => 'Count stock', 'status' => 'pending', 'priority' => 'high']);
        Task::factory()->create(['title' => 'Count tomorrow', 'due_date' => today()->addDay(), 'priority' => 'high']);
        Task::factory()->create(['title' => 'Count archived', 'priority' => 'high', 'is_archived' => true]);
        Task::factory()->create(['title' => 'Count finished', 'status' => 'done', 'priority' => 'high']);
        $this->get('/tasks?q=Count&status=pending&priority=high&today=1')
            ->assertOk()->assertSee('Count stock')
            ->assertDontSee('Count tomorrow')->assertDontSee('Count archived')->assertDontSee('Count finished');
    }

    public function test_task_list_is_paginated(): void
    {
        Task::factory()->count(12)->create();
        $this->get('/tasks')->assertOk()
            ->assertViewHas('tasks', fn ($tasks) => $tasks->count() === 10 && $tasks->total() === 12)
            ->assertSee('Next');
        $this->get('/tasks?page=2')->assertOk()->assertViewHas('tasks', fn ($tasks) => $tasks->count() === 2);
    }

    public function test_task_text_is_html_escaped_in_list_and_details(): void
    {
        $script = '<script>alert("xss")</script>';
        $task = Task::factory()->create(['title' => $script, 'description' => $script]);
        foreach (['/tasks', "/tasks/{$task->id}"] as $url) {
            $this->get($url)->assertOk()->assertSee($script)->assertDontSee($script, false);
        }
    }
}
