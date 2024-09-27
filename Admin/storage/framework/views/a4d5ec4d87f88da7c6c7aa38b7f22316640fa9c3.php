<!DOCTYPE html>
<!--
**********************************************************************************************************
    Copyright (c) 2023.
**********************************************************************************************************  -->
<!-- 
Template Name: eClass - Learning Management System 
Version: 5.9.0
Author: Media City
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]> -->
<?php
$language = Session::get('changed_language'); //or 'english' //set the system language
$rtl = array('ar','he','ur', 'arc', 'az', 'dv', 'ku', 'fa'); //make a list of rtl languages
?>
<html lang="en" <?php if(in_array($language,$rtl)): ?> dir="rtl" <?php endif; ?>>
<!-- <![endif]-->
<!-- head -->
<head>
<?php echo $__env->make('theme.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</head>
<?php if($gsetting->cookie_enable == '1'): ?> <!-- Cookies Message --> 
<?php echo $__env->make('cookieConsent::index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<!-- end head -->
<!-- body start-->
<body>
<?php if(env('GOOGLE_TAG_MANAGER_ENABLED') == 'true' && env('GOOGLE_TAG_MANAGER_ID') == !NULL): ?> <!-- Google Tag manager --> 
<?php echo $__env->make('googletagmanager::body', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>
<!-- preloader --> 
<?php if($errors->any()): ?>
<div class="alert alert-danger">
  <ul>
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <li><?php echo e($error); ?></li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </ul>
</div>
<?php endif; ?>
<?php if($gsetting->preloader_enable == 1): ?>
<div class="preloader">
    <div class="status">
      <?php if(isset($gsetting->preloader_logo)): ?>
        <div class="status-message">
        	<img src="<?php echo e(asset('images/logo/'.$gsetting['preloader_logo'])); ?>" alt="logo" class="img-fluid">
        </div>
      <?php endif; ?>
    </div>
</div>
<?php endif; ?>
<!-- whatsapp chat button -->
<div id="myButton"></div>
<?php
  if(isset(Auth::user()->orders)){
      //Run User Enroll expire background process
      App\Jobs\EnrollExpire::dispatchNow();
  }

  if(env('ENABLE_INSTRUCTOR_SUBS_SYSTEM') == 1){

    if(isset(Auth::user()->plans)){
        //Run User Plan Subscription expire background process
        App\Jobs\InstructorPlan::dispatchNow();
    }
  }
?>
<!-- end preloader -->
<!-- top-nav bar start-->
<?php echo $__env->make('theme.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!-- top-nav bar end-->
<!-- home start -->
<?php echo $__env->yieldContent('content'); ?>
<!-- testimonial end -->
<!-- footer start -->
<?php echo $__env->make('theme.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!-- footer end -->
<!-- jquery -->
<?php echo $__env->make('theme.scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<!-- end jquery -->
</body>
<!-- body end -->
</html> 
<?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/theme/master.blade.php ENDPATH**/ ?>