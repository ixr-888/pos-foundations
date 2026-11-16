@extends('layouts.app')
@section('title', 'Welcome')
@section('page', 'home')
@section('breadcrumb', 'Welcome')
@section('content')
<div class="page-heading"><div><p class="eyebrow">YOUR DAY, A LITTLE CLEARER</p><h1>A good day starts here.</h1><p class="page-description">Plan the work. Keep things moving. Celebrate the small wins.</p></div><span class="quiet-badge"><span class="status-dot"></span>{{ now()->format('l, M d') }}</span></div>
<section class="stat-grid" aria-label="Workspace task summary">
    <article class="stat-card"><div class="stat-top">Active tasks <span class="stat-icon"><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#overview"></use></svg></span></div><strong class="stat-number">{{ $taskCount }}</strong><p>Your current shared task list</p></article>
    <article class="stat-card"><div class="stat-top">Still to do <span class="stat-icon amber"><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#check"></use></svg></span></div><strong class="stat-number">{{ $openCount }}</strong><p>Every finished task is a little progress</p></article>
    <article class="stat-card"><div class="stat-top">Completed <span class="stat-icon blue"><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#check"></use></svg></span></div><strong class="stat-number">{{ $doneCount }}</strong><p>Good work, all in one place</p></article>
</section>
<section class="welcome-panel task-hero">
    <div class="welcome-copy"><span class="panel-eyebrow">A SHARED PLAN FOR THE COUNTER</span><h2>Less keeping track.<br>More getting things done.</h2><p>From opening the store to closing the day, keep every next step clear for your team.</p><a class="button button-light" href="{{ route('tasks.index') }}">Explore tasks <span aria-hidden="true">↗</span></a></div>
    <div class="hero-score"><span class="hero-score-number">{{ str_pad($todayCount, 2, '0', STR_PAD_LEFT) }}</span><span>tasks due today</span><a href="{{ route('tasks.index', ['today' => 1]) }}">See today's list →</a></div>
</section>
<div class="home-bottom">
    <section class="panel"><div class="panel-heading"><div><h2>Next on the list</h2><p>The nearest due dates, ready when you are.</p></div><a class="text-link" href="{{ route('tasks.index') }}">View all ↗</a></div><div class="recent-list">@forelse ($upcomingTasks as $task)<a class="recent-row" href="{{ route('tasks.show', $task) }}"><span class="avatar" aria-hidden="true">✓</span><div class="person-info"><strong>{{ $task->title }}</strong><span>Due {{ $task->due_date->format('M d') }} · {{ \App\Models\Task::PRIORITIES[$task->priority] }} priority</span></div><span class="badge status-{{ $task->status }}">{{ \App\Models\Task::STATUSES[$task->status] }}</span></a>@empty<div class="empty-state"><strong>You're all caught up.</strong><p>There are no unfinished tasks.</p></div>@endforelse</div></section>
    <section class="panel quick-panel"><div class="panel-heading"><div><h2>Make the day work for you</h2><p>One place for the whole team.</p></div></div>@auth<a class="quick-link" href="{{ route('tasks.create') }}"><span class="quick-icon">+</span><span><strong>Create a task</strong><small>Give your team a clear next step</small></span><span aria-hidden="true">↗</span></a>@else<a class="quick-link" href="{{ route('register') }}"><span class="quick-icon">+</span><span><strong>Join the workspace</strong><small>Create an account to manage tasks</small></span><span aria-hidden="true">↗</span></a>@endauth<a class="quick-link" href="{{ route('about') }}"><span class="quick-icon">i</span><span><strong>A clearer way to work</strong><small>Get to know Tasks for Today</small></span><span aria-hidden="true">↗</span></a></section>
</div>
@endsection
