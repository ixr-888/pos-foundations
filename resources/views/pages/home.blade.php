@extends('layouts.app')
@section('title', 'Overview')
@section('page', 'home')
@section('breadcrumb', 'Overview')

@section('content')
<div class="page-heading">
    <div><p class="eyebrow">YOUR STORE AT A GLANCE</p><h1>A good day starts here.</h1><p class="page-description">Your customers, your team, one organized workspace.</p></div>
    <span class="quiet-badge"><span class="status-dot"></span> Account overview</span>
</div>

<section class="stat-grid" aria-label="Account summary">
    <article class="stat-card">
        <div class="stat-top"><span>Total customers</span><span class="stat-icon"><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#customers"></use></svg></span></div>
        <strong class="stat-number">{{ $customerCount }}</strong>
        <p>People in your customer directory</p>
    </article>
    <article class="stat-card">
        <div class="stat-top"><span>Team accounts</span><span class="stat-icon blue"><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#users"></use></svg></span></div>
        <strong class="stat-number">{{ $userCount }}</strong>
        <p>Users across your store roles</p>
    </article>
    <article class="stat-card">
        <div class="stat-top"><span>Active team members</span><span class="stat-icon amber"><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#check"></use></svg></span></div>
        <strong class="stat-number">{{ $activeUserCount }}<span class="stat-denominator"> / {{ $userCount }}</span></strong>
        <p>Accounts marked as active</p>
    </article>
</section>

<section class="welcome-panel">
    <div class="welcome-copy"><span class="panel-eyebrow">BUILT AROUND YOUR PEOPLE</span><h2>Good service begins<br>with knowing who's who.</h2><p>Browse your customer records or get a clear view of the people behind the counter.</p><a class="button button-light" href="{{ route('customers.index') }}">Explore customers <span aria-hidden="true">↗</span></a></div>
    <div class="counter-illustration" aria-hidden="true">
        <div class="illustration-ring ring-one"></div><div class="illustration-ring ring-two"></div>
        <div class="illustration-card card-back"><span class="mini-avatar">M</span><div><strong>Miguel Santos</strong><small>Customer account</small></div><span class="mini-check">✓</span></div>
        <div class="illustration-card card-front"><span class="mini-avatar sand">A</span><div><strong>Ana Reyes</strong><small>Customer account</small></div><span class="mini-check">✓</span></div>
        <div class="illustration-tag"><span class="status-dot"></span> A little more connected.</div>
    </div>
</section>

<div class="home-bottom">
    <section class="panel">
        <div class="panel-heading"><div><h2>Recently joined customers</h2><p>A quick look at your newest records.</p></div><a class="text-link" href="{{ route('customers.index') }}">View all <span aria-hidden="true">↗</span></a></div>
        <div class="recent-list">
            @forelse ($recentCustomers as $customer)
            <div class="recent-row"><span class="avatar">{{ strtoupper(substr($customer->full_name, 0, 1)) }}</span><div class="person-info"><strong>{{ $customer->full_name }}</strong><span>{{ $customer->email }}</span></div><span class="badge {{ $customer->status === 'Active' ? 'badge-active' : 'badge-inactive' }}">{{ $customer->status }}</span></div>
            @empty
            <p class="page-note">No customer records yet.</p>
            @endforelse
        </div>
    </section>
    <section class="panel quick-panel">
        <div class="panel-heading"><div><h2>Find your way around</h2><p>Everything you need, close at hand.</p></div></div>
        <a class="quick-link" href="{{ route('users.index') }}"><span class="quick-icon"><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#users"></use></svg></span><span><strong>Your team</strong><small>Browse user accounts and roles</small></span><span aria-hidden="true">↗</span></a>
        <a class="quick-link" href="{{ route('about') }}"><span class="quick-icon"><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#info"></use></svg></span><span><strong>About Counter</strong><small>Get to know this workspace</small></span><span aria-hidden="true">↗</span></a>
    </section>
</div>
@endsection
