<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><title>Task completed</title></head>
<body style="margin:0;padding:32px 16px;background:#f4f7f4;font-family:Arial,sans-serif;color:#243c2e">
<div style="max-width:560px;margin:0 auto;background:#fff;border:1px solid #e0e9e0;border-radius:12px;padding:32px">
    <p style="font-size:12px;letter-spacing:2px;color:#54775e">COUNTER POS · TASKS FOR TODAY</p>
    <h1 style="font-size:26px">One more task, completed.</h1>
    <p><strong>{{ $task->title }}</strong> has been marked done by {{ $completedBy }}.</p>
    <dl><dt>Task number</dt><dd>#{{ $task->id }}</dd><dt>Due date</dt><dd>{{ $task->due_date->format('F d, Y') }}</dd><dt>Completed at</dt><dd>{{ $task->completed_at?->format('F d, Y · g:i a T') }}</dd></dl>
    @if ($task->description)<p style="white-space:pre-wrap">{{ $task->description }}</p>@endif
    <p style="margin-top:28px"><a style="display:inline-block;padding:12px 18px;background:#236748;color:#fff;text-decoration:none;border-radius:6px" href="{{ route('tasks.show', $task) }}">View task</a></p>
    <p style="font-size:12px;line-height:1.7;color:#6b7b70">Generated automatically when this task changed to Done. Saving an already completed task does not generate another completion notification.</p>
</div>
</body>
</html>
