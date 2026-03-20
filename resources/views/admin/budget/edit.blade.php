@extends('layouts.admin')

@section('title', 'Edit Budget')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-edit me-1"></i> Edit Budget: {{ $budget->project->proj_name ?? '—' }} / {{ $budget->costCode->cc_no ?? '' }}
        </h6>
        <a href="{{ route('admin.budget.show', $budget->budget_id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Budget
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.budget.update', $budget->budget_id) }}">
                @csrf
                @method('PUT')

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Budget Details</h6>
                    </div>
                    <div class="card-body">
                        {{-- Read-only fields --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Project</label>
                                <input type="text" class="form-control" value="{{ $budget->project->proj_name ?? '—' }}" readonly disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cost Code</label>
                                <input type="text" class="form-control"
                                       value="{{ ($budget->costCode->cc_no ?? '') . ' - ' . ($budget->costCode->cc_description ?? '—') }}" readonly disabled>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fiscal Year</label>
                                <input type="text" class="form-control" value="{{ $budget->budget_fiscal_year }}" readonly disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Original Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control"
                                           value="{{ number_format($budget->budget_original_amount, 2) }}" readonly disabled>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Editable fields --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="revised_amount">Revised Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="revised_amount" name="revised_amount"
                                           value="{{ old('revised_amount', $budget->budget_revised_amount) }}"
                                           min="0" step="0.01" required>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Revised amount cannot be less than committed amount (${{ number_format($budget->budget_committed_amount, 2) }})
                                </small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="status">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="1" @selected(old('status', $budget->budget_status) == 1)>Active</option>
                                    <option value="0" @selected(old('status', $budget->budget_status) == 0)>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $budget->budget_notes) }}</textarea>
                        </div>

                        {{-- Summary info --}}
                        <div class="card bg-light">
                            <div class="card-body py-2">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Committed</small>
                                        <strong>${{ number_format($budget->budget_committed_amount, 2) }}</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Spent</small>
                                        <strong>${{ number_format($budget->budget_spent_amount, 2) }}</strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Remaining</small>
                                        <strong class="{{ $budget->remaining_amount < 0 ? 'text-danger' : 'text-success' }}">
                                            ${{ number_format($budget->remaining_amount, 2) }}
                                        </strong>
                                    </div>
                                    <div class="col-md-3">
                                        <small class="text-muted d-block">Utilization</small>
                                        <strong>{{ number_format($budget->utilization_percent, 1) }}%</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-4">
                    <a href="{{ route('admin.budget.show', $budget->budget_id) }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Update Budget
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Validate revised amount against committed
    const committedAmount = {{ $budget->budget_committed_amount }};
    const revisedInput = document.getElementById('revised_amount');

    revisedInput.addEventListener('change', function() {
        const val = parseFloat(this.value) || 0;
        if (val < committedAmount) {
            alert('Revised amount cannot be less than the committed amount ($' + committedAmount.toFixed(2) + ').');
            this.value = committedAmount.toFixed(2);
        }
    });
});
</script>
@endpush
