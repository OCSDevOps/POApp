<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Supplier Portal') - {{ config('app.name', 'POAPP') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Inter', sans-serif; background: #f6f8fb; }
        .navbar-brand { font-weight: 700; }
        .content { padding: 2rem 1.5rem; }
        .card { border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.04); }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid px-3">
            <a class="navbar-brand" href="{{ route('supplier.dashboard') }}">Supplier Portal</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#supplierNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="supplierNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link{{ request()->routeIs('supplier.dashboard') ? ' active' : '' }}" href="{{ route('supplier.dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link{{ str_contains(request()->route()->getName(), 'supplier.pricing.') ? ' active' : '' }}" href="{{ route('supplier.pricing.index') }}">Pricing</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link{{ str_contains(request()->route()->getName(), 'supplier.rfq.') ? ' active' : '' }}" href="{{ route('supplier.rfq.index') }}">RFQs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link{{ request()->routeIs('supplier.profile') ? ' active' : '' }}" href="{{ route('supplier.profile') }}">Profile</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted small">{{ auth('supplier')->user()->name ?? '' }}</span>
                    <form method="POST" action="{{ route('supplier.logout') }}">
                        @csrf
                        <button class="btn btn-outline-secondary btn-sm" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="content container-fluid">
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
