@extends('layouts.app')
@section('title', 'Profile')
@section('page', 'profile')
@section('breadcrumb', 'Profile')
@section('content')
<div class="page-heading"><div><p class="eyebrow">THE PERSON BEHIND THE PROJECT</p><h1>Project profile.</h1><p class="page-description">Meet the student behind Tasks for Today.</p></div></div>
<section class="panel profile-card"><span class="profile-avatar" aria-hidden="true">NB</span><div><p class="eyebrow">STUDENT DEVELOPER</p><h2>Nazrin Ivan Bardinas</h2><p>IT0049 · Web System Technologies</p><p>A Laravel project for organizing daily store tasks, with secure management and automatic completion emails.</p><a class="button button-outline" href="{{ route('about') }}">About the project</a></div></section>
@auth<p class="page-note">To change your own login details, open <a class="text-link" href="{{ route('account.edit') }}">Account settings</a>.</p>@endauth
@endsection
