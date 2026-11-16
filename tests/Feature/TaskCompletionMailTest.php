<?php

namespace Tests\Feature;

use App\Mail\TaskCompleted;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class TaskCompletionMailTest extends TestCase
{
    use RefreshDatabase;

    private function payload(Task $task, array $changes = []): array
    {
        return array_replace([
            'title' => $task->title, 'description' => $task->description,
            'due_date' => $task->due_date->toDateString(),
            'priority' => $task->priority, 'status' => 'done',
        ], $changes);
    }

    #[DataProvider('unfinishedStatuses')]
    public function test_transition_to_done_sends_one_email_to_configured_recipient(string $status): void
    {
        Mail::fake();
        config(['tasks.notification_recipient' => 'assessor@example.test']);
        $task = Task::factory()->create(['status' => $status]);
        $user = User::factory()->create(['name' => 'Task Manager']);
        $this->actingAs($user)->put("/tasks/{$task->id}", $this->payload($task))
            ->assertSessionHasNoErrors()->assertRedirect(route('tasks.show', $task));
        $this->assertSame('done', $task->fresh()->status);
        $this->assertNotNull($task->fresh()->completed_at);
        Mail::assertSent(TaskCompleted::class, fn (TaskCompleted $mail) =>
            $mail->hasTo('assessor@example.test') && $mail->task->id === $task->id
            && $mail->completedBy === 'Task Manager');
        Mail::assertSentCount(1);
    }

    public static function unfinishedStatuses(): array
    {
        return [['pending'], ['in_progress']];
    }

    public function test_two_identical_done_updates_generate_only_one_notification(): void
    {
        Mail::fake();
        $task = Task::factory()->create();
        $this->actingAs(User::factory()->create());
        $payload = $this->payload($task);
        $this->put("/tasks/{$task->id}", $payload)->assertSessionHasNoErrors();
        $completedAt = $task->fresh()->completed_at;
        $this->put("/tasks/{$task->id}", $payload)->assertSessionHasNoErrors();
        Mail::assertSentCount(1);
        $this->assertTrue($completedAt->equalTo($task->fresh()->completed_at));
    }

    public function test_editing_an_already_done_task_does_not_send_or_change_completion_time(): void
    {
        Mail::fake();
        $completedAt = now()->subHour()->startOfSecond();
        $task = Task::factory()->create(['status' => 'done', 'completed_at' => $completedAt]);
        $this->actingAs(User::factory()->create())
            ->put("/tasks/{$task->id}", $this->payload($task, ['title' => 'Updated title']))
            ->assertSessionHasNoErrors();
        $this->assertTrue($completedAt->equalTo($task->fresh()->completed_at));
        Mail::assertNothingSent();
    }

    public function test_reopening_then_completing_is_a_new_transition(): void
    {
        Mail::fake();
        $task = Task::factory()->create();
        $this->actingAs(User::factory()->create());
        $this->put("/tasks/{$task->id}", $this->payload($task))->assertSessionHasNoErrors();
        $this->put("/tasks/{$task->id}", $this->payload($task, ['status' => 'pending']))
            ->assertSessionHasNoErrors();
        $this->assertNull($task->fresh()->completed_at);
        Mail::assertSentCount(1);
        $this->put("/tasks/{$task->id}", $this->payload($task))->assertSessionHasNoErrors();
        Mail::assertSentCount(2);
    }

    public function test_creating_an_already_done_task_does_not_trigger_an_update_email(): void
    {
        Mail::fake();
        $sample = Task::factory()->make();
        $this->actingAs(User::factory()->create())->post('/tasks', $this->payload($sample))
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('done', Task::firstOrFail()->status);
        Mail::assertNothingSent();
    }

    public function test_transport_failure_rolls_back_status_and_shows_retry_message(): void
    {
        $task = Task::factory()->create();
        $user = User::factory()->create();
        config(['tasks.notification_recipient' => 'tasks@example.test']);
        Mail::shouldReceive('to')->once()->with('tasks@example.test')->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new TransportException('Sandbox unavailable'));
        $this->actingAs($user)->from("/tasks/{$task->id}/edit")
            ->put("/tasks/{$task->id}", $this->payload($task))
            ->assertRedirect("/tasks/{$task->id}/edit")->assertSessionHasErrors('notification');
        $this->assertSame('pending', $task->fresh()->status);
        $this->assertNull($task->fresh()->completed_at);
    }

    public function test_bad_recipient_configuration_does_not_silently_complete_task(): void
    {
        Mail::fake();
        config(['tasks.notification_recipient' => '']);
        $task = Task::factory()->create();
        $this->actingAs(User::factory()->create())->put("/tasks/{$task->id}", $this->payload($task))
            ->assertSessionHasErrors('recipient');
        $this->assertSame('pending', $task->fresh()->status);
        Mail::assertNothingSent();
    }

    public function test_mailable_renders_task_details_and_escapes_html(): void
    {
        $task = Task::factory()->create([
            'title' => 'Finish the report',
            'description' => '<script>alert("test")</script>',
            'status' => 'done', 'completed_at' => now(),
        ]);
        $mail = new TaskCompleted($task, 'Test Manager');
        $mail->assertHasSubject('Task completed: Finish the report');
        $mail->assertSeeInHtml('Finish the report');
        $mail->assertSeeInHtml('Test Manager');
        $mail->assertSeeInText('Task #'.$task->id);
        $this->assertStringContainsString(e($task->description), $mail->render());
        $this->assertStringNotContainsString($task->description, $mail->render());
    }

    public function test_real_log_mail_transport_records_one_email_for_two_done_updates(): void
    {
        $path = storage_path('logs/task-mail-test-'.Str::uuid().'.log');
        config([
            'mail.default' => 'log',
            'mail.mailers.log' => ['transport' => 'log', 'channel' => 'task_mail_test'],
            'logging.channels.task_mail_test' => ['driver' => 'single', 'path' => $path, 'level' => 'debug'],
            'tasks.notification_recipient' => 'tasks@example.test',
        ]);
        Mail::purge('log');

        try {
            $task = Task::factory()->create(['title' => 'Log proof task']);
            $this->actingAs(User::factory()->create());
            $this->put("/tasks/{$task->id}", $this->payload($task))->assertSessionHasNoErrors();
            $this->put("/tasks/{$task->id}", $this->payload($task))->assertSessionHasNoErrors();
            $this->assertFileExists($path);
            $log = file_get_contents($path);
            $this->assertSame(1, substr_count($log, 'Subject: Task completed: Log proof task'));
            $this->assertStringContainsString('To: tasks@example.test', $log);
            $this->assertStringContainsString('One more task, completed.', $log);
        } finally {
            Log::forgetChannel('task_mail_test');
            Mail::purge('log');
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
