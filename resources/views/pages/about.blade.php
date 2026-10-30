@extends('layouts.app')
@section('title', 'About')
@section('page', 'about')
@section('breadcrumb', 'About')

@section('content')
<div class="page-heading"><div><p class="eyebrow">A LITTLE CONTEXT</p><h1>About Counter</h1><p class="page-description">The foundation of a more organized store.</p></div><span class="quiet-badge">Counter POS · v2.0</span></div>

<section class="about-hero panel">
    <div class="about-mark"><img src="{{ asset('images/mark.svg') }}" width="68" height="68" alt=""></div>
    <p class="eyebrow">PEOPLE FIRST. ALWAYS.</p>
    <h2>A simple place for<br>the people in your business.</h2>
    <p>Counter brings customer and team account information into one easy-to-navigate workspace. Start with the people who make your store work, and build from there.</p>
    <a class="button button-primary" href="{{ route('home') }}">Back to overview <span aria-hidden="true">↗</span></a>
</section>

<div class="about-grid">
    <section class="panel about-section"><span class="feature-icon"><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#customers"></use></svg></span><h2>Know your customers</h2><p>View customer names, contact details, cities, and account status in a single directory. Search and filter to find the right record quickly.</p><a class="text-link" href="{{ route('customers.index') }}">Explore customer accounts <span aria-hidden="true">↗</span></a></section>
    <section class="panel about-section"><span class="feature-icon blue"><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#users"></use></svg></span><h2>Know your team</h2><p>Browse user accounts, usernames, roles, and statuses. Keep a clear view of the people who support your day-to-day operations.</p><a class="text-link" href="{{ route('users.index') }}">Explore user accounts <span aria-hidden="true">↗</span></a></section>
</div>

<section class="panel project-details">
    <div><p class="eyebrow">ABOUT THIS PROJECT</p><h2>From arrays to Eloquent</h2><p>Created for IT0049 TFA6, this Laravel application uses migrations to define its tables, seeders for fictional sample data, and Eloquent models to retrieve records. Blade views display the stored customer and staff accounts.</p></div>
    <dl><div><dt>Framework</dt><dd>Laravel 12 · PHP 8.2+</dd></div><div><dt>Pages</dt><dd>Overview, About, Customers, Users</dd></div><div><dt>Directory data</dt><dd>Database records via Eloquent</dd></div><div><dt>Project scope</dt><dd>Read-only account listings</dd></div></dl>
</section>
@endsection
