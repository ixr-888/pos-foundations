COUNTER POS — TASKS FOR TODAY

Task completed: {{ $task->title }}
Task #{{ $task->id }}
Completed by: {{ $completedBy }}
Due date: {{ $task->due_date->format('F d, Y') }}
Completed at: {{ $task->completed_at?->format('F d, Y g:i a T') }}

{{ $task->description }}

View task: {{ route('tasks.show', $task) }}

Generated when the task changed to Done. Saving an already completed task does not generate another completion notification.
