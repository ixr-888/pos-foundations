@extends('layouts.app')
@section('title', 'Log In')
@section('page', 'login')
@section('breadcrumb', 'Log In')
@section('content')
<section class="panel auth-card">
    <p class="eyebrow">WELCOME BACK TO THE COUNTER</p><h1>Pick up where you left off.</h1><p class="auth-intro">Log in to create, update, and archive tasks.</p>
    <form method="post" action="{{ route('login') }}">
        @csrf
        <div class="field"><label for="email">Email address</label><input id="email" type="email" name="email" value="{{ is_string(old('email')) ? old('email') : '' }}" required maxlength="255" autofocus autocomplete="username"></div>
        <div class="field"><label for="password">Password</label><input id="password" type="password" name="password" required autocomplete="current-password"></div>
        <div class="auth-options"><label class="check-label"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Remember me</label><a class="text-link" href="{{ route('password.request') }}">Forgot password?</a></div>
        <button class="button button-primary full-width" type="submit">Log in</button>
    </form>
    <p class="auth-footnote">New here? <a class="text-link" href="{{ route('register') }}">Create an account</a></p>
</section>
@endsection
