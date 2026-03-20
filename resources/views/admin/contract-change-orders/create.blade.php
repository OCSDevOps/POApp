@extends('layouts.admin')

@section('title', 'Create Change Order for ' . $contract->contract_number)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.contracts.index') }}">Contracts</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.contracts.show', $contract->contract_id) }}">{{ $contract->contract_number }}</a></li>
                    <li class="breadcrumb-item active">New Change Order</li>
                </ol>
            </nav>

            <!-- Contract Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-file-contract"></i> Contract Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted d-block">Contract #</small>
                            <strong>{{ $contract->contract_number }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Project</small>
                            <strong>{{ $contract->project->proj_name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Subcontractor</small>
                            <strong>{{ $contract->supplier->sup_name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Current Value</small>
                            <strong class="text-primary fs-5">${{ number_format($contract->current_value, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create Change Order Form -->
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Create Change Order for {{ $contract->contract_number }}</h4>
                </div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.contract-change-orders.store', $contract->contract_id) }}">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" name="amount" id="amount"
                                           class="form-control @error('amount') is-invalid @enderror"
                                           value="{{ old('amount') }}" required
                                           placeholder="Enter amount (negative for deductions)">
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">Use a negative value for deductions (e.g., -5000.00)</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" rows="4"
                                          class="form-control @error('description') is-invalid @enderror"
                                          required placeholder="Describe the change order...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="reason" class="form-label">Reason</label>
                                <input type="text" name="reason" id="reason"
                                       class="form-control @error('reason') is-invalid @enderror"
                                       value="{{ old('reason') }}"
                                       placeholder="Optional reason for the change order">
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.contracts.show', $contract->contract_id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Contract
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Change Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
