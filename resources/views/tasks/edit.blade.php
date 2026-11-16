@extends('layouts.app')
@section('title', 'Edit Task')
@section('page', 'tasks')
@section('breadcrumb', 'Edit Task')
@section('content')
<div class="page-heading"><div><p class="eyebrow">KEEP EVERYONE IN THE LOOP</p><h1>Edit your task.</h1><p class="page-description">Update the details or move this task forward. Fields marked * are required.</p></div></div>
<form class="panel form-panel" method="post" action="{{ route('tasks.update', $task) }}">
    @csrf
    @method('PUT')
    @include('tasks.partials.form')
</form>
@endsection
