<div class="form-group<?php echo e($errors->has('css') ? ' has-error' : ''); ?>">
    <form action="<?php echo e(route('css.store')); ?>" method="POST">
      <?php echo e(csrf_field()); ?>

        <label class="text-dark" for="css"><?php echo e(__('Custom CSS')); ?> :</label>
        <small class="text-danger"><?php echo e($errors->first('css','CSS Cannot be blank !')); ?></small>
        <textarea class="form-control" name="css" id="inputTextarea" rows="3" placeholder="a {
          color:red;
        }"><?php echo e($css); ?></textarea>
    	<br>
      <div class="form-group">
        <button type="reset" class="btn btn-danger-rgba mr-1" title="<?php echo e(__('Reset')); ?>"><i class="fa fa-ban"></i> <?php echo e(__("Reset")); ?></button>
        <button type="submit" class="btn btn-primary-rgba" title="<?php echo e(__('Save')); ?>"><i class="fa fa-check-circle"></i>
        <?php echo e(__("Save")); ?></button>
      </div>
    </form>
</div>
<br>
<div class="form-group<?php echo e($errors->has('js') ? ' has-error' : ''); ?>">
    <form action="<?php echo e(route('js.store')); ?>" method="POST">
      <?php echo e(csrf_field()); ?>

        <label class="text-dark" for="js"><?php echo e(__('Custom JS')); ?> :</label>
        <small class="text-danger"><?php echo e($errors->first('js','Javascript Cannot be blank !')); ?></small>
        <textarea class="form-control" name="js" id="inputTextarea" rows="3" placeholder="$(document).ready(function{
          //code
        });"><?php echo e($js); ?></textarea>
    	<br>
     
        <div class="form-group">
          <button type="reset" class="btn btn-danger-rgba mr-1" title="<?php echo e(__('Reset')); ?>"><i class="fa fa-ban"></i> <?php echo e(__("Reset")); ?></button>
          <button type="submit" class="btn btn-primary-rgba" title="<?php echo e(__('Save')); ?>"><i class="fa fa-check-circle"></i>
          <?php echo e(__("Save")); ?></button>
        </div>
    </form>
</div><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/setting/customstyle.blade.php ENDPATH**/ ?>