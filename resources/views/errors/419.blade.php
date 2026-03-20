@extends('layouts.auth')

@section('content')
    <div class="account-pages pt-2 pt-sm-5 pb-4 pb-sm-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xxl-4 col-lg-5">
                    <div class="card">
                        <!-- Logo -->
                        <div class="card-header pt-4 pb-4 text-center bg-primary">
                            <a href="index.html">
                                <span><img src="assets/images/logo.png" alt="" height="18"></span>
                            </a>
                        </div>
                        
                        <div class="card-body p-4">

                            <div class="text-center">
                                <img src="assets/images/startman.svg" height="120" alt="File not found Image">

                                <h1 class="text-error mt-4">419</h1>
                                <h4 class="text-uppercase text-danger mt-3">Session Expired</h4>
                                <p class="text-muted mt-3">Click the link below to go to <a href="" class="text-muted"><b>Login Page</b></a></p>

                                <a class="btn btn-info mt-3" href="{{ route('login') }}"><i class="mdi mdi-reply"></i> Login</a>
                            </div>

                        </div> <!-- end card-body-->
                    </div>
                    <!-- end card-->
                    
                </div> <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end page -->

    <footer class="footer footer-alt">
        2018 - 2021 © Hyper - Coderthemes.com
    </footer>
@endsection

