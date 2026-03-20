@extends('layouts.auth')

@section('content')
<div class="auth-fluid">
    <div class="auth-fluid-form-box">
        <div class="align-items-center d-flex h-100">
            <div class="card-body">
                <div class="auth-brand text-center text-lg-start mb-4">
                    <h4>Supplier Portal Login</h4>
                    <p class="text-muted mb-0">Sign in with your supplier credentials.</p>
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

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('supplier.login.submit') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        <a href="{{ route('supplier.password.request') }}" class="text-muted">Forgot password?</a>
                    </div>
                    <div class="d-grid mb-3">
                        <button class="btn btn-primary" type="submit">Log In</button>
                    </div>
                    <p class="text-center text-muted mb-0">
                        New supplier? <a href="{{ route('supplier.register') }}">Create an account</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
    <div class="auth-fluid-right text-center d-none d-md-flex">
        <div class="auth-user-testimonial w-100 p-4">
            <h2 class="mb-3">Welcome back!</h2>
            <p class="lead text-muted">Access RFQs, submit quotes, and manage your catalog.</p>
        </div>
    </div>
</div>
@endsection
