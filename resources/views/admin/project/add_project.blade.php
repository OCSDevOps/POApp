@extends('layouts.admin')

@section('title', 'Create New Project')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-project-diagram"></i> Create New Project
        </h1>
        <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Projects
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Project Information</h6>
                </div>
                <div class="card-body">
                    @include('partials.validation-errors')
                    <form action="{{ route('admin.projects.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="proj_name">Project Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('proj_name') is-invalid @enderror"
                                           id="proj_name"
                                           name="proj_name"
                                           value="{{ old('proj_name') }}"
                                           required>
                                    @error('proj_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="proj_number">Project Number <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('proj_number') is-invalid @enderror"
                                           id="proj_number"
                                           name="proj_number"
                                           value="{{ old('proj_number') }}"
                                           required>
                                    @error('proj_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="proj_address">Address <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('proj_address') is-invalid @enderror"
                                   id="proj_address"
                                   name="proj_address"
                                   value="{{ old('proj_address') }}"
                                   required>
                            @error('proj_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="proj_description">Description</label>
                            <textarea class="form-control @error('proj_description') is-invalid @enderror"
                                      id="proj_description"
                                      name="proj_description"
                                      rows="3">{{ old('proj_description') }}</textarea>
                            @error('proj_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="proj_contact">Contact</label>
                                    <input type="number"
                                           class="form-control @error('proj_contact') is-invalid @enderror"
                                           id="proj_contact"
                                           name="proj_contact"
                                           value="{{ old('proj_contact', 0) }}">
                                    @error('proj_contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="mt-4 mb-4">

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Project
                            </button>
                            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Project form scripts if needed
</script>
@endpush
