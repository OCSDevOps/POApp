@extends('layout.master')

@section('title', 'Create Budget Change Order - ' . $project->proj_name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Create Budget Change Order: {{ $project->proj_name }}</h4>
                    <a href="{{ route('admin.budget-change-orders.index', $project->proj_id) }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($budgets->isEmpty())
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> No budgets set up for this project. 
                            <a href="{{ route('admin.budgets.setup', $project->proj_id) }}">Set up budgets first</a>.
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.budget-change-orders.store', $project->proj_id) }}" id="bcoForm">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Cost Code <span class="text-danger">*</span></label>
                                        <select name="cost_code_id" id="cost_code_id" class="form-select" required>
                                            <option value="">Select Cost Code</option>
                                            @foreach($costCodes as $cc)
                                                <option value="{{ $cc->cc_id }}">
                                                    {{ $cc->getFormattedCode() }} - {{ $cc->cc_description }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Change Order Type <span class="text-danger">*</span></label>
                                        <select name="bco_type" id="bco_type" class="form-select" required>
                                            <option value="">Select Type</option>
                                            <option value="increase">Increase</option>
                                            <option value="decrease">Decrease</option>
                                            <option value="transfer">Transfer</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Budget Info -->
                            <div id="currentBudgetInfo" style="display: none;">
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Current Budget Information</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Current Budget:</strong>
                                                <div id="current_budget" class="fs-5">$0.00</div>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Committed:</strong>
                                                <div id="committed" class="fs-5">$0.00</div>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Actual:</strong>
                                                <div id="actual" class="fs-5">$0.00</div>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Available:</strong>
                                                <div id="available" class="fs-5">$0.00</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">New Budget Amount <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" name="new_budget" id="new_budget" class="form-control" 
                                                   step="0.01" min="0" placeholder="0.00" required>
                                        </div>
                                        <small class="text-muted">Enter the new total budget amount</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Change Amount</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="text" id="change_amount" class="form-control" readonly>
                                        </div>
                                        <small class="text-muted">Calculated automatically</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Transfer Fields -->
                            <div id="transferFields" style="display: none;">
                                <div class="card bg-warning bg-opacity-10 mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Budget Transfer</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Transfer From Cost Code <span class="text-danger">*</span></label>
                                                    <select name="transfer_from_cost_code_id" id="transfer_from_cost_code_id" class="form-select">
                                                        <option value="">Select Source Cost Code</option>
                                                        @foreach($costCodes as $cc)
                                                            <option value="{{ $cc->cc_id }}">
                                                                {{ $cc->getFormattedCode() }} - {{ $cc->cc_description }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Transfer Amount <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" name="transfer_amount" id="transfer_amount" 
                                                               class="form-control" step="0.01" min="0" placeholder="0.00">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Reason for Change <span class="text-danger">*</span></label>
                                <textarea name="reason" id="reason" class="form-control" rows="4" 
                                          placeholder="Explain why this budget change is needed..." required></textarea>
                                <small class="text-muted">Maximum 1000 characters</small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Create Budget Change Order
                                </button>
                                <a href="{{ route('admin.budget-change-orders.index', $project->proj_id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
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
    // Load current budget when cost code selected
    $('#cost_code_id').change(function() {
        const costCodeId = $(this).val();
        if (costCodeId) {
            loadBudgetDetails(costCodeId);
        } else {
            $('#currentBudgetInfo').hide();
        }
    });

    // Show/hide transfer fields
    $('#bco_type').change(function() {
        if ($(this).val() === 'transfer') {
            $('#transferFields').show();
            $('#transfer_from_cost_code_id, #transfer_amount').prop('required', true);
        } else {
            $('#transferFields').hide();
            $('#transfer_from_cost_code_id, #transfer_amount').prop('required', false);
        }
    });

    // Calculate change amount
    $('#new_budget').on('input', function() {
        const current = parseFloat($('#current_budget').text().replace(/[$,]/g, '')) || 0;
        const newBudget = parseFloat($(this).val()) || 0;
        const change = newBudget - current;
        
        $('#change_amount').val((change >= 0 ? '+' : '') + change.toFixed(2))
            .toggleClass('text-success', change >= 0)
            .toggleClass('text-danger', change < 0);
    });

    function loadBudgetDetails(costCodeId) {
        $.ajax({
            url: '{{ route("admin.budget-change-orders.budget-details", $project->proj_id) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                cost_code_id: costCodeId
            },
            success: function(response) {
                $('#current_budget').text('$' + parseFloat(response.current_budget).toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#committed').text('$' + parseFloat(response.committed).toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#actual').text('$' + parseFloat(response.actual).toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#available').text('$' + parseFloat(response.available).toLocaleString('en-US', {minimumFractionDigits: 2}));
                $('#currentBudgetInfo').show();
            },
            error: function() {
                alert('Failed to load budget details');
            }
        });
    }

    // Form validation
    $('#bcoForm').on('submit', function(e) {
        const newBudget = parseFloat($('#new_budget').val()) || 0;
        const currentBudget = parseFloat($('#current_budget').text().replace(/[$,]/g, '')) || 0;
        const committed = parseFloat($('#committed').text().replace(/[$,]/g, '')) || 0;

        if (newBudget < committed) {
            e.preventDefault();
            alert('New budget cannot be less than committed amount ($' + committed.toFixed(2) + ')');
            return false;
        }

        if (newBudget === currentBudget) {
            e.preventDefault();
            alert('New budget must be different from current budget');
            return false;
        }
    });
});
</script>
@endpush
@endsection
