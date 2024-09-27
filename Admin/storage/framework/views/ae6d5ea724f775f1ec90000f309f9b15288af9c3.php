
<?php $__env->startSection('title', 'Import Demo Content'); ?>
<?php $__env->startSection('maincontent'); ?>
<?php
$data['heading'] = 'Import Demo Content';
?>
<?php echo $__env->make('admin.layouts.topbar',$data, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="contentbar">
    <div class="row">
<?php if($errors->any()): ?>  
  <div class="alert alert-danger" role="alert">
  <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>     
  <p><?php echo e($error); ?><button type="button" class="close" data-dismiss="alert" aria-label="Close" title="<?php echo e(__('Close')); ?>">
  <span aria-hidden="true" style="color:red;">&times;</span></button></p>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>  
  </div>
  <?php endif; ?>
  <!-- row started -->
    <div class="col-lg-12">
        <div class="card dashboard-card m-b-30">
                <!-- Card header will display you the heading -->
                <div class="card-header">
                    <h5 class="card-box text-white"><?php echo e(__('Import Demo Content')); ?></h5>
                </div> 
                <!-- card body started -->
                <div class="card-body">
                    <div class="card-body bg-danger">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <small class="process-fonts text-white"><i class="fa fa-info-circle"></i> <?php echo e(__('Important Note')); ?>

                                    <ul class="process-font text-white">
                                        <li>
                                            <?php echo e(__('Please take Backup first.')); ?>

                                        </li>
                                        <li>
                                            <?php echo e(__('ON Click of import data your existing data will remove (except users & settings)')); ?>

                                        </li>
                                        <li>
                                           <?php echo e(__('ON Click of reset data will reset your site (which you see after fresh install).')); ?> <?php echo e(__('Its erase your Demo data and give blank site.')); ?>

                                        </li>
                                        <li>
                                            <?php echo e(__('You get only placeholder images in demo data.')); ?>

                                        </li>
                                        <li>
                                            <?php echo e(__('Demo data refers to sample or placeholder data that is used for demonstration or testing purposes. It is used to show how LMS works, or to test the functionality of a LMS.')); ?>

                                        </li>
                                    </ul>
                                
                            </div>
                        </div>
                    </div>
                    <!-- ========== DemoImport and reset start ===================== -->
                <div class="row">
                    <!-- DemoImport start -->
                    <div class="col-6">
                        <!-- ========== DemoImport start ===================== -->
                        <!-- form start -->
                        <form action="<?php echo e(url('admin/import/demo')); ?>" class="form" method="POST">
                            <?php echo csrf_field(); ?>
                            <!-- row start -->
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- card start -->
                                    <div class="card">
                                        <!-- card body start -->
                                        <div class="card-body">
                                            <!-- row start -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <!-- row start -->
                                                    <div class="row">       
                                                            <!-- DemoImport -->
                                                            <div class="col-md-12">
                                                                <label class="text-dark"><?php echo e(__('Demo Import')); ?> :<span class="text-danger">*</span></label>
                                                                <button type="submit" class="btn btn-danger btn-lg btn-block" title="<?php echo e(__('Demo Import')); ?>">
                                                                    <?php echo e(__('Demo Import')); ?>

                                                                </button>
                                                            </div>
                                                    </div><!-- row end -->
                                                </div><!-- col end -->
                                            </div><!-- row end -->
                                        </div><!-- card body end -->
                                    </div><!-- card end -->
                                </div><!-- col end -->
                            </div><!-- row end -->
                        </form>
                    <!-- form end -->
                    </div>
                    <!-- DemoImport end -->
                    <!-- reset start -->
                    <div class="col-6">
                          <!-- form start -->
                        <form action="<?php echo e(url('admin/reset/demo')); ?>" class="form" method="POST">
                            <?php echo csrf_field(); ?>
                            <!-- row start -->
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- card start -->
                                    <div class="card">
                                        <!-- card body start -->
                                        <div class="card-body">
                                            <!-- row start -->
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <!-- row start -->
                                                    <div class="row">       
                                                        <!-- ResetDemo -->
                                                        <div class="col-md-12">
                                                                <label class="text-dark"><?php echo e(__('Reset Demo')); ?> :<span class="text-danger">*</span></label>
                                                                <button type="submit" class="btn btn-warning btn-lg btn-block" title="<?php echo e(__('Reset Demo')); ?>">
                                                                    <?php echo e(__('Reset Demo')); ?>

                                                                </button>
                                                        </div>
                                                    </div><!-- row end -->
                                                </div><!-- col end -->
                                            </div><!-- row end -->
                                        </div><!-- card body end -->
                                    </div><!-- card end -->
                                </div><!-- col end -->
                            </div><!-- row end -->
                        </form>
                    <!-- form end -->
                    </div>
                     <!-- reset end -->
                </div>
                <!-- ========== DemoImport and reset end ===================== -->
                </div>
                <!-- card body end -->
        </div><!-- col end -->
    </div>
</div>
</div><!-- row end -->
    <br><br>
<?php $__env->stopSection(); ?>
<!-- main content section ended -->
<!-- This section will contain javacsript start -->
<?php $__env->startSection('script'); ?>
<?php $__env->stopSection(); ?>
<!-- This section will contain javacsript end -->
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/database/demo.blade.php ENDPATH**/ ?>