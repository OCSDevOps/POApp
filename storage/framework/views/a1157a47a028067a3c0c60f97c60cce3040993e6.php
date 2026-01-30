<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8" />
        <title>Log In</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
        <meta content="Coderthemes" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="<?php echo e(asset('assets/images/favicon.ico')); ?>">

        <!-- App css -->
        <link href="<?php echo e(asset('assets/css/icons.min.css')); ?>" rel="stylesheet" type="text/css" />
        <link href="<?php echo e(asset('assets/css/app.min.css')); ?>" rel="stylesheet" type="text/css" id="light-style" />
        <link href="<?php echo e(asset('assets/css/app-dark.min.css')); ?>" rel="stylesheet" type="text/css" id="dark-style" />

        

        <?php echo $__env->yieldContent('css'); ?>

    </head>

    <body class="authentication-bg pb-0" data-layout-config='{"darkMode":false}'>

        

        <?php echo $__env->yieldContent('content'); ?>

        <!-- bundle -->
        <script src="<?php echo e(asset('assets/js/vendor.min.js')); ?>"></script>
        <script src="<?php echo e(asset('assets/js/app.min.js')); ?>"></script>

        

        <?php echo $__env->yieldContent('js'); ?>

    </body>

</html>
<?php /**PATH C:\Users\SM\Desktop\POApp\html\resources\views/layouts/auth.blade.php ENDPATH**/ ?>