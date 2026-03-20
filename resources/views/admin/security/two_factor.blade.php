@extends('layouts.admin')

@section('title', 'Two-Factor Authentication')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Two-Factor Authentication</h1>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-1"></i> Back to Dashboard
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-shield-alt me-1"></i> Security Status
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="me-2">Current Status:</span>
                    @if($user && $user->hasTwoFactorEnabled())
                        <span class="badge bg-success">Enabled</span>
                    @else
                        <span class="badge bg-secondary">Disabled</span>
                    @endif
                </div>

                @if(!$user || !$user->hasTwoFactorEnabled())
                    <form action="{{ route('admin.security.2fa.setup') }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key me-1"></i> Generate Setup Secret
                        </button>
                    </form>
                @endif

                @if($pendingSecret)
                    <div class="alert alert-info">
                        <div class="fw-bold mb-2">Step 1: Add this secret to your authenticator app</div>
                        <div class="mb-2">
                            <code style="font-size: 1rem;">{{ $pendingSecret }}</code>
                        </div>
                        <div class="small text-muted">
                            If your app supports QR import URLs, use this: <br>
                            <code>{{ $otpAuthUrl }}</code>
                        </div>
                    </div>

                    <form action="{{ route('admin.security.2fa.confirm') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="otp_code" class="form-label">Step 2: Enter a 6-digit code to confirm</label>
                            <input
                                type="text"
                                name="otp_code"
                                id="otp_code"
                                maxlength="6"
                                class="form-control"
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                required
                            >
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-1"></i> Enable 2FA
                        </button>
                    </form>
                @endif

                @if($user && $user->hasTwoFactorEnabled())
                    <hr>
                    <h6 class="text-danger">Disable Two-Factor Authentication</h6>
                    <p class="text-muted mb-3">Enter your password to disable 2FA on this account.</p>
                    <form action="{{ route('admin.security.2fa.disable') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="password" class="form-label">Current Password</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-times me-1"></i> Disable 2FA
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle me-1"></i> Guidance
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Use Google Authenticator, Microsoft Authenticator, or Authy.</li>
                    <li>Codes refresh every 30 seconds.</li>
                    <li>After enabling, login requires password + one-time code.</li>
                    <li>Keep your authenticator app backup method safe.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
