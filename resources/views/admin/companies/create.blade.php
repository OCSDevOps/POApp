@extends('layouts.admin')

@section('title', 'Create Company')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-building"></i> Create New Company
        </h1>
        <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Companies
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Company Information</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.companies.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="name">Company Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="subdomain">Subdomain</label>
                            <input type="text" 
                                   class="form-control @error('subdomain') is-invalid @enderror" 
                                   id="subdomain" 
                                   name="subdomain" 
                                   value="{{ old('subdomain') }}"
                                   placeholder="Leave blank to auto-generate from name">
                            @error('subdomain')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Used for URL identification (e.g., company-slug). Auto-generated if left blank.
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox"
                                       class="form-check-input"
                                       id="status"
                                       name="status"
                                       value="1"
                                       checked>
                                <label class="form-check-label" for="status">Active</label>
                            </div>
                        </div>

                        <hr class="mt-4 mb-4">

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Company
                            </button>
                            <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Help</h6>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold">Creating a Company</h6>
                    <p class="small">
                        Each company has its own isolated data (projects, purchase orders, users, etc.).
                    </p>
                    <ul class="small">
                        <li>Company name must be unique</li>
                        <li>Subdomain is used for URL identification</li>
                        <li>Only super admins can create companies</li>
                        <li>New companies start with no users or data</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
