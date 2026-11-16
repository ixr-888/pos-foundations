@extends('layouts.app')
@section('title', 'Task List')
@section('page', 'tasks')
@section('breadcrumb', 'Task List')

@section('content')
<div class="page-heading">
    <div><p class="eyebrow">A LITTLE PROGRESS, EVERY DAY</p><h1>Tasks for today.</h1><p class="page-description">Keep your counter moving, one task at a time.</p></div>
    @auth
        <a class="button button-primary" href="{{ route('tasks.create') }}">+ Create task</a>
    @else
        <a class="button button-outline" href="{{ route('login') }}">Log in to manage tasks</a>
    @endauth
</div>
<section class="stat-grid" aria-label="Task summary">
    <article class="stat-card"><div class="stat-top">All active tasks</div><strong class="stat-number">{{ $totalCount }}</strong><p>Archived tasks stay out of the way</p></article>
    <article class="stat-card"><div class="stat-top">Still to do</div><strong class="stat-number">{{ $openCount }}</strong><p>Pending and in progress</p></article>
    <article class="stat-card"><div class="stat-top">Completed</div><strong class="stat-number">{{ $doneCount }}</strong><p>A little more accomplished</p></article>
</section>
<section class="panel">
    <div class="panel-heading directory-heading"><div><h2>Your task list</h2><p>{{ $tasks->total() }} {{ $tasks->total() === 1 ? 'task matches' : 'tasks match' }} your filters.</p></div></div>
    <form class="directory-toolbar task-filters" method="get" action="{{ route('tasks.index') }}">
        <label class="search-field"><span class="sr-only">Search task titles</span><input type="search" name="q" value="{{ $filters['q'] ?? '' }}" maxlength="100" placeholder="Search task titles…"></label>
        <label class="filter-field"><span class="sr-only">Filter by status</span><select name="status"><option value="">All statuses</option>@foreach (\App\Models\Task::STATUSES as $value => $label)<option value="{{ $value }}" @selected(($filters['status'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></label>
        <label class="filter-field"><span class="sr-only">Filter by priority</span><select name="priority"><option value="">All priorities</option>@foreach (\App\Models\Task::PRIORITIES as $value => $label)<option value="{{ $value }}" @selected(($filters['priority'] ?? '') === $value)>{{ $label }}</option>@endforeach</select></label>
        <label class="check-label"><input type="checkbox" name="today" value="1" @checked(($filters['today'] ?? '') === '1')> Due today</label>
        <button type="submit" class="button button-primary">Apply</button>
        <a class="reset-button" href="{{ route('tasks.index') }}">Reset</a>
    </form>
    <div class="table-scroll">
        <table class="task-table">
            <caption class="sr-only">Active tasks, ordered by due date</caption>
            <thead><tr><th scope="col">Task</th><th scope="col">Due date</th><th scope="col">Priority</th><th scope="col">Status</th><th scope="col">Actions</th></tr></thead>
            <tbody>
            @forelse ($tasks as $task)
                <tr>
                    <td><a class="task-title" href="{{ route('tasks.show', $task) }}">{{ $task->title }}</a><span class="task-preview">{{ \Illuminate\Support\Str::limit($task->description ?? '', 70) }}</span></td>
                    <td class="no-wrap">{{ $task->due_date->format('M d, Y') }}@if ($task->status !== 'done' && $task->due_date->lt(today()))<span class="overdue-label">Overdue</span>@endif</td>
                    <td><span class="priority priority-{{ $task->priority }}">{{ \App\Models\Task::PRIORITIES[$task->priority] }}</span></td>
                    <td><span class="badge status-{{ $task->status }}">{{ \App\Models\Task::STATUSES[$task->status] }}</span></td>
                    <td><div class="row-actions"><a class="text-link" href="{{ route('tasks.show', $task) }}">View</a>@auth<a class="text-link" href="{{ route('tasks.edit', $task) }}">Edit</a>@include('tasks.partials.archive', ['task' => $task])@endauth</div></td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state"><strong>No tasks found</strong><p>Try changing your filters.@auth You can also create a new task.@endauth</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="directory-footer"><p>Showing {{ $tasks->firstItem() ?? 0 }}–{{ $tasks->lastItem() ?? 0 }} of {{ $tasks->total() }}</p>{{ $tasks->links('partials.pagination') }}</div>
</section>
@guest<p class="page-note">Everyone can view tasks. Create an account or log in to add, edit, and archive them.</p>@endguest
@endsection
