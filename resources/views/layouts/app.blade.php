<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Counter POS Tasks for Today: a shared task workspace for your store.">
    <title>@yield('title') · Counter POS</title>
    <link rel="icon" href="{{ asset('images/mark.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script defer src="{{ asset('js/app.js') }}"></script>
</head>
<body data-page="@yield('page')">
<a class="skip-link" href="#main-content">Skip to content</a>
<div class="app-shell">
    <aside class="sidebar" aria-label="Main navigation">
        <a class="brand" href="{{ route('home') }}" aria-label="Counter POS home"><img src="{{ asset('images/mark.svg') }}" width="38" height="38" alt=""><span>counter<span class="brand-subtitle">TASKS FOR TODAY</span></span></a>
        <div class="workspace-switch"><span class="workspace-icon">C</span><div><strong>The daily counter</strong><span>Shared store workspace</span></div><span class="workspace-dot"></span></div>
        <p class="nav-label">WORKSPACE</p>
        <nav class="nav-list">
            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}" @if(request()->routeIs('home')) aria-current="page" @endif><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#overview"></use></svg><span>Welcome</span></a>
            <a class="nav-link {{ request()->routeIs('tasks.*') ? 'active' : '' }}" href="{{ route('tasks.index') }}" @if(request()->routeIs('tasks.*')) aria-current="page" @endif><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#check"></use></svg><span>Task List</span></a>
            <a class="nav-link {{ request()->routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}" @if(request()->routeIs('profile')) aria-current="page" @endif><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#users"></use></svg><span>Profile</span></a>
            <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}" @if(request()->routeIs('about')) aria-current="page" @endif><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#info"></use></svg><span>About</span></a>
        </nav>
        @auth
        <div class="secondary-nav"><p class="nav-label">STORE DIRECTORIES</p><a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">Customer Accounts</a><a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">User Accounts</a></div>
        @endauth
        <div class="sidebar-bottom"><span class="small-mark">c</span><div><strong>Small steps.<br>A better working day.</strong><span>Counter POS · Tasks for Today</span></div></div>
    </aside>
    <div class="main-shell">
        <header class="topbar"><div class="breadcrumbs"><span>Workspace</span><span class="slash">/</span><strong>@yield('breadcrumb')</strong></div><div class="header-actions">@auth<a class="account-link" href="{{ route('account.edit') }}">{{ \Illuminate\Support\Str::limit(auth()->user()->name, 24) }}</a><form method="post" action="{{ route('logout') }}">@csrf<button class="button button-outline" type="submit">Log out</button></form>@else<a class="text-link" href="{{ route('login') }}">Log in</a><a class="button button-primary" href="{{ route('register') }}">Create account</a>@endauth</div></header>
        <main id="main-content" tabindex="-1">@include('partials.feedback')@yield('content')</main>
        <footer class="footer"><span>Counter POS · Tasks for Today</span><span>One task at a time.</span></footer>
    </div>
</div>
</body>
</html>
