@extends('layouts.admin')

@section('title', 'Compliance Dashboard')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Compliance Dashboard</h4>
            <p class="text-muted mb-0">Company-wide supplier compliance overview.</p>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Active Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_active }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Required Items</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $total_required }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-check-double"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card danger h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Expired</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expired_count }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Expiring Soon</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expiring_count }}</div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Expired Items Section --}}
    <div class="card mb-4">
        <div class="card-header bg-danger bg-opacity-10">
            <h5 class="mb-0 text-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>Expired Items
            </h5>
        </div>
        <div class="card-body">
            @if($expired_items->isEmpty())
                <div class="alert alert-success mb-0">
                    <i class="fas fa-check-circle me-2"></i>No expired items. All compliance items are current.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped datatable align-middle">
                        <thead>
                            <tr>
                                <th>Supplier Name</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Expiry Date</th>
                                <th>Days Overdue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expired_items as $item)
                                <tr>
                                    <td>{{ $item->supplier->sup_name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $item->type_text }}</span>
                                    </td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('m/d/Y') : 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-danger">
                                            {{ abs($item->days_until_expiry) }} days
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Expiring Soon Section --}}
    <div class="card mb-4">
        <div class="card-header bg-warning bg-opacity-10">
            <h5 class="mb-0 text-warning">
                <i class="fas fa-clock me-2"></i>Expiring Soon (30 Days)
            </h5>
        </div>
        <div class="card-body">
            @if($expiring_items->isEmpty())
                <div class="alert alert-success mb-0">
                    <i class="fas fa-check-circle me-2"></i>No items expiring soon. All compliance items have more than 30 days remaining.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped datatable align-middle">
                        <thead>
                            <tr>
                                <th>Supplier Name</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Expiry Date</th>
                                <th>Days Remaining</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expiring_items as $item)
                                <tr>
                                    <td>{{ $item->supplier->sup_name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $item->type_text }}</span>
                                    </td>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('m/d/Y') : 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            {{ $item->days_until_expiry }} days
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
