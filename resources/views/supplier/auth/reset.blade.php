@extends('layouts/auth')

@section('content')
<div class="auth-fluid">
    <div class="auth-fluid-form-box">
        <div class="align-items-center d-flex h-100">
            <div class="card-body">
                <div class="auth-brand text-center text-lg-start mb-4">
                    <h4>Reset Password</h4>
                    <p class="text-muted mb-0">Set a new password for your supplier account.</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('supplier.password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">
                    <div class="mb-3">
                        <label class="form-label">New password</label>
                        <input type="password" class="form-control" name="password" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                    <div class="d-grid mb-3">
                        <button class="btn btn-primary" type="submit">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="auth-fluid-right text-center d-none d-md-flex">
        <div class="auth-user-testimonial w-100 p-4">
            <h2 class="mb-3">Security first</h2>
            <p class="lead text-muted">Use a strong password to keep your account safe.</p>
        </div>
    </div>
</div>
@endsection
