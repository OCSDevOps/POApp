@extends('layouts.admin')

@section('title', 'Add Integration')

@section('content')
<div class="container-fluid">
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle"></i> Add Integration
        </h1>
        <a href="{{ route('admin.integrations.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Back to Integrations
        </a>
    </div>

    <div class="row">
        {{-- Integration Form --}}
        <div class="col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-link me-1"></i> Integration Details
                    </h6>
                </div>
                <div class="card-body">
                    @include('partials.validation-errors')
                    <form method="POST" action="{{ route('admin.integrations.store') }}">
                        @csrf
                        <input type="hidden" name="company_id" value="{{ session('company_id') }}">

                        <div class="mb-3">
                            <label for="integration_type" class="form-label fw-bold">Integration Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('integration_type') is-invalid @enderror"
                                    id="integration_type"
                                    name="integration_type"
                                    required>
                                <option value="">-- Select Integration Type --</option>
                                <option value="sage" {{ old('integration_type') === 'sage' ? 'selected' : '' }}>
                                    Sage 300
                                </option>
                                <option value="quickbooks" {{ old('integration_type') === 'quickbooks' ? 'selected' : '' }}>
                                    QuickBooks Online
                                </option>
                            </select>
                            @error('integration_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="client_id" class="form-label fw-bold">Client ID <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('client_id') is-invalid @enderror"
                                   id="client_id"
                                   name="client_id"
                                   value="{{ old('client_id') }}"
                                   required
                                   placeholder="Enter your application Client ID">
                            @error('client_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="client_secret" class="form-label fw-bold">Client Secret <span class="text-danger">*</span></label>
                            <input type="password"
                                   class="form-control @error('client_secret') is-invalid @enderror"
                                   id="client_secret"
                                   name="client_secret"
                                   required
                                   placeholder="Enter your application Client Secret">
                            @error('client_secret')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plug me-1"></i> Connect Integration
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Info Panel --}}
        <div class="col-lg-5">
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle me-1"></i> How It Works
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        Connecting an accounting integration follows the <strong>OAuth 2.0</strong> authorization flow:
                    </p>
                    <ol class="ps-3 mb-3">
                        <li class="mb-2">Enter your Client ID and Secret from your accounting platform's developer portal.</li>
                        <li class="mb-2">Click <strong>Connect Integration</strong> to begin the authorization process.</li>
                        <li class="mb-2">You will be redirected to the accounting platform to grant access.</li>
                        <li class="mb-2">After authorization, you will be returned here with a confirmed connection.</li>
                    </ol>
                    <hr>
                    <p class="mb-2"><strong>Supported Platforms:</strong></p>
                    <div class="d-flex gap-3">
                        <div class="text-center">
                            <i class="fas fa-building fa-2x text-primary mb-1"></i>
                            <br><small>Sage 300</small>
                        </div>
                        <div class="text-center">
                            <i class="fas fa-calculator fa-2x text-success mb-1"></i>
                            <br><small>QuickBooks Online</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-triangle me-1"></i> Important
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 ps-3">
                        <li class="mb-1">Your Client Secret is stored securely and will not be displayed again after saving.</li>
                        <li class="mb-1">Ensure your OAuth redirect URI is set to the application's callback URL in your accounting platform.</li>
                        <li>Only one integration per type per company is supported.</li>
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
