@extends('layouts.admin')

@section('title', 'Company Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <h4 class="mb-3">Company Profile</h4>
        <div class="card mb-4">
            <div class="card-body">
                @include('partials.validation-errors')
                <form method="POST" action="{{ route('admin.company.update') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="company_name" class="form-control" value="{{ $company->company_name ?? '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <input type="text" class="form-control" value="{{ ($company->company_status ?? 1) ? 'Active' : 'Inactive' }}" disabled>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Address</label>
                            <textarea name="company_address" class="form-control" rows="3" required>{{ $company->company_address ?? '' }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Company Logo</label>
                            <input type="file" name="company_logo" class="form-control">
                            @if(!empty($company->company_logo))
                                <small class="text-muted">Current: {{ $company->company_logo }}</small>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">App Logo 1</label>
                            <input type="file" name="app_logo_one" class="form-control">
                            @if(!empty($company->app_logo_one))
                                <small class="text-muted">Current: {{ $company->app_logo_one }}</small>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">App Logo 2</label>
                            <input type="file" name="app_logo_two" class="form-control">
                            @if(!empty($company->app_logo_two))
                                <small class="text-muted">Current: {{ $company->app_logo_two }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Save Company</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12">
        <h4 class="mb-3">SMTP Settings</h4>
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.company.smtp') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Host</label>
                            <input type="text" name="smtp_host" class="form-control" value="{{ $smtp['smtp_host'] }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Username</label>
                            <input type="text" name="smtp_username" class="form-control" value="{{ $smtp['smtp_username'] }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Password</label>
                            <input type="password" name="smtp_password" class="form-control" value="{{ $smtp['smtp_password'] }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Port</label>
                            <input type="text" name="smtp_port" class="form-control" value="{{ $smtp['smtp_port'] }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Encryption</label>
                            <input type="text" name="smtp_encryption" class="form-control" value="{{ $smtp['smtp_encryption'] }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From Address</label>
                            <input type="email" name="smtp_from_address" class="form-control" value="{{ $smtp['smtp_from_address'] }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">From Name</label>
                            <input type="text" name="smtp_from_name" class="form-control" value="{{ $smtp['smtp_from_name'] }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CC</label>
                            <input type="text" name="smtp_cc" class="form-control" value="{{ $smtp['smtp_cc'] }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">BCC</label>
                            <input type="text" name="smtp_bcc" class="form-control" value="{{ $smtp['smtp_bcc'] }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Mail Body (footer)</label>
                            <textarea name="smtp_mail_body" class="form-control" rows="3">{{ $smtp['smtp_mail_body'] }}</textarea>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary">Save SMTP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
