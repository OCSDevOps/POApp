<?php $__env->startSection('content'); ?>
<div class="auth-fluid">
    <div class="auth-fluid-form-box">
        <div class="align-items-center d-flex h-100">
            <div class="card-body">
                <div class="auth-brand text-center text-lg-start mb-4">
                    <h4>Supplier Registration</h4>
                    <p class="text-muted mb-0">Create an account to access the supplier portal.</p>
                </div>

                <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php echo e(route('supplier.register.submit')); ?>">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label class="form-label">Full name</label>
                        <input type="text" class="form-control" name="name" value="<?php echo e(old('name')); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" class="form-control" name="email" value="<?php echo e(old('email')); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone (optional)</label>
                        <input type="text" class="form-control" name="phone" value="<?php echo e(old('phone')); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supplier ID (if provided by staff)</label>
                        <input type="text" class="form-control" name="supplier_id" value="<?php echo e(old('supplier_id')); ?>">
                        <small class="text-muted">Leave blank if you are creating a new supplier account.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm password</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                    <div class="d-grid mb-3">
                        <button class="btn btn-primary" type="submit">Create Account</button>
                    </div>
                    <p class="text-center text-muted mb-0">
                        Already registered? <a href="<?php echo e(route('supplier.login')); ?>">Sign in</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
    <div class="auth-fluid-right text-center d-none d-md-flex">
        <div class="auth-user-testimonial w-100 p-4">
            <h2 class="mb-3">Join our supplier network</h2>
            <p class="lead text-muted">Manage quotes, pricing, and purchase orders in one place.</p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts/auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\SM\Desktop\POApp\html\resources\views/supplier/auth/register.blade.php ENDPATH**/ ?>