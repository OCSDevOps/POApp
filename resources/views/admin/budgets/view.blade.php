@extends('layouts.admin')

@section('title', 'Budget Summary - ' . $project->proj_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Budget Summary: {{ $project->proj_name }}</h4>
                    <div>
                        <a href="{{ route('admin.budget-change-orders.create', $project->proj_id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-exchange-alt"></i> Create Budget Change Order
                        </a>
                        <a href="{{ route('admin.budgets.setup', $project->proj_id) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit Budgets
                        </a>
                        <a href="{{ route('admin.projects.show', $project->proj_id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Project
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Overall Project Summary -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Total Budget</h6>
                                    <h3>${{ number_format($summary->total_budget, 2) }}</h3>
                                    <small>Original: ${{ number_format($summary->total_original, 2) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Committed</h6>
                                    <h3>${{ number_format($summary->total_committed, 2) }}</h3>
                                    <small>{{ $summary->total_budget > 0 ? number_format(($summary->total_committed / $summary->total_budget) * 100, 1) : 0 }}% of budget</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Available</h6>
                                    <h3>${{ number_format($summary->total_available, 2) }}</h3>
                                    <small>{{ $summary->total_budget > 0 ? number_format(($summary->total_available / $summary->total_budget) * 100, 1) : 0 }}% remaining</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6 class="card-title">Actual Spent</h6>
                                    <h3>${{ number_format($summary->total_actual, 2) }}</h3>
                                    <small>{{ $summary->total_budget > 0 ? number_format(($summary->total_actual / $summary->total_budget) * 100, 1) : 0 }}% of budget</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Budget by Cost Code -->
                    @foreach($groupedSummary as $parentCode => $budgets)
                        @php
                            $parentObj = $parentCodes->firstWhere('cc_parent_code', $parentCode);
                        @endphp
                        
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="fas fa-folder"></i> {{ $parentCode }} - {{ $parentObj->cc_description ?? 'Other' }}
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 15%;">Cost Code</th>
                                                <th style="width: 25%;">Description</th>
                                                <th style="width: 12%;" class="text-end">Budget</th>
                                                <th style="width: 12%;" class="text-end">Committed</th>
                                                <th style="width: 12%;" class="text-end">Actual</th>
                                                <th style="width: 12%;" class="text-end">Available</th>
                                                <th style="width: 12%;" class="text-end">Variance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($budgets as $budget)
                                                @php
                                                    $utilization = $budget->budget > 0 ? ($budget->committed / $budget->budget) * 100 : 0;
                                                    $variance = $budget->budget - $budget->actual;
                                                    $costCode = \App\Models\CostCode::find($budget->cost_code_id);
                                                @endphp
                                                <tr class="budget-row" data-cost-code="{{ $budget->cost_code_id }}">
                                                    <td>
                                                        <strong>{{ $costCode->getFormattedCode() }}</strong>
                                                        @if($costCode->cc_level == 2)
                                                            <span class="badge bg-info">Cat</span>
                                                        @elseif($costCode->cc_level == 3)
                                                            <span class="badge bg-secondary">Sub</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $budget->description }}</td>
                                                    <td class="text-end">
                                                        ${{ number_format($budget->budget, 2) }}
                                                        @if($budget->change_orders != 0)
                                                            <br><small class="text-muted">
                                                                ({{ $budget->change_orders > 0 ? '+' : '' }}${{ number_format($budget->change_orders, 2) }})
                                                            </small>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        ${{ number_format($budget->committed, 2) }}
                                                        <br><span class="badge 
                                                            @if($utilization >= 90) bg-danger
                                                            @elseif($utilization >= 75) bg-warning
                                                            @else bg-success
                                                            @endif">
                                                            {{ number_format($utilization, 1) }}%
                                                        </span>
                                                    </td>
                                                    <td class="text-end">${{ number_format($budget->actual, 2) }}</td>
                                                    <td class="text-end">
                                                        <strong>${{ number_format($budget->available, 2) }}</strong>
                                                    </td>
                                                    <td class="text-end">
                                                        <span class="badge {{ $variance >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $variance >= 0 ? '+' : '' }}${{ number_format($variance, 2) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="2">{{ $parentCode }} Total</th>
                                                <th class="text-end">${{ number_format($budgets->sum('budget'), 2) }}</th>
                                                <th class="text-end">${{ number_format($budgets->sum('committed'), 2) }}</th>
                                                <th class="text-end">${{ number_format($budgets->sum('actual'), 2) }}</th>
                                                <th class="text-end">${{ number_format($budgets->sum('available'), 2) }}</th>
                                                <th class="text-end">
                                                    @php
                                                        $groupVariance = $budgets->sum('budget') - $budgets->sum('actual');
                                                    @endphp
                                                    <span class="badge {{ $groupVariance >= 0 ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $groupVariance >= 0 ? '+' : '' }}${{ number_format($groupVariance, 2) }}
                                                    </span>
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Budget Details Modal -->
                    <div class="modal fade" id="budgetDetailsModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Budget Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body" id="budgetDetailsContent">
                                    <div class="text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </div>
                                </div>
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
    // Show budget details on row click
    $('.budget-row').click(function() {
        const costCodeId = $(this).data('cost-code');
        showBudgetDetails(costCodeId);
    });

    function showBudgetDetails(costCodeId) {
        const modal = new bootstrap.Modal(document.getElementById('budgetDetailsModal'));
        modal.show();

        $.ajax({
            url: '{{ route("admin.budgets.details", ["projectId" => $project->proj_id, "costCodeId" => "__COST_CODE__"]) }}'.replace('__COST_CODE__', costCodeId),
            method: 'GET',
            success: function(response) {
                let html = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Cost Code: ${response.budget.cost_code.cc_full_code || response.budget.cost_code.cc_no}</h6>
                            <p>${response.budget.cost_code.cc_description}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h6>Budget Utilization: ${response.utilization.toFixed(1)}%</h6>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar ${response.utilization >= 90 ? 'bg-danger' : response.utilization >= 75 ? 'bg-warning' : 'bg-success'}" 
                                     style="width: ${response.utilization}%">
                                    ${response.utilization.toFixed(1)}%
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <table class="table table-bordered">
                        <tr>
                            <th>Total Budget:</th>
                            <td class="text-end">$${parseFloat(response.budget.budget_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        </tr>
                        <tr>
                            <th>Committed:</th>
                            <td class="text-end">$${parseFloat(response.budget.budget_committed).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        </tr>
                        <tr>
                            <th>Available:</th>
                            <td class="text-end"><strong>$${parseFloat(response.available).toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
                        </tr>
                        <tr>
                            <th>Actual Spent:</th>
                            <td class="text-end">$${parseFloat(response.budget.budget_actual || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                        </tr>
                    </table>

                    <h6 class="mt-3">Change Orders</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>BCO Number</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                if (response.change_orders && response.change_orders.length > 0) {
                    response.change_orders.forEach(function(co) {
                        html += `
                            <tr>
                                <td>${co.bco_number}</td>
                                <td><span class="badge bg-info">${co.bco_type}</span></td>
                                <td>${co.bco_amount >= 0 ? '+' : ''}$${parseFloat(co.bco_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                                <td><span class="badge bg-${co.bco_status === 'approved' ? 'success' : co.bco_status === 'rejected' ? 'danger' : 'warning'}">${co.bco_status}</span></td>
                                <td>${new Date(co.created_at).toLocaleDateString()}</td>
                            </tr>
                        `;
                    });
                } else {
                    html += '<tr><td colspan="5" class="text-center text-muted">No change orders</td></tr>';
                }

                html += '</tbody></table></div>';
                $('#budgetDetailsContent').html(html);
            },
            error: function() {
                $('#budgetDetailsContent').html('<div class="alert alert-danger">Failed to load budget details</div>');
            }
        });
    }
});
</script>
@endpush
@endsection
