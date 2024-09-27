<?php if(Session::has('success')): ?>
    <div class="offset-md-3 col-md-offset-3 col-md-6 animated fadeInDown alert alert-success" role="alert">
       <?php echo e(Session::get('success')); ?>

    </div>
<?php endif; ?>

<?php if(Session::has('delete')): ?>
    <div class="offset-md-3 col-md-offset-3 col-md-6 animated fadeInDown alert alert-danger" role="alert">
       <?php echo e(Session::get('delete')); ?>

    </div>
<?php endif; ?>
<?php /**PATH D:\xampp8.1\htdocs\Personal\theteacher.pk\Admin\resources\views/admin/message.blade.php ENDPATH**/ ?>