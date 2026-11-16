@extends('layouts.app')
@section('title', 'Task Details')
@section('page', 'tasks')
@section('breadcrumb', 'Task Details')
@section('content')
<div class="page-heading"><div><p class="eyebrow">TASK #{{ $task->id }}</p><h1>{{ $task->title }}</h1><p class="page-description">A clear view of what needs to happen.</p></div><a class="button button-outline" href="{{ route('tasks.index') }}">Back to tasks</a></div>
<article class="panel detail-panel">
    <div class="detail-tags"><span class="badge status-{{ $task->status }}">{{ \App\Models\Task::STATUSES[$task->status] }}</span><span class="priority priority-{{ $task->priority }}">{{ \App\Models\Task::PRIORITIES[$task->priority] }} priority</span></div>
    <h2>Description</h2><p class="task-description">{{ $task->description ?: 'No description added.' }}</p>
    <dl class="detail-grid">
        <div><dt>Due date</dt><dd>{{ $task->due_date->format('F d, Y') }}</dd></div>
        <div><dt>Created by</dt><dd>{{ $task->creator?->name ?? 'Former team member' }}</dd></div>
        <div><dt>Last updated</dt><dd>{{ $task->updated_at->format('M d, Y · g:i a') }}</dd></div>
        <div><dt>Completed</dt><dd>{{ $task->completed_at?->format('M d, Y · g:i a') ?? 'Still to do' }}</dd></div>
    </dl>
    @auth<div class="form-actions"><a class="button button-primary" href="{{ route('tasks.edit', $task) }}">Edit task</a>@include('tasks.partials.archive', ['task' => $task])</div>@else<a class="button button-outline" href="{{ route('login') }}">Log in to manage this task</a>@endauth
</article>
@endsection
