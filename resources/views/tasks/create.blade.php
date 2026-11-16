@extends('layouts.app')
@section('title', 'Create Task')
@section('page', 'tasks')
@section('breadcrumb', 'Create Task')
@section('content')
<div class="page-heading"><div><p class="eyebrow">MAKE ROOM FOR THE NEXT THING</p><h1>Create a task.</h1><p class="page-description">Give your team a clear next step. Fields marked * are required.</p></div></div>
<form class="panel form-panel" method="post" action="{{ route('tasks.store') }}">
    @csrf
    @include('tasks.partials.form')
</form>
@endsection
