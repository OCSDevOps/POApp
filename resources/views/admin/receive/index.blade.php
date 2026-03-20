@extends('layouts.admin')

@section('title', 'Receive Orders')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fas fa-truck-loading me-2"></i>Receive Orders</h4>
        <div>
            <a href="{{ route('admin.receive.backorderreport') }}" class="btn btn-outline-warning btn-sm me-1">
                <i class="fas fa-exclamation-triangle"></i> Back Order Report
            </a>
            <a href="{{ route('admin.receive.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Create Receive Order
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Summary Cards --}}
    @if(isset($summary) && $summary->count() > 0)
    @php
        $summaryTotalOrders = $summary->count();
        $summaryCompleted = $summary->where('porder_delivery_status', '1')->count();
        $summaryPending = $summary->where('porder_delivery_status', '0')->count();
        $summaryTotalAmount = $summary->sum('rorder_totalamount');
    @endphp
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-primary border-4">
                <div class="card-body py-2">
                    <div class="text-muted small">Total Receive Orders</div>
                    <div class="fs-4 fw-bold">{{ $summaryTotalOrders }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-success border-4">
                <div class="card-body py-2">
                    <div class="text-muted small">Completed</div>
                    <div class="fs-4 fw-bold text-success">{{ $summaryCompleted }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-warning border-4">
                <div class="card-body py-2">
                    <div class="text-muted small">Pending</div>
                    <div class="fs-4 fw-bold text-warning">{{ $summaryPending }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-start border-info border-4">
                <div class="card-body py-2">
                    <div class="text-muted small">Total Amount</div>
                    <div class="fs-4 fw-bold">${{ number_format($summaryTotalAmount, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="receiveOrdersTable">
                    <thead class="table-light">
                        <tr>
                            <th>RO #</th>
                            <th>PO #</th>
                            <th>Project</th>
                            <th>Supplier</th>
                            <th>Slip No</th>
                            <th>Date</th>
                            <th class="text-end">Total Amount</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($receiveOrders as $ro)
                            <tr>
                                <td><strong>{{ $ro->rorder_slip_no }}</strong></td>
                                <td>
                                    @if($ro->purchaseOrder)
                                        <a href="{{ route('admin.porder.show', $ro->purchaseOrder->porder_id) }}">
                                            {{ $ro->purchaseOrder->porder_no }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $ro->purchaseOrder->project->proj_name ?? '—' }}</td>
                                <td>{{ $ro->purchaseOrder->supplier->sup_name ?? '—' }}</td>
                                <td>{{ $ro->rorder_slip_no }}</td>
                                <td>{{ \Carbon\Carbon::parse($ro->rorder_date)->format('m/d/Y') }}</td>
                                <td class="text-end">${{ number_format($ro->rorder_totalamount, 2) }}</td>
                                <td>
                                    <span class="badge
                                        @if($ro->rorder_status == 1) bg-success
                                        @elseif($ro->rorder_status == 0) bg-secondary
                                        @else bg-info
                                        @endif">
                                        @if($ro->rorder_status == 1) Active
                                        @elseif($ro->rorder_status == 0) Inactive
                                        @else {{ $ro->rorder_status }}
                                        @endif
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.receive.show', $ro->rorder_id) }}"
                                       class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.receive.edit', $ro->rorder_id) }}"
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger delete-btn" title="Delete"
                                            data-url="{{ route('admin.receive.destroy', $ro->rorder_id) }}" data-name="{{ $ro->rorder_slip_no }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    No receive orders found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($receiveOrders->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{ $receiveOrders->appends(request()->query())->links() }}
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#receiveOrdersTable').DataTable({
        paging: false,
        info: false,
        searching: true,
        order: [[5, 'desc']],
        columnDefs: [
            { orderable: false, targets: -1 }
        ]
    });
});
</script>
@endpush
@include('partials.delete-modal')
@endsection
