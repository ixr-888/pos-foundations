@extends('layouts.app')
@section('title', 'About')
@section('page', 'about')
@section('breadcrumb', 'About')
@section('content')
<div class="page-heading"><div><p class="eyebrow">A LITTLE ORDER GOES A LONG WAY</p><h1>About Counter.</h1><p class="page-description">A shared task workspace for the people behind the counter.</p></div></div>
<section class="panel about-hero"><img class="about-mark" src="{{ asset('images/mark.svg') }}" alt="" width="56" height="56"><p class="eyebrow">TASKS FOR TODAY</p><h2>Clear tasks. A smoother day.</h2><p>Counter brings your daily store tasks together, so everyone knows what comes next and what is already done.</p><a class="button button-primary" href="{{ route('tasks.index') }}">Explore the task list</a></section>
<div class="about-grid">
    <section class="panel about-section"><h2>A plan everyone can see</h2><p>The Welcome, Task List, Profile, and About pages are open to everyone. Search tasks, check due dates, and see progress without an account.</p><h2>A workspace your team can manage</h2><p>Log in to create, edit, and archive tasks. Each task has a title, description, due date, priority, and progress status.</p></section>
    <section class="panel about-section"><h2>Completion stays in the loop</h2><p>When an existing task moves to Done, a completion email is generated for the workspace recipient. Editing a task that is already Done does not send a repeat.</p><h2>A tidy list, with a history</h2><p>Archiving removes a task from the active list while retaining its database record.</p></section>
</div>
<section class="panel project-details"><div><p class="eyebrow">THE PROJECT</p><h2>Built with Laravel.</h2><p>Tasks for Today extends the Counter POS foundations with database-backed workflows, Breeze authentication, validation, and completion notifications.</p></div><dl><div><dt>Course</dt><dd>IT0049 · Web System Technologies</dd></div><div><dt>Activity</dt><dd>Technical Summative Assessment 4</dd></div><div><dt>Authentication</dt><dd>Laravel Breeze · Blade</dd></div><div><dt>Task storage</dt><dd>Eloquent · SQLite or MySQL</dd></div></dl></section>
@endsection
