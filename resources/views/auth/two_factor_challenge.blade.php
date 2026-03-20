@extends('layouts.auth')

@section('content')
    <div class="auth-fluid">
        <div class="auth-fluid-form-box">
            <div class="align-items-center d-flex h-100">
                <div class="card-body">
                    <div class="auth-brand text-center text-lg-start">
                        <a href="#" class="logo-dark">
                            <span><img src="assets/images/logo-dark.png" alt="" height="18"></span>
                        </a>
                    </div>

                    <h4 class="mt-0">Two-Factor Verification</h4>
                    <p class="text-muted mb-4">Enter the 6-digit code from your authenticator app.</p>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('auth.2fa.verify') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="otp_code" class="form-label">Authentication Code</label>
                            <input
                                class="form-control"
                                type="text"
                                name="otp_code"
                                id="otp_code"
                                maxlength="6"
                                inputmode="numeric"
                                pattern="[0-9]{6}"
                                required
                                autofocus
                                placeholder="123456"
                            >
                        </div>
                        <div class="d-grid mb-2 text-center">
                            <button class="btn btn-primary" type="submit">
                                <i class="mdi mdi-shield-check"></i> Verify Code
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-3">
                        <a href="{{ route('login') }}" class="text-muted">
                            <small>Back to login</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="auth-fluid-right text-center">
            <div class="auth-user-testimonial"></div>
        </div>
    </div>
@endsection
