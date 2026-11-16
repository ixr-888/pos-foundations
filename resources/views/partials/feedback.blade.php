@if (session('success'))
    <div class="notice notice-success" role="status">{{ session('success') }}</div>
@endif
@if (session('status'))
    <div class="notice notice-success" role="status">{{ session('status') }}</div>
@endif
@if ($errors->any())
    <div class="notice notice-error" role="alert" tabindex="-1">
        <strong>Please check the following:</strong>
        <ul>@foreach ($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul>
    </div>
@endif
