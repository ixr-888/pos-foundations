<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Database\Seeders\SampleAccounts;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SeedersTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeders_populate_customers_users_and_tasks_without_email(): void
    {
        Mail::fake();
        $this->seed();
        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('users', 5);
        $this->assertDatabaseCount('tasks', 5);
        foreach (SampleAccounts::customers() as $record) {
            $this->assertDatabaseHas('customers', $record);
        }
        foreach (SampleAccounts::users() as $record) {
            $this->assertDatabaseHas('users', $record);
        }
        $this->get('/tasks')->assertOk()->assertSee('Open the counter');
        Mail::assertNothingSent();
    }

    public function test_reseeding_preserves_passwords_task_edits_and_archive_flags(): void
    {
        $this->seed();
        $password = User::findOrFail(2001)->password;
        $task = Task::where('title', 'Open the counter')->firstOrFail();
        $task->description = 'Edited description';
        $task->title = 'Renamed sample task';
        $task->is_archived = true;
        $task->save();

        $this->seed();
        $this->assertDatabaseCount('customers', 5);
        $this->assertDatabaseCount('users', 5);
        $this->assertDatabaseCount('tasks', 5);
        $this->assertSame($password, User::findOrFail(2001)->password);
        $this->assertSame('Edited description', $task->fresh()->description);
        $this->assertTrue($task->fresh()->is_archived);
    }
}
