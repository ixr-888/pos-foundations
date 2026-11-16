@extends('layouts.app')
@section('title', 'Account Settings')
@section('page', 'account')
@section('breadcrumb', 'Account Settings')
@section('content')
<div class="page-heading"><div><p class="eyebrow">YOUR PLACE AT THE COUNTER</p><h1>Account settings.</h1><p class="page-description">Keep your login details up to date.</p></div></div>
<section class="panel form-panel account-section">
    <h2>Your details</h2>
    <form method="post" action="{{ route('account.update') }}">@csrf @method('PATCH')
        <div class="field"><label for="name">Full name</label><input id="name" name="name" value="{{ is_string(old('name', $user->name)) ? old('name', $user->name) : '' }}" required maxlength="255" autocomplete="name"></div>
        <div class="field"><label for="username">Username</label><input id="username" name="username" value="{{ is_string(old('username', $user->username)) ? old('username', $user->username) : '' }}" required maxlength="50" pattern="[A-Za-z0-9_.-]+" autocomplete="username"></div>
        <div class="field"><label for="email">Email address</label><input id="email" type="email" name="email" value="{{ is_string(old('email', $user->email)) ? old('email', $user->email) : '' }}" required maxlength="255" autocomplete="email"></div>
        <button type="submit" class="button button-primary">Save details</button>
    </form>
    @unless ($user->hasVerifiedEmail())<p class="field-help">Your email address is not verified. <a class="text-link" href="{{ route('verification.notice') }}">Verify email</a></p>@endunless
</section>
<section class="panel form-panel account-section">
    <h2>Change password</h2>
    @if ($errors->updatePassword->any())<div class="notice notice-error" role="alert"><ul>@foreach ($errors->updatePassword->all() as $message)<li>{{ $message }}</li>@endforeach</ul></div>@endif
    <form method="post" action="{{ route('password.update') }}">@csrf @method('PUT')
        <div class="field"><label for="current_password">Current password</label><input id="current_password" name="current_password" type="password" required autocomplete="current-password"></div>
        <div class="form-grid"><div class="field"><label for="new_password">New password</label><input id="new_password" name="password" type="password" required minlength="8" autocomplete="new-password"></div><div class="field"><label for="new_password_confirmation">Confirm new password</label><input id="new_password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password"></div></div>
        <button type="submit" class="button button-primary">Update password</button>
    </form>
</section>
<section class="panel form-panel account-section">
    <h2>Delete your account</h2><p class="auth-intro">This permanently deletes your login. Shared tasks are retained and their creator is shown as a former team member. Enter your password to confirm.</p>
    @if ($errors->userDeletion->any())<div class="notice notice-error" role="alert"><ul>@foreach ($errors->userDeletion->all() as $message)<li>{{ $message }}</li>@endforeach</ul></div>@endif
    <form method="post" action="{{ route('account.destroy') }}" data-confirm="Permanently delete your account? You will be logged out. Shared tasks will be retained.">@csrf @method('DELETE')<div class="field"><label for="delete_password">Current password</label><input id="delete_password" name="password" type="password" required autocomplete="current-password"></div><button type="submit" class="button button-danger">Delete my account</button></form>
</section>
@endsection
