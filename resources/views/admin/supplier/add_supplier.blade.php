@extends('layouts.admin')

@section('title', 'Create New Supplier')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus-circle me-2"></i>Create New Supplier
            </h1>
        </div>
        <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Suppliers
        </a>
    </div>

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-1"></i> Please fix the following errors:
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Create Form Card --}}
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-truck me-1"></i> Supplier Information
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.suppliers.store') }}" method="POST">
                @csrf

                <div class="row">
                    {{-- Supplier Name --}}
                    <div class="col-md-6 mb-3">
                        <label for="sup_name" class="form-label">Supplier Name <span class="text-danger">*</span></label>
                        <input type="text" name="sup_name" id="sup_name"
                               class="form-control @error('sup_name') is-invalid @enderror"
                               value="{{ old('sup_name') }}" required>
                        @error('sup_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6 mb-3">
                        <label for="sup_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="sup_email" id="sup_email"
                               class="form-control @error('sup_email') is-invalid @enderror"
                               value="{{ old('sup_email') }}" required>
                        @error('sup_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div class="col-md-6 mb-3">
                        <label for="sup_phone" class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="sup_phone" id="sup_phone"
                               class="form-control @error('sup_phone') is-invalid @enderror"
                               value="{{ old('sup_phone') }}" required>
                        @error('sup_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Contact Person --}}
                    <div class="col-md-6 mb-3">
                        <label for="sup_contact_person" class="form-label">Contact Person <span class="text-danger">*</span></label>
                        <input type="text" name="sup_contact_person" id="sup_contact_person"
                               class="form-control @error('sup_contact_person') is-invalid @enderror"
                               value="{{ old('sup_contact_person') }}" required>
                        @error('sup_contact_person')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    {{-- Supplier Type --}}
                    <div class="col-md-6 mb-3">
                        <label for="sup_type" class="form-label">Supplier Type</label>
                        <select name="sup_type" id="sup_type" class="form-select @error('sup_type') is-invalid @enderror">
                            <option value="1" {{ old('sup_type') == 1 ? 'selected' : '' }}>Supplier</option>
                            <option value="2" {{ old('sup_type') == 2 ? 'selected' : '' }}>Subcontractor</option>
                            <option value="3" {{ old('sup_type') == 3 ? 'selected' : '' }}>Both</option>
                        </select>
                        @error('sup_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="row">
                    {{-- Address --}}
                    <div class="col-md-12 mb-3">
                        <label for="sup_address" class="form-label">Address <span class="text-danger">*</span></label>
                        <input type="text" name="sup_address" id="sup_address"
                               class="form-control @error('sup_address') is-invalid @enderror"
                               value="{{ old('sup_address') }}" required>
                        @error('sup_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Details --}}
                    <div class="col-md-12 mb-3">
                        <label for="sup_details" class="form-label">Details / Notes</label>
                        <textarea name="sup_details" id="sup_details" rows="3"
                               class="form-control @error('sup_details') is-invalid @enderror">{{ old('sup_details') }}</textarea>
                        @error('sup_details')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Create Supplier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Additional scripts if needed
</script>
@endpush
