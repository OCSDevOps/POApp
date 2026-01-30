<?php $__env->startSection('content'); ?>

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

                    
                    <?php if(Session::get('invalid-user')): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="dripicons-wrong me-2"></i> <strong>Invalid Credentials</strong>!
                        </div>
                    <?php endif; ?>

                    
                    <?php if($message=Session::get('error-login')): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="dripicons-wrong me-2"></i> Please login to access <strong><?php echo e($message); ?></strong> page !
                        </div>
                    <?php endif; ?>

                    
                    <?php if($message=Session::get('logout')): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="dripicons-checkmark me-2"></i> <strong>Successfully logged out !!</strong>
                        </div>
                    <?php endif; ?>

                    <!-- title-->
                    <h4 class="mt-0">Sign In</h4>
                    <p class="text-muted mb-4">Enter your email address and password to access account.</p>

                    <!-- form -->
                    <form action="<?php echo e(route('auth.validatelogin')); ?>" method="post">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="emailaddress" class="form-label">Email address</label>
                            <input class="form-control" type="email" name="email" id="emailaddress" required="" placeholder="Enter your email">
                        </div>
                        <div class="mb-3">
                            
                            <label for="password" class="form-label">Password</label>
                            <input class="form-control" type="password" name="password" required="" id="password" placeholder="Enter your password">
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="checkbox-signin">
                                <label class="form-check-label" for="checkbox-signin">Remember me</label>
                            </div>
                        </div>
                        <div class="d-grid mb-0 text-center">
                            <button class="btn btn-primary" type="submit"><i class="mdi mdi-login"></i> Log In </button>
                        </div>
                        <!-- social-->
                        
                    </form>
                    <!-- end form-->

                    <!-- Footer-->
                    

                </div> <!-- end .card-body -->
            </div> <!-- end .align-items-center.d-flex.h-100-->
        </div>
        <!-- end auth-fluid-form-box-->

        <!-- Auth fluid right content -->
        <div class="auth-fluid-right text-center">
            <div class="auth-user-testimonial">
                
            </div> <!-- end auth-user-testimonial-->
        </div>
        <!-- end Auth fluid right content -->
    </div>
    <!-- end auth-fluid-->

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\SM\Desktop\POApp\html\resources\views/auth/login.blade.php ENDPATH**/ ?>