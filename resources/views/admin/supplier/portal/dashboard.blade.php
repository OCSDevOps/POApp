@extends('layouts.admin')

@section('title', 'Supplier Dashboard')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <h4 class="mb-1">Supplier Dashboard - {{ $supplier->sup_name ?? '' }}</h4>
        <p class="text-muted mb-0">Overview of orders, RFQs, and catalog.</p>
    </div>

    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stat-card border-start border-primary border-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Orders</p>
                        <h4 class="mb-0">{{ $total_orders }}</h4>
                    </div>
                    <i class="fa fa-file-alt fa-2x text-primary"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stat-card border-start border-warning border-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Pending Orders</p>
                        <h4 class="mb-0">{{ $pending_orders }}</h4>
                    </div>
                    <i class="fa fa-clock fa-2x text-warning"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stat-card border-start border-success border-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Catalog Items</p>
                        <h4 class="mb-0">{{ $catalog_items }}</h4>
                    </div>
                    <i class="fa fa-list fa-2x text-success"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-3">
        <div class="card stat-card border-start border-info border-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Pending RFQs</p>
                        <h4 class="mb-0">{{ $pending_rfqs }}</h4>
                    </div>
                    <i class="fa fa-envelope-open-text fa-2x text-info"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mb-3">
        <div class="card">
            <div class="card-header">Recent Orders</div>
            <div class="card-body table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>PO #</th>
                            <th>Project</th>
                            <th>Created</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_orders as $order)
                            <tr>
                                <td>{{ $order->porder_no }}</td>
                                <td>{{ $order->project->proj_name ?? '' }}</td>
                                <td>{{ $order->porder_createdate }}</td>
                                <td>
                                    @if($order->porder_delivery_status == 1)
                                        <span class="badge bg-success">Received</span>
                                    @elseif($order->porder_delivery_status == 2)
                                        <span class="badge bg-warning text-dark">Partial</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">No recent orders.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
