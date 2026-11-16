@extends('layouts.app')
@section('title', 'Create Account')
@section('page', 'register')
@section('breadcrumb', 'Create Account')
@section('content')
<section class="panel auth-card">
    <p class="eyebrow">MAKE YOURSELF PART OF THE DAY</p><h1>Join the workspace.</h1><p class="auth-intro">Create an account to help keep the counter moving.</p>
    <form method="post" action="{{ route('register') }}">
        @csrf
        <div class="field"><label for="name">Full name</label><input id="name" name="name" value="{{ is_string(old('name')) ? old('name') : '' }}" required maxlength="255" autocomplete="name" autofocus></div>
        <div class="field"><label for="username">Username</label><input id="username" name="username" value="{{ is_string(old('username')) ? old('username') : '' }}" required maxlength="50" pattern="[A-Za-z0-9_-]+" autocomplete="username" aria-describedby="username-help"><p class="field-help" id="username-help">Use letters, numbers, underscores, or hyphens.</p></div>
        <div class="field"><label for="email">Email address</label><input id="email" type="email" name="email" value="{{ is_string(old('email')) ? old('email') : '' }}" required maxlength="255" autocomplete="email"></div>
        <div class="form-grid"><div class="field"><label for="password">Password</label><input id="password" type="password" name="password" required minlength="8" autocomplete="new-password"><p class="field-help">At least 8 characters.</p></div><div class="field"><label for="password_confirmation">Confirm password</label><input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"></div></div>
        <button class="button button-primary full-width" type="submit">Create account</button>
    </form>
    <p class="auth-footnote">Already have an account? <a class="text-link" href="{{ route('login') }}">Log in</a></p>
</section>
@endsection
