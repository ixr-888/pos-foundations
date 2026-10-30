@extends('layouts.app')
@section('title', 'User Accounts')
@section('page', 'users')
@section('breadcrumb', 'User Accounts')

@section('content')
<div class="page-heading">
    <div><p class="eyebrow">THE PEOPLE BEHIND THE COUNTER</p><h1>User Accounts</h1><p class="page-description">Meet your store team and see their assigned roles.</p></div>
    <span class="quiet-badge"><span class="status-dot"></span> {{ count($users) }} accounts</span>
</div>

<section class="panel directory" data-directory data-export-name="counter-users.csv" aria-labelledby="directory-title">
    <div class="panel-heading directory-heading"><div class="heading-with-count"><h2 id="directory-title">Team directory</h2><span class="count-pill">{{ count($users) }}</span></div><button class="button button-outline" type="button" data-export><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#download"></use></svg> Export CSV</button></div>
    <div class="directory-toolbar">
        <div class="search-field"><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#search"></use></svg><label class="sr-only" for="user-search">Search user accounts</label><input id="user-search" type="search" placeholder="Search by name, username, or email..." data-search autocomplete="off"></div>
        <div class="filter-field"><label class="sr-only" for="user-role">Filter users by role</label><select id="user-role" data-filter="role"><option value="">All roles</option><option>Administrator</option><option>Manager</option><option>Cashier</option><option>Inventory Staff</option></select></div>
        <div class="filter-field"><label class="sr-only" for="user-status">Filter users by status</label><select id="user-status" data-filter="status"><option value="">All statuses</option><option>Active</option><option>Inactive</option></select></div>
        <button class="reset-button" type="button" data-reset>Reset</button>
    </div>
    <div class="table-scroll" tabindex="0" role="region" aria-label="User account table">
        <table>
            <caption class="sr-only">User accounts</caption>
            <thead><tr><th scope="col">ACCOUNT ID</th><th scope="col">TEAM MEMBER</th><th scope="col">USERNAME</th><th scope="col">ROLE</th><th scope="col">STATUS</th></tr></thead>
            <tbody>
                @foreach ($users as $user)
                <tr data-record data-status="{{ $user->status }}" data-role="{{ $user->role }}">
                    <td><span class="record-id">U-{{ $user->id }}</span></td>
                    <td><div class="person-cell"><span class="avatar avatar-blue">{{ strtoupper(substr($user->full_name, 0, 1)) }}</span><div class="person-info"><strong>{{ $user->full_name }}</strong><span>{{ $user->email }}</span></div></div></td>
                    <td><span class="username">{{ $user->username }}</span></td>
                    <td><span class="role-badge">{{ $user->role }}</span></td>
                    <td><span class="badge {{ $user->status === 'Active' ? 'badge-active' : 'badge-inactive' }}">{{ $user->status }}</span></td>
                </tr>
                @endforeach
                <tr data-empty-state @if ($users->isNotEmpty()) hidden @endif><td colspan="5"><div class="empty-state"><strong>No team members to display</strong><p>Try a different search or reset your filters.</p></div></td></tr>
            </tbody>
        </table>
    </div>
    <div class="directory-footer"><p role="status" aria-live="polite">Showing <strong data-visible-count>{{ count($users) }}</strong> of {{ count($users) }} users</p><span class="data-note"><span class="status-dot"></span> Sample directory</span></div>
</section>
<p class="page-note">This directory displays sample user records. Roles are labels for this activity; login and access control are future features.</p>
@endsection
