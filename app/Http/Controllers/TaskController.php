<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Mail\TaskCompleted;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(array_keys(Task::STATUSES))],
            'priority' => ['nullable', Rule::in(array_keys(Task::PRIORITIES))],
            'today' => ['nullable', 'in:1'],
        ]);
        $query = Task::active();
        if (isset($filters['q']) && $filters['q'] !== '') {
            $query->where('title', 'like', '%'.$filters['q'].'%');
        }
        foreach (['status', 'priority'] as $field) {
            if (! empty($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
        if (! empty($filters['today'])) {
            $query->whereDate('due_date', today());
        }

        return view('tasks.index', [
            'tasks' => $query->orderBy('due_date')->orderBy('id')->paginate(10)->withQueryString(),
            'filters' => $filters,
            'totalCount' => Task::active()->count(),
            'openCount' => Task::active()->where('status', '!=', 'done')->count(),
            'doneCount' => Task::active()->where('status', 'done')->count(),
        ]);
    }

    public function show(Task $task): View
    {
        abort_if($task->is_archived, 404);

        return view('tasks.show', ['task' => $task->load('creator')]);
    }

    public function create(): View
    {
        return view('tasks.create', ['task' => new Task([
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => today()->toDateString(),
        ])]);
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        $task = new Task($request->validated());
        $task->created_by = $request->user()->id;
        $task->completed_at = $task->status === 'done' ? now() : null;
        $task->save();

        return to_route('tasks.show', $task)->with('success', 'Task created.');
    }

    public function edit(Task $task): View
    {
        abort_if($task->is_archived, 404);

        return view('tasks.edit', compact('task'));
    }

    public function update(TaskRequest $request, Task $task): RedirectResponse
    {
        try {
            $notified = DB::transaction(function () use ($request, $task): bool {
                // Re-read stored status under a lock, including for a stale edit form.
                $current = Task::query()->lockForUpdate()->findOrFail($task->id);
                abort_if($current->is_archived, 404);
                $wasDone = $current->status === 'done';
                $current->fill($request->validated());
                $becameDone = ! $wasDone && $current->status === 'done';
                $current->completed_at = $current->status === 'done'
                    ? ($wasDone ? $current->completed_at : now())
                    : null;
                $current->save();

                if ($becameDone) {
                    $recipient = Validator::make(
                        ['recipient' => config('tasks.notification_recipient')],
                        ['recipient' => ['required', 'email']],
                        ['recipient.*' => 'Set a valid TASK_NOTIFICATION_EMAIL in your environment.']
                    )->validate()['recipient'];

                    // Synchronous mail needs no worker with the default log transport.
                    // A transport failure rolls back the task so the action can be retried.
                    Mail::to($recipient)->send(new TaskCompleted($current, $request->user()->name));
                }

                return $becameDone;
            });
        } catch (TransportExceptionInterface $exception) {
            report($exception);

            return back()->withInput()->withErrors([
                'notification' => 'The email could not be generated. Your task was not changed. Check mail settings and try again.',
            ]);
        }

        return to_route('tasks.show', $task)->with('success', $notified
            ? 'Task completed. A notification email was generated.'
            : 'Task updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        // Soft deletion using the required boolean column, never delete().
        $archived = Task::active()->whereKey($task->id)->update([
            'is_archived' => true,
            'updated_at' => now(),
        ]);
        abort_if($archived === 0, 404);

        return to_route('tasks.index')->with('success', 'Task archived. Its database record has been retained.');
    }
}
