<form action="<?php echo e(route('user.quick',$id)); ?>" method="POST">
    <?php echo e(csrf_field()); ?>

    <label class="switch">
        <input class="user" type="checkbox" data-id="<?php echo e($id); ?>"
            name="status" <?php echo e($status == '1' ? 'checked' : ''); ?>>
        <span class="knob"></span>
    </label>
</form><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/allinstructor/status.blade.php ENDPATH**/ ?>