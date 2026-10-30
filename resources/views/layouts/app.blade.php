<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Counter POS customer and team account workspace.">
    <title>@yield('title') · Counter POS</title>
    <link rel="icon" href="{{ asset('images/mark.svg') }}" type="image/svg+xml">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script defer src="{{ asset('js/app.js') }}"></script>
</head>
<body data-page="@yield('page')">
    <a class="skip-link" href="#main-content">Skip to content</a>
    <div class="app-shell">
        <aside class="sidebar" aria-label="Main navigation">
            <a class="brand" href="{{ route('home') }}" aria-label="Counter POS home">
                <img src="{{ asset('images/mark.svg') }}" width="38" height="38" alt="">
                <span>counter<span class="brand-subtitle">POINT OF SALE</span></span>
            </a>

            <div class="workspace-switch">
                <span class="workspace-icon">C</span>
                <div><strong>Sample store</strong><span>Main workspace</span></div>
                <span class="workspace-dot" aria-label="Demo workspace"></span>
            </div>

            <p class="nav-label">WORKSPACE</p>
            <nav class="nav-list">
                <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" aria-current="{{ request()->routeIs('home') ? 'page' : 'false' }}">
                    <svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#overview"></use></svg>
                    <span>Overview</span>
                </a>
                <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.index') ? 'active' : '' }}" aria-current="{{ request()->routeIs('customers.index') ? 'page' : 'false' }}">
                    <svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#customers"></use></svg>
                    <span>Customer Accounts</span>
                </a>
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}" aria-current="{{ request()->routeIs('users.index') ? 'page' : 'false' }}">
                    <svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#users"></use></svg>
                    <span>User Accounts</span>
                </a>
                <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" aria-current="{{ request()->routeIs('about') ? 'page' : 'false' }}">
                    <svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#info"></use></svg>
                    <span>About</span>
                </a>
            </nav>

            <div class="sidebar-bottom">
                <span class="small-mark">c</span>
                <div><strong>One workspace.<br>A clearer counter.</strong><span>Counter POS Foundations</span></div>
            </div>
        </aside>

        <div class="main-shell">
            <header class="topbar">
                <div class="breadcrumbs"><span>Workspace</span><span class="slash">/</span><strong>@yield('breadcrumb')</strong></div>
                <span class="demo-badge"><span></span> Demo workspace</span>
            </header>

            <main id="main-content" tabindex="-1">
                @yield('content')
            </main>

            <footer class="footer"><span>Counter POS · Account workspace</span><span>Fictional sample records</span></footer>
        </div>
    </div>
</body>
</html>
