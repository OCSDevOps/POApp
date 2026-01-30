@extends('layouts/auth')

@section('content')
<div class="auth-fluid">
    <div class="auth-fluid-form-box">
        <div class="align-items-center d-flex h-100">
            <div class="card-body">
                <div class="auth-brand text-center text-lg-start mb-4">
                    <h4>Verify Your Email</h4>
                    <p class="text-muted mb-0">We sent a verification link to your email. Please verify to continue.</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('supplier.verification.send') }}">
                    @csrf
                    <div class="d-grid mb-3">
                        <button class="btn btn-primary" type="submit">Resend Verification Email</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <form method="POST" action="{{ route('supplier.logout') }}">
                        @csrf
                        <button class="btn btn-link p-0" type="submit">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="auth-fluid-right text-center d-none d-md-flex">
        <div class="auth-user-testimonial w-100 p-4">
            <h2 class="mb-3">Almost there!</h2>
            <p class="lead text-muted">Check your inbox and click the link to finish setting up your account.</p>
        </div>
    </div>
</div>
@endsection
