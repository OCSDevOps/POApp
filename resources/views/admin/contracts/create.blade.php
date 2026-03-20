@extends('layouts.admin')

@section('title', 'Create Contract')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 font-weight-bold text-primary">
            <i class="fas fa-plus-circle me-1"></i> Create Contract
        </h5>
        <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to Contracts
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

    <form method="POST" action="{{ route('admin.contracts.store') }}" enctype="multipart/form-data" id="contractForm">
        @csrf

        {{-- Contract Details --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-1"></i> Contract Details
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
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
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="supplier_id">Subcontractor <span class="text-danger">*</span></label>
                        <select class="form-select" id="supplier_id" name="supplier_id" required>
                            <option value="">-- Select Subcontractor --</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->sup_id }}" @selected(old('supplier_id') == $supplier->sup_id)>
                                    {{ $supplier->sup_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="cost_code_id">Cost Code</label>
                        <select class="form-select" id="cost_code_id" name="cost_code_id">
                            <option value="">-- Select Cost Code --</option>
                            @foreach($costCodes as $costCode)
                                <option value="{{ $costCode->cc_id }}" @selected(old('cost_code_id') == $costCode->cc_id)>
                                    {{ $costCode->cc_description }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label" for="title">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title"
                               value="{{ old('title') }}" placeholder="Contract title" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"
                              placeholder="Brief description of the contract">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Financial Details --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-dollar-sign me-1"></i> Financial Details
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="original_value">Original Value <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="original_value" name="original_value"
                                   value="{{ old('original_value') }}" min="0" step="0.01" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="retention_percentage">Retention % <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="retention_percentage" name="retention_percentage"
                                   value="{{ old('retention_percentage', 10) }}" min="0" max="100" step="0.01">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="start_date">Start Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                               value="{{ old('start_date') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="end_date">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                               value="{{ old('end_date') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Scope & Terms --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-clipboard-list me-1"></i> Scope & Terms
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label" for="scope_of_work">Scope of Work</label>
                    <textarea class="form-control" id="scope_of_work" name="scope_of_work" rows="5"
                              placeholder="Describe the scope of work for this contract">{{ old('scope_of_work') }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="terms_conditions">Terms & Conditions</label>
                    <textarea class="form-control" id="terms_conditions" name="terms_conditions" rows="5"
                              placeholder="Contract terms and conditions">{{ old('terms_conditions') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Documents --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-paperclip me-1"></i> Documents
                </h6>
            </div>
            <div class="card-body">
                <div id="document-rows">
                    <div class="row g-3 mb-3 document-row">
                        <div class="col-md-5">
                            <label class="form-label">File</label>
                            <input type="file" class="form-control" name="documents[]" multiple>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Document Type</label>
                            <select class="form-select" name="document_types[]">
                                <option value="">-- Select Type --</option>
                                <option value="COI">COI</option>
                                <option value="Signed Contract">Signed Contract</option>
                                <option value="W-9">W-9</option>
                                <option value="Lien Waiver">Lien Waiver</option>
                                <option value="Insurance Certificate">Insurance Certificate</option>
                                <option value="License">License</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-success btn-sm add-document-row">
                                <i class="fas fa-plus me-1"></i> Add More
                            </button>
                        </div>
                    </div>
                </div>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i> You can select multiple files per row or add additional rows for different document types.
                </small>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i> Create Contract
            </button>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add document row
    $(document).on('click', '.add-document-row', function() {
        var newRow = `
            <div class="row g-3 mb-3 document-row">
                <div class="col-md-5">
                    <input type="file" class="form-control" name="documents[]" multiple>
                </div>
                <div class="col-md-4">
                    <select class="form-select" name="document_types[]">
                        <option value="">-- Select Type --</option>
                        <option value="COI">COI</option>
                        <option value="Signed Contract">Signed Contract</option>
                        <option value="W-9">W-9</option>
                        <option value="Lien Waiver">Lien Waiver</option>
                        <option value="Insurance Certificate">Insurance Certificate</option>
                        <option value="License">License</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-document-row">
                        <i class="fas fa-times me-1"></i> Remove
                    </button>
                </div>
            </div>`;
        $('#document-rows').append(newRow);
    });

    // Remove document row
    $(document).on('click', '.remove-document-row', function() {
        $(this).closest('.document-row').remove();
    });

    // Validate end date is after start date
    $('#end_date').on('change', function() {
        var startDate = $('#start_date').val();
        var endDate = $(this).val();
        if (startDate && endDate && endDate < startDate) {
            alert('End date must be after the start date.');
            $(this).val('');
        }
    });
});
</script>
@endpush
