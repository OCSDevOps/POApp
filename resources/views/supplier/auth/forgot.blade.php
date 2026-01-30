@extends('layouts/auth')

@section('content')
<div class="auth-fluid">
    <div class="auth-fluid-form-box">
        <div class="align-items-center d-flex h-100">
            <div class="card-body">
                <div class="auth-brand text-center text-lg-start mb-4">
                    <h4>Forgot Password</h4>
                    <p class="text-muted mb-0">We will email you a reset link.</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('supplier.password.email') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="d-grid mb-3">
                        <button class="btn btn-primary" type="submit">Send Reset Link</button>
                    </div>
                    <p class="text-center text-muted mb-0">
                        <a href="{{ route('supplier.login') }}">Back to login</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
    <div class="auth-fluid-right text-center d-none d-md-flex">
        <div class="auth-user-testimonial w-100 p-4">
            <h2 class="mb-3">Need help?</h2>
            <p class="lead text-muted">Contact your company admin if you no longer use this email.</p>
        </div>
    </div>
</div>
@endsection
