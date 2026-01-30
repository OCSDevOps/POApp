@extends('layout.master')

@section('title', 'Budget Setup - ' . $project->proj_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Budget Setup: {{ $project->proj_name }}</h4>
                    <div>
                        <a href="{{ route('admin.budgets.assign-cost-codes', $project->proj_id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-edit"></i> Modify Cost Codes
                        </a>
                        <a href="{{ route('admin.projects.show', $project->proj_id) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Project
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($assignedCostCodes->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> No cost codes assigned to this project. 
                            <a href="{{ route('admin.budgets.assign-cost-codes', $project->proj_id) }}">Assign cost codes first</a>.
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.budgets.save', $project->proj_id) }}" id="budgetForm">
                            @csrf

                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle"></i> Enter budget amounts for each cost code. Existing budgets can be modified via Budget Change Orders.
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 20%;">Cost Code</th>
                                            <th style="width: 30%;">Description</th>
                                            <th style="width: 15%;">Current Budget</th>
                                            <th style="width: 15%;">Committed</th>
                                            <th style="width: 20%;">New Budget Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assignedCostCodes as $costCode)
                                            @php
                                                $existingBudget = $existingBudgets->get($costCode->cc_id);
                                            @endphp
                                            <tr>
                                                <td>
                                                    <strong>{{ $costCode->getFormattedCode() }}</strong>
                                                    @if($costCode->cc_level == 1)
                                                        <span class="badge bg-primary">Parent</span>
                                                    @elseif($costCode->cc_level == 2)
                                                        <span class="badge bg-info">Category</span>
                                                    @elseif($costCode->cc_level == 3)
                                                        <span class="badge bg-secondary">Subcategory</span>
                                                    @endif
                                                </td>
                                                <td>{{ $costCode->cc_description }}</td>
                                                <td>
                                                    @if($existingBudget)
                                                        <strong>${{ number_format($existingBudget->budget_amount, 2) }}</strong>
                                                        @if($existingBudget->budget_change_orders_total != 0)
                                                            <br><small class="text-muted">
                                                                (Original: ${{ number_format($existingBudget->budget_original_amount, 2) }})
                                                            </small>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">Not set</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($existingBudget)
                                                        ${{ number_format($existingBudget->budget_committed, 2) }}
                                                        @php
                                                            $utilization = $existingBudget->budget_amount > 0 
                                                                ? ($existingBudget->budget_committed / $existingBudget->budget_amount) * 100 
                                                                : 0;
                                                        @endphp
                                                        <br><small class="badge 
                                                            @if($utilization >= 90) bg-danger
                                                            @elseif($utilization >= 75) bg-warning
                                                            @else bg-success
                                                            @endif">
                                                            {{ number_format($utilization, 1) }}%
                                                        </small>
                                                    @else
                                                        <span class="text-muted">$0.00</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <input type="hidden" name="budgets[{{ $loop->index }}][cost_code_id]" value="{{ $costCode->cc_id }}">
                                                    
                                                    @if($existingBudget && $existingBudget->budget_committed > 0)
                                                        <div class="alert alert-warning alert-sm mb-0 p-2">
                                                            <small><i class="fas fa-lock"></i> Budget has commitments. Use Budget Change Order to modify.</small>
                                                        </div>
                                                        <input type="hidden" name="budgets[{{ $loop->index }}][amount]" value="{{ $existingBudget->budget_amount }}">
                                                    @else
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input type="number" 
                                                                   class="form-control budget-amount" 
                                                                   name="budgets[{{ $loop->index }}][amount]" 
                                                                   value="{{ $existingBudget ? $existingBudget->budget_amount : '' }}"
                                                                   step="0.01" 
                                                                   min="0"
                                                                   placeholder="0.00"
                                                                   {{ $existingBudget && $existingBudget->budget_committed > 0 ? 'disabled' : 'required' }}>
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2">Total Project Budget</th>
                                            <th id="currentTotal">${{ number_format($existingBudgets->sum('budget_amount'), 2) }}</th>
                                            <th id="committedTotal">${{ number_format($existingBudgets->sum('budget_committed'), 2) }}</th>
                                            <th id="newTotal">$0.00</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Save Budgets
                                </button>
                                <a href="{{ route('admin.budgets.view', $project->proj_id) }}" class="btn btn-success btn-lg">
                                    <i class="fas fa-chart-line"></i> View Budget Summary
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate total on input change
    $('.budget-amount').on('input', function() {
        calculateTotal();
    });

    // Calculate initial total
    calculateTotal();

    function calculateTotal() {
        let total = 0;
        $('.budget-amount').each(function() {
            const value = parseFloat($(this).val()) || 0;
            total += value;
        });
        $('#newTotal').text('$' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    // Form validation
    $('#budgetForm').on('submit', function(e) {
        let hasValue = false;
        $('.budget-amount').each(function() {
            if ($(this).val() && parseFloat($(this).val()) > 0) {
                hasValue = true;
            }
        });

        if (!hasValue) {
            e.preventDefault();
            alert('Please enter at least one budget amount.');
            return false;
        }
    });
});
</script>
@endpush
@endsection
