<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="description" content="<?php echo e($gsetting->meta_data_desc); ?>">
    <meta name="keywords" content="<?php echo e($gsetting->meta_data_keyword); ?>">
    <meta name="author" content="<?php echo e(config('app.name')); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <?php if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'): ?>
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> 
    <?php endif; ?>
    <!-- <title><?php echo $__env->yieldContent('title'); ?> | <?php echo e(__('Admin')); ?></title> -->
    <title><?php echo $__env->yieldContent('title'); ?></title>
    <?php echo $__env->make('admin.layouts.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>
<body class="vertical-layout"> 
<div id="containerbar">
    <?php if(Auth::User()->role == "admin"): ?>
    <?php if($gsetting->sidebar_enable == 1): ?>
    <?php echo $__env->make('admin.layouts.new_sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
    <?php echo $__env->make('admin.layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <?php endif; ?>
    <?php if(Auth::User()->role == "instructor"): ?>
    <?php if($gsetting->instructor_sidebar == 1): ?>
    <?php echo $__env->make('admin.layouts.instructor_sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
    <?php echo $__env->make('instructor.layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <?php endif; ?>
    <div class="rightbar">
    
    <?php echo $__env->yieldContent('maincontent'); ?>
    <!-- Start Footerbar -->
    <div class="footerbar">
        <footer class="footer">
          <?php echo e($gsetting->project_title); ?>

            <p class="mb-0">© <?php echo e($gsetting->cpy_txt); ?> <?php echo e(get_release()); ?></p>
        </footer>
    </div>
<!-- End Footerbar -->
</div>
</div>
 <?php echo $__env->make('admin.layouts.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
 <?php echo $__env->yieldContent('script'); ?>
</body>
</html><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/layouts/master.blade.php ENDPATH**/ ?>