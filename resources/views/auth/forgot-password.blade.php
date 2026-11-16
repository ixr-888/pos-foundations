@extends('layouts.app')
@section('title', 'Reset Password')
@section('page', 'login')
@section('breadcrumb', 'Reset Password')
@section('content')
<section class="panel auth-card"><p class="eyebrow">LET'S GET YOU BACK IN</p><h1>Forgot your password?</h1><p class="auth-intro">Enter your account email to generate a password reset link.</p>
    <form method="post" action="{{ route('password.email') }}">@csrf<div class="field"><label for="email">Email address</label><input id="email" type="email" name="email" required value="{{ is_string(old('email')) ? old('email') : '' }}" autocomplete="email" autofocus></div><button class="button button-primary full-width" type="submit">Send reset link</button></form>
    <p class="auth-footnote"><a class="text-link" href="{{ route('login') }}">Back to log in</a></p>
</section>
@endsection
