@extends('layouts.app')
@section('title', 'Confirm Password')
@section('page', 'account')
@section('breadcrumb', 'Confirm Password')
@section('content')
<section class="panel auth-card"><p class="eyebrow">CONFIRM IT'S YOU</p><h1>Confirm your password.</h1><form method="post" action="{{ route('password.confirm') }}">@csrf<div class="field"><label for="password">Password</label><input id="password" type="password" name="password" required autocomplete="current-password" autofocus></div><button type="submit" class="button button-primary full-width">Confirm password</button></form></section>
@endsection
