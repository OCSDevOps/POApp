@extends('layouts.admin')

@section('title', 'Budget Details')

@section('content')
<div class="container-fluid">

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 font-weight-bold text-primary">
            <i class="fas fa-wallet me-1"></i> Budget: {{ $budget->project->proj_name ?? '—' }} / {{ $budget->costCode->cc_no ?? '' }} - {{ $budget->costCode->cc_description ?? '—' }}
        </h5>
        <div>
            <a href="{{ route('admin.budget.edit', $budget->budget_id) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="{{ route('admin.budget.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            <button type="button" class="btn btn-danger btn-sm delete-btn"
                    data-url="{{ route('admin.budget.destroy', $budget->budget_id) }}" data-name="{{ ($budget->project->proj_name ?? '') . ' / ' . ($budget->costCode->cc_no ?? '') }}">
                <i class="fas fa-trash me-1"></i> Delete
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-1"></i> Budget Details
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th style="width: 40%;">Project</th>
                            <td>{{ $budget->project->proj_name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Cost Code</th>
                            <td>{{ ($budget->costCode->cc_no ?? '') . ' - ' . ($budget->costCode->cc_description ?? '—') }}</td>
                        </tr>
                        <tr>
                            <th>Fiscal Year</th>
                            <td>{{ $budget->budget_fiscal_year }}</td>
                        </tr>
                        <tr>
                            <th>Original Amount</th>
                            <td>${{ number_format($budget->budget_original_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Revised Amount</th>
                            <td>${{ number_format($budget->budget_revised_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Committed</th>
                            <td>${{ number_format($budget->budget_committed_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Spent</th>
                            <td>${{ number_format($budget->budget_spent_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Remaining</th>
                            <td>
                                <strong class="{{ $budget->remaining_amount < 0 ? 'text-danger' : 'text-success' }}">
                                    ${{ number_format($budget->remaining_amount, 2) }}
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $budget->budget_notes ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($budget->budget_status == 1)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Utilization Card --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-1"></i> Budget Utilization
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $utilization = $budget->utilization_percent;
                        $barColor = $utilization > 90 ? 'bg-danger' : ($utilization >= 75 ? 'bg-warning' : 'bg-success');
                    @endphp
                    <div class="d-flex justify-content-between mb-1">
                        <span>Utilization</span>
                        <strong>{{ number_format($utilization, 1) }}%</strong>
                    </div>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar {{ $barColor }}" role="progressbar"
                             style="width: {{ min($utilization, 100) }}%"
                             aria-valuenow="{{ $utilization }}" aria-valuemin="0" aria-valuemax="100">
                            {{ number_format($utilization, 1) }}%
                        </div>
                    </div>
                    @if($budget->is_over_budget)
                        <div class="alert alert-danger mt-3 mb-0">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            This budget is over the allocated amount.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-invoice me-1"></i> Related Purchase Orders
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped mb-0" id="purchaseOrdersTable">
                            <thead>
                                <tr>
                                    <th>PO #</th>
                                    <th>Date</th>
                                    <th class="text-end">Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($purchaseOrders as $po)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.porder.show', $po->porder_id) }}">
                                                {{ $po->porder_no ?? '—' }}
                                            </a>
                                        </td>
                                        <td>{{ $po->porder_createdate ? \Carbon\Carbon::parse($po->porder_createdate)->format('Y-m-d') : '—' }}</td>
                                        <td class="text-end">${{ number_format($po->total_amount ?? 0, 2) }}</td>
                                        <td>
                                            @if($po->porder_status == 1)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">No purchase orders linked to this budget.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@include('partials.delete-modal')
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#purchaseOrdersTable').DataTable({
        "paging": false,
        "searching": true,
        "ordering": true,
        "info": false,
        "autoWidth": false,
        "responsive": true,
        "order": [[1, 'desc']]
    });
});
</script>
@endpush
