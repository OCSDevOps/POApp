@extends('layouts.admin')

@section('title', 'Create Budget')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-plus-circle me-1"></i> Create Budget
        </h6>
        <a href="{{ route('admin.budget.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Budgets
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
            <form method="POST" action="{{ route('admin.budget.store') }}">
                @csrf

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Budget Details</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="project_id">Project <span class="text-danger">*</span></label>
                                <select class="form-select" id="project_id" name="project_id" required>
                                    <option value="">-- Select Project --</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->proj_id }}" @selected(old('project_id') == $project->proj_id)>
                                            {{ $project->proj_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="cost_code_id">Cost Code <span class="text-danger">*</span></label>
                                <select class="form-select" id="cost_code_id" name="cost_code_id" required>
                                    <option value="">-- Select Cost Code --</option>
                                    @foreach($costCodes as $costCode)
                                        <option value="{{ $costCode->cc_id }}" @selected(old('cost_code_id') == $costCode->cc_id)>
                                            {{ $costCode->cc_no }} - {{ $costCode->cc_description }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="fiscal_year">Fiscal Year <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="fiscal_year" name="fiscal_year"
                                       value="{{ old('fiscal_year', date('Y')) }}" min="2000" max="2099" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="original_amount">Original Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="original_amount" name="original_amount"
                                           value="{{ old('original_amount') }}" min="0" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-4">
                    <a href="{{ route('admin.budget.index') }}" class="btn btn-secondary me-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Budget
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
    // No additional scripts needed
});
</script>
@endpush
