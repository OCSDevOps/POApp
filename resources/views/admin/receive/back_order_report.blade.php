@extends('layouts.admin')

@section('title', 'Back Order Report')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Back Order Report</h4>
        <a href="{{ route('admin.receive.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Receive Orders
        </a>
    </div>

    <div class="row">
        {{-- Main Column: Back Orders Table --}}
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Outstanding Back Orders</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="backOrdersTable">
                            <thead class="table-light">
                                <tr>
                                    <th>PO #</th>
                                    <th>Item</th>
                                    <th class="text-center">Ordered Qty</th>
                                    <th class="text-center">Received Qty</th>
                                    <th class="text-center">Back Order Qty</th>
                                    <th>Supplier</th>
                                    <th>Project</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($backOrders as $bo)
                                    <tr>
                                        <td>
                                            <strong>{{ $bo->porder_no ?? '—' }}</strong>
                                        </td>
                                        <td>{{ $bo->item_name ?? $bo->po_detail_item ?? '—' }}</td>
                                        <td class="text-center">{{ $bo->ordered_qty ?? 0 }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $bo->received_qty ?? 0 }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-danger fs-6">{{ $bo->backorder_qty ?? 0 }}</span>
                                        </td>
                                        <td>{{ $bo->sup_name ?? '—' }}</td>
                                        <td>{{ $bo->proj_name ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                                            No outstanding back orders. All items have been fully received.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar: Supplier Summary --}}
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Summary by Supplier</h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($supplierSummary) && count($supplierSummary) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($supplierSummary as $supplier)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $supplier->sup_name ?? '—' }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $supplier->backorder_count ?? 0 }} item(s) on back order
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-danger rounded-pill fs-6">
                                                {{ $supplier->total_backorder_qty ?? 0 }}
                                            </span>
                                            <br>
                                            <small class="text-muted">units</small>
                                        </div>
                                    </div>
                                    @if(isset($supplier->total_backorder_value))
                                    <div class="mt-1">
                                        <small class="text-muted">
                                            Value: <strong>${{ number_format($supplier->total_backorder_value, 2) }}</strong>
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="card-body text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                            No supplier back orders.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="p-2 bg-light rounded">
                                <small class="text-muted d-block">Total Back Orders</small>
                                <span class="fs-4 fw-bold text-danger">{{ count($backOrders) }}</span>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="p-2 bg-light rounded">
                                <small class="text-muted d-block">Suppliers Affected</small>
                                <span class="fs-4 fw-bold text-warning">{{ isset($supplierSummary) ? count($supplierSummary) : 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#backOrdersTable').DataTable({
        paging: true,
        pageLength: 25,
        searching: true,
        order: [[4, 'desc']],
        columnDefs: [
            { className: 'text-center', targets: [2, 3, 4] }
        ]
    });
});
</script>
@endpush
@endsection
