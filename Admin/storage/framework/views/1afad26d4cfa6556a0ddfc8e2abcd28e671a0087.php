<form action="<?php echo e(route('adminsetting.update')); ?>" method="POST">
    <?php echo e(csrf_field()); ?>

    <div class="row">
        <div class="shadow-sm border card col-md-6" style="width: 18rem;">
            <h4 class="card-text"><?php echo e(__('New Sidebar for Admin')); ?></h4>
            <img src="<?php echo e(url('images/sidebar/admin.png')); ?>" class="card-img-top" alt="<?php echo e(__('Side Bar Admin')); ?>">
            <div class="card-body">                
                <div class="custom-radio-button mt-3">
                    <div class="form-check-inline radio-primary">
                        <input type="checkbox" class="custom_toggle" id="customSwitch19" name="sidebar_enable"
                            <?php echo e($gsetting->sidebar_enable == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch19" id="customSwitch19">
                    </div>
                </div>
            </div>
        </div>
        <div class="shadow-sm border card col-md-6" style="width: 18rem;">
            <h4 class="card-text"><?php echo e(__('New Sidebar for Instructor')); ?></h4>
            <img src="<?php echo e(url('images/sidebar/instructor.png')); ?>" class="card-img-top" alt="<?php echo e(__('Side Bar Instructor')); ?>">
            <div class="card-body">                
                <div class="custom-radio-button mt-3">
                    <div class="form-check-inline radio-primary">
                        <input type="checkbox" class="custom_toggle" id="customSwitch20" name="instructor_sidebar"
                            <?php echo e($gsetting->instructor_sidebar == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch20" id="customSwitch20">
                    </div>
                </div>
            </div>
        </div>
        <!-- Apply theme button -->
        <div class="mt-3 col-md-12">
            <div class="form-group">
                <button type="reset" class="btn btn-danger-rgba mr-1" title="<?php echo e(__('Reset')); ?>"><i class="fa fa-ban"></i> <?php echo e(__("Reset")); ?></button>
                <button type="submit" class="btn btn-primary-rgba" title="<?php echo e(__('Update')); ?>"><i class="fa fa-check-circle"></i>
                    <?php echo e(__("Save")); ?></button>
            </div>
        </div>    
    </div>
</form><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/setting/admin.blade.php ENDPATH**/ ?>