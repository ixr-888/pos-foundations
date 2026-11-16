@extends('layouts.app')
@section('title', 'Verify Email')
@section('page', 'account')
@section('breadcrumb', 'Verify Email')
@section('content')
<section class="panel auth-card"><p class="eyebrow">ONE MORE STEP</p><h1>Verify your email.</h1><p class="auth-intro">Use the link generated for your email address. You can continue managing tasks while verification is pending.</p><form method="post" action="{{ route('verification.send') }}">@csrf<button class="button button-primary full-width" type="submit">Generate a new verification link</button></form><p class="auth-footnote"><a class="text-link" href="{{ route('tasks.index') }}">Continue to tasks</a></p></section>
@endsection
