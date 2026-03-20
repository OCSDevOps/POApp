@extends('layouts.admin')

@section('title', 'Edit Project')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-project-diagram"></i> Edit Project: {{ $project->proj_name }}
        </h1>
        <div>
            <a href="{{ route('admin.projects.show', $project->proj_id) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Details
            </a>
            <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Projects
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Project Information</h6>
                </div>
                <div class="card-body">
                    @include('partials.validation-errors')
                    <form action="{{ route('admin.projects.update', $project->proj_id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="proj_name">Project Name <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('proj_name') is-invalid @enderror"
                                           id="proj_name"
                                           name="proj_name"
                                           value="{{ old('proj_name', $project->proj_name) }}"
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
                                           value="{{ old('proj_number', $project->proj_number) }}"
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
                                   value="{{ old('proj_address', $project->proj_address) }}"
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
                                      rows="3">{{ old('proj_description', $project->proj_description) }}</textarea>
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
                                           value="{{ old('proj_contact', $project->proj_contact) }}">
                                    @error('proj_contact')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="proj_status">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('proj_status') is-invalid @enderror"
                                    id="proj_status"
                                    name="proj_status"
                                    required>
                                <option value="1" {{ old('proj_status', $project->proj_status) == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('proj_status', $project->proj_status) == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('proj_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="mt-4 mb-4">

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Project
                            </button>
                            <a href="{{ route('admin.projects.show', $project->proj_id) }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Project Info</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Project ID:</strong><br>
                        {{ $project->proj_id }}
                    </div>
                    @if($project->proj_createdate)
                        <div class="mb-3">
                            <strong>Created:</strong><br>
                            {{ \Carbon\Carbon::parse($project->proj_createdate)->format('M d, Y h:i A') }}
                        </div>
                    @endif
                    @if($project->proj_modifydate)
                        <div class="mb-3">
                            <strong>Last Modified:</strong><br>
                            {{ \Carbon\Carbon::parse($project->proj_modifydate)->format('M d, Y h:i A') }}
                        </div>
                    @endif
                    <div class="alert alert-warning">
                        <small><i class="fas fa-exclamation-triangle"></i> Changing project status to inactive will affect all associated purchase orders.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Edit project form scripts if needed
</script>
@endpush
