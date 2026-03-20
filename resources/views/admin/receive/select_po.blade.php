@extends('layouts.admin')

@section('title', 'Select Purchase Order to Receive')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Select Purchase Order to Receive</h4>
        <a href="{{ route('admin.receive.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Receive Orders
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Purchase Orders Available for Receiving</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="selectPoTable">
                    <thead class="table-light">
                        <tr>
                            <th>PO #</th>
                            <th>Project</th>
                            <th>Supplier</th>
                            <th>Date</th>
                            <th class="text-end">Total</th>
                            <th>Delivery Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchaseOrders as $po)
                            <tr>
                                <td><strong>{{ $po->porder_no }}</strong></td>
                                <td>{{ $po->project->proj_name ?? '—' }}</td>
                                <td>{{ $po->supplier->sup_name ?? '—' }}</td>
                                <td>{{ \Carbon\Carbon::parse($po->porder_createdate)->format('m/d/Y') }}</td>
                                <td class="text-end">${{ number_format($po->porder_total_amount, 2) }}</td>
                                <td>
                                    @php
                                        $deliveryStatus = $po->porder_delivery_status ?? 0;
                                    @endphp
                                    <span class="badge
                                        @if($deliveryStatus == 1) bg-success
                                        @elseif($deliveryStatus == 2) bg-warning
                                        @else bg-secondary
                                        @endif">
                                        @if($deliveryStatus == 1) Fully Received
                                        @elseif($deliveryStatus == 2) Partially Received
                                        @else Not Received
                                        @endif
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.receive.create', ['po_id' => $po->porder_id]) }}"
                                       class="btn btn-sm btn-success">
                                        <i class="fas fa-truck-loading"></i> Receive
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-2 d-block"></i>
                                    No purchase orders available for receiving.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#selectPoTable').DataTable({
        paging: true,
        pageLength: 25,
        searching: true,
        order: [[3, 'desc']],
        columnDefs: [
            { orderable: false, targets: -1 }
        ]
    });
});
</script>
@endpush
@endsection
