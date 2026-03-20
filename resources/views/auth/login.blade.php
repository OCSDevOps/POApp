{{-- extended main app layout for basic structure --}}
@extends('layouts.auth')

{{-- content of login page starts --}}
@section('content')

    <div class="auth-fluid">
        <!--Auth fluid left content -->
        <div class="auth-fluid-form-box">
            <div class="align-items-center d-flex h-100">
                <div class="card-body">

                    <!-- Logo -->
                    <div class="auth-brand text-center text-lg-start">
                        <a href="index.html" class="logo-dark">
                            <span><img src="assets/images/logo-dark.png" alt="" height="18"></span>
                        </a>
                        <a href="index.html" class="logo-light">
                            <span><img src="assets/images/logo.png" alt="" height="18"></span>
                        </a>
                    </div>

                    {{-- Invalid User Error --}}
                    @if (Session::get('invalid-user'))
                        <div class="alert alert-danger" role="alert">
                            <i class="dripicons-wrong me-2"></i> <strong>Invalid Credentials</strong>!
                        </div>
                    @endif

                    {{-- Invalid Access Error --}}
                    @if ($message=Session::get('error-login'))
                        <div class="alert alert-danger" role="alert">
                            <i class="dripicons-wrong me-2"></i> Please login to access <strong>{{$message}}</strong> page !
                        </div>
                    @endif

                    {{-- Logout Success Message --}}
                    @if ($message=Session::get('logout'))
                        <div class="alert alert-success" role="alert">
                            <i class="dripicons-checkmark me-2"></i> <strong>Successfully logged out !!</strong>
                        </div>
                    @endif

                    <!-- title-->
                    <h4 class="mt-0">Sign In</h4>
                    <p class="text-muted mb-4">Enter your email address and password to access account.</p>

                    <!-- form -->
                    <form action="{{ route('auth.validatelogin') }}" method="post">
                        @csrf
                        <div class="mb-3">
                            <label for="emailaddress" class="form-label">Email address</label>
                            <input class="form-control" type="email" name="email" id="emailaddress" required="" placeholder="Enter your email">
                        </div>
                        <div class="mb-3">
                            {{-- <a href="pages-recoverpw-2.html" class="text-muted float-end"><small>Forgot your password?</small></a> --}}
                            <label for="password" class="form-label">Password</label>
                            <input class="form-control" type="password" name="password" required="" id="password" placeholder="Enter your password">
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="checkbox-signin" name="remember" value="1">
                                <label class="form-check-label" for="checkbox-signin">Remember me</label>
                            </div>
                        </div>
                        <div class="d-grid mb-0 text-center">
                            <button class="btn btn-primary" type="submit"><i class="mdi mdi-login"></i> Log In </button>
                        </div>

                        @if(config('app.debug'))
                        <!-- Demo Login Buttons (only visible in debug mode) -->
                        <div class="mt-3">
                            <p class="text-muted text-center mb-2"><small>Quick Demo Login</small></p>
                            <div class="d-grid gap-1">
                                <button type="button" class="btn btn-outline-success btn-sm demo-login-btn"
                                    data-email="superadmin@demo.com" data-password="admin123">
                                    <i class="mdi mdi-shield-account"></i> Super Admin
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm demo-login-btn"
                                    data-email="admin@demo.com" data-password="admin123">
                                    <i class="mdi mdi-account-cog"></i> Company Admin
                                </button>
                                <button type="button" class="btn btn-outline-info btn-sm demo-login-btn"
                                    data-email="manager@demo.com" data-password="admin123">
                                    <i class="mdi mdi-account-tie"></i> Project Manager
                                </button>
                            </div>
                        </div>
                        @endif
                    </form>
                    <!-- end form-->

                    <!-- Footer-->
                    {{-- <footer class="footer footer-alt">
                        <p class="text-muted">Don't have an account? <a href="pages-register-2.html" class="text-muted ms-1"><b>Sign Up</b></a></p>
                    </footer> --}}

                </div> <!-- end .card-body -->
            </div> <!-- end .align-items-center.d-flex.h-100-->
        </div>
        <!-- end auth-fluid-form-box-->

        <!-- Auth fluid right content -->
        <div class="auth-fluid-right text-center">
            <div class="auth-user-testimonial">
                {{-- <h2 class="mb-3">I love the color!</h2>
                <p class="lead"><i class="mdi mdi-format-quote-open"></i> It's a elegent templete. I love it very much! . <i class="mdi mdi-format-quote-close"></i>
                </p>
                <p>
                    - Hyper Admin User
                </p> --}}
            </div> <!-- end auth-user-testimonial-->
        </div>
        <!-- end Auth fluid right content -->
    </div>
    <!-- end auth-fluid-->

@endsection

@section('js')
<script>
document.querySelectorAll('.demo-login-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var form = this.closest('form');
        var emailEl = form.querySelector('[name="email"]');
        var passEl = form.querySelector('[name="password"]');
        emailEl.value = this.dataset.email;
        passEl.value = this.dataset.password;
        // Remove required so native validation doesn't block programmatic submit
        emailEl.removeAttribute('required');
        passEl.removeAttribute('required');
        form.submit();
    });
});
</script>
@endsection
{{-- content of login page ends --}}
