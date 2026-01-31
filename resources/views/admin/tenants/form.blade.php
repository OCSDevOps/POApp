@extends('layouts.admin')

@section('title', isset($company) ? 'Edit Company' : 'Create Company')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas {{ isset($company) ? 'fa-edit' : 'fa-plus' }}"></i>
                        {{ isset($company) ? 'Edit Company' : 'Create New Company' }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" 
                          action="{{ isset($company) ? route('admin.tenants.update', $company->id) : route('admin.tenants.store') }}">
                        @csrf
                        @if(isset($company))
                            @method('PUT')
                        @endif

                        <h5 class="mb-3 text-primary">Company Information</h5>
                        
                        <div class="form-group row">
                            <label for="name" class="col-sm-3 col-form-label">Company Name <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" 
                                       value="{{ old('name', $company->name ?? '') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="subdomain" class="col-sm-3 col-form-label">Subdomain</label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <input type="text" class="form-control @error('subdomain') is-invalid @enderror" 
                                           id="subdomain" name="subdomain" 
                                           value="{{ old('subdomain', $company->subdomain ?? '') }}"
                                           placeholder="company-name">
                                    <div class="input-group-append">
                                        <span class="input-group-text">.poapp.com</span>
                                    </div>
                                </div>
                                <small class="form-text text-muted">Lowercase letters, numbers, and hyphens only.</small>
                                @error('subdomain')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="email" class="col-sm-3 col-form-label">Email</label>
                            <div class="col-sm-9">
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" 
                                       value="{{ old('email', $company->email ?? '') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="phone" class="col-sm-3 col-form-label">Phone</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" 
                                       value="{{ old('phone', $company->phone ?? '') }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="address" class="col-sm-3 col-form-label">Address</label>
                            <div class="col-sm-9">
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="2">{{ old('address', $company->address ?? '') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="city" class="col-sm-3 col-form-label">City</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                       id="city" name="city" 
                                       value="{{ old('city', $company->city ?? '') }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="state" class="col-sm-3 col-form-label">State</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                       id="state" name="state" 
                                       value="{{ old('state', $company->state ?? '') }}">
                                @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="zip" class="col-sm-3 col-form-label">ZIP Code</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('zip') is-invalid @enderror" 
                                       id="zip" name="zip" 
                                       value="{{ old('zip', $company->zip ?? '') }}">
                                @error('zip')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="country" class="col-sm-3 col-form-label">Country</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                       id="country" name="country" 
                                       value="{{ old('country', $company->country ?? 'USA') }}">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        @if(isset($company))
                            <div class="form-group row">
                                <label for="status" class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-9">
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" name="status">
                                        <option value="1" {{ old('status', $company->status) == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $company->status) == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        @if(!isset($company))
                            <hr class="my-4">
                            <h5 class="mb-3 text-primary">Admin User</h5>
                            
                            <div class="form-group row">
                                <label for="admin_name" class="col-sm-3 col-form-label">Admin Name <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control @error('admin_name') is-invalid @enderror" 
                                           id="admin_name" name="admin_name" 
                                           value="{{ old('admin_name') }}" required>
                                    @error('admin_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="admin_email" class="col-sm-3 col-form-label">Admin Email <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control @error('admin_email') is-invalid @enderror" 
                                           id="admin_email" name="admin_email" 
                                           value="{{ old('admin_email') }}" required>
                                    @error('admin_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="admin_password" class="col-sm-3 col-form-label">Admin Password <span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control @error('admin_password') is-invalid @enderror" 
                                           id="admin_password" name="admin_password" required>
                                    <small class="form-text text-muted">Minimum 8 characters.</small>
                                    @error('admin_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        <div class="form-group row mt-4">
                            <div class="col-sm-9 offset-sm-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ isset($company) ? 'Update Company' : 'Create Company' }}
                                </button>
                                <a href="{{ route('admin.tenants.index') }}" class="btn btn-secondary">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
