@extends('layouts.admin')

@section('title', 'Company Settings - ' . $company->name)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-cog"></i> Settings — {{ $company->name }}
        </h1>
        <a href="{{ route('admin.tenants.show', $company->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Company
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Company Settings</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.tenants.update-settings', $company->id) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                            <select name="currency" id="currency" class="form-select" required>
                                <option value="USD" @selected(($company->settings['currency'] ?? 'USD') == 'USD')>USD - US Dollar</option>
                                <option value="CAD" @selected(($company->settings['currency'] ?? '') == 'CAD')>CAD - Canadian Dollar</option>
                                <option value="EUR" @selected(($company->settings['currency'] ?? '') == 'EUR')>EUR - Euro</option>
                                <option value="GBP" @selected(($company->settings['currency'] ?? '') == 'GBP')>GBP - British Pound</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="timezone" class="form-label">Timezone <span class="text-danger">*</span></label>
                            <select name="timezone" id="timezone" class="form-select" required>
                                <option value="America/New_York" @selected(($company->settings['timezone'] ?? 'America/New_York') == 'America/New_York')>Eastern Time (US & Canada)</option>
                                <option value="America/Chicago" @selected(($company->settings['timezone'] ?? '') == 'America/Chicago')>Central Time (US & Canada)</option>
                                <option value="America/Denver" @selected(($company->settings['timezone'] ?? '') == 'America/Denver')>Mountain Time (US & Canada)</option>
                                <option value="America/Los_Angeles" @selected(($company->settings['timezone'] ?? '') == 'America/Los_Angeles')>Pacific Time (US & Canada)</option>
                                <option value="UTC" @selected(($company->settings['timezone'] ?? '') == 'UTC')>UTC</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="date_format" class="form-label">Date Format <span class="text-danger">*</span></label>
                            <select name="date_format" id="date_format" class="form-select" required>
                                <option value="m/d/Y" @selected(($company->settings['date_format'] ?? 'm/d/Y') == 'm/d/Y')>MM/DD/YYYY</option>
                                <option value="d/m/Y" @selected(($company->settings['date_format'] ?? '') == 'd/m/Y')>DD/MM/YYYY</option>
                                <option value="Y-m-d" @selected(($company->settings['date_format'] ?? '') == 'Y-m-d')>YYYY-MM-DD</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="fiscal_year_start" class="form-label">Fiscal Year Start</label>
                            <input type="date" name="fiscal_year_start" id="fiscal_year_start" class="form-control"
                                   value="{{ $company->settings['fiscal_year_start'] ?? '' }}">
                            <small class="text-muted">Leave blank to use calendar year (January 1)</small>
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                        <a href="{{ route('admin.tenants.show', $company->id) }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Current Settings</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        @foreach($company->settings ?? [] as $key => $value)
                            <tr>
                                <th>{{ ucwords(str_replace('_', ' ', $key)) }}:</th>
                                <td>{{ $value }}</td>
                            </tr>
                        @endforeach
                    </table>
                    @if(empty($company->settings))
                        <p class="text-muted mb-0">No custom settings configured. Using defaults.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
