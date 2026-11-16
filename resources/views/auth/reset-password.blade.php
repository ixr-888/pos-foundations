@extends('layouts.app')
@section('title', 'New Password')
@section('page', 'login')
@section('breadcrumb', 'New Password')
@section('content')
<section class="panel auth-card"><p class="eyebrow">A FRESH START</p><h1>Choose a new password.</h1>
    <form method="post" action="{{ route('password.store') }}">@csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="field"><label for="email">Email address</label><input id="email" type="email" name="email" required value="{{ is_string(old('email', $request->email)) ? old('email', $request->email) : '' }}" autocomplete="username"></div>
        <div class="field"><label for="password">New password</label><input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" autofocus></div>
        <div class="field"><label for="password_confirmation">Confirm new password</label><input id="password_confirmation" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"></div>
        <button class="button button-primary full-width" type="submit">Reset password</button>
    </form>
</section>
@endsection
