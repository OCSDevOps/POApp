@extends('layouts.admin')

@section('title', 'Procore API Settings')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog"></i> Procore API Settings
        </h1>
        <a href="{{ route('admin.procore.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="row">
        {{-- Settings Form --}}
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-key me-1"></i> API Configuration
                    </h6>
                </div>
                <div class="card-body">
                    @include('partials.validation-errors')
                    <form method="POST" action="{{ route('admin.procore.updatesettings') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="client_id" class="form-label fw-bold">Client ID <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('client_id') is-invalid @enderror"
                                   id="client_id"
                                   name="client_id"
                                   value="{{ old('client_id', $settings['client_id'] ?? '') }}"
                                   required
                                   placeholder="Enter your Procore Client ID">
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="client_secret" class="form-label fw-bold">Client Secret <span class="text-danger">*</span></label>
                            <input type="password"
                                   class="form-control @error('client_secret') is-invalid @enderror"
                                   id="client_secret"
                                   name="client_secret"
                                   placeholder="{{ !empty($settings['client_id']) ? '********** (leave blank to keep existing)' : 'Enter your Procore Client Secret' }}"
                                   {{ empty($settings['client_id']) ? 'required' : '' }}>
                            @error('client_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">For security, the existing secret is not displayed.</div>
                        </div>

                        <div class="mb-3">
                            <label for="company_id" class="form-label fw-bold">Procore Company ID <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('company_id') is-invalid @enderror"
                                   id="company_id"
                                   name="company_id"
                                   value="{{ old('company_id', $settings['company_id'] ?? '') }}"
                                   required
                                   placeholder="Enter your Procore Company ID">
                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Base URL</label>
                            <input type="text"
                                   class="form-control bg-light"
                                   value="{{ $settings['base_url'] ?? 'https://api.procore.com' }}"
                                   readonly
                                   disabled>
                            <div class="form-text">The API base URL is managed by the system and cannot be changed.</div>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Test Connection --}}
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-plug me-1"></i> Connection Test
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Test your Procore API connection to verify that the credentials are valid and the API is reachable.
                    </p>
                    <form method="POST" action="{{ route('admin.procore.testconnection') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-success w-100">
                            <i class="fas fa-satellite-dish me-1"></i> Test Connection
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle me-1"></i> Help
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Where to find these values:</strong></p>
                    <ul class="mb-0 ps-3">
                        <li class="mb-1"><strong>Client ID &amp; Secret</strong> &mdash; Created in the Procore Developer Portal under your App's OAuth credentials.</li>
                        <li class="mb-1"><strong>Company ID</strong> &mdash; Found in your Procore URL or under Company Settings.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // No additional scripts needed for this view
</script>
@endpush
