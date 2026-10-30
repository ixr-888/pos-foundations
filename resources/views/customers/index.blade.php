@extends('layouts.app')
@section('title', 'Customer Accounts')
@section('page', 'customers')
@section('breadcrumb', 'Customer Accounts')

@section('content')
<div class="page-heading">
    <div><p class="eyebrow">THE PEOPLE YOU SERVE</p><h1>Customer Accounts</h1><p class="page-description">A clear, simple directory of your store's customers.</p></div>
    <span class="quiet-badge"><span class="status-dot"></span> {{ count($customers) }} accounts</span>
</div>

<section class="panel directory" data-directory data-export-name="counter-customers.csv" aria-labelledby="directory-title">
    <div class="panel-heading directory-heading"><div class="heading-with-count"><h2 id="directory-title">Customer directory</h2><span class="count-pill">{{ count($customers) }}</span></div><button class="button button-outline" type="button" data-export><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#download"></use></svg> Export CSV</button></div>
    <div class="directory-toolbar">
        <div class="search-field"><svg aria-hidden="true"><use href="{{ asset('images/icons.svg') }}#search"></use></svg><label class="sr-only" for="customer-search">Search customer accounts</label><input id="customer-search" type="search" placeholder="Search by name, email, or city..." data-search autocomplete="off"></div>
        <div class="filter-field"><label class="sr-only" for="customer-status">Filter customers by status</label><select id="customer-status" data-filter="status"><option value="">All statuses</option><option>Active</option><option>Inactive</option></select></div>
        <button class="reset-button" type="button" data-reset>Reset</button>
    </div>
    <div class="table-scroll" tabindex="0" role="region" aria-label="Customer account table">
        <table>
            <caption class="sr-only">Customer accounts</caption>
            <thead><tr><th scope="col">ACCOUNT ID</th><th scope="col">CUSTOMER</th><th scope="col">PHONE</th><th scope="col">CITY</th><th scope="col">STATUS</th><th scope="col">JOINED</th></tr></thead>
            <tbody>
                @foreach ($customers as $customer)
                <tr data-record data-status="{{ $customer->status }}">
                    <td><span class="record-id">C-{{ $customer->id }}</span></td>
                    <td><div class="person-cell"><span class="avatar">{{ strtoupper(substr($customer->full_name, 0, 1)) }}</span><div class="person-info"><strong>{{ $customer->full_name }}</strong><span>{{ $customer->email }}</span></div></div></td>
                    <td class="no-wrap">{{ $customer->phone }}</td>
                    <td>{{ $customer->city }}</td>
                    <td><span class="badge {{ $customer->status === 'Active' ? 'badge-active' : 'badge-inactive' }}">{{ $customer->status }}</span></td>
                    <td class="no-wrap">{{ $customer->joined_on?->format('Y-m-d') }}</td>
                </tr>
                @endforeach
                <tr data-empty-state @if ($customers->isNotEmpty()) hidden @endif><td colspan="6"><div class="empty-state"><strong>No customers to display</strong><p>Try a different search or reset your filters.</p></div></td></tr>
            </tbody>
        </table>
    </div>
    <div class="directory-footer"><p role="status" aria-live="polite">Showing <strong data-visible-count>{{ count($customers) }}</strong> of {{ count($customers) }} customers</p><span class="data-note"><span class="status-dot"></span> Sample directory</span></div>
</section>
<p class="page-note">All names and contact details on this page are fictional sample data.</p>
@endsection
