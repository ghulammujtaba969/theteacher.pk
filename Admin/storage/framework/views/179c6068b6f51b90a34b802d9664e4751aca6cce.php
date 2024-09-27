<div class="col-md-6">
    <div class="form-group">
        <label class="text-dark"
            for="promo_text"><?php echo e(__('Facebook Chat Bubble')); ?>:</label>
        <input form="settingsform" value="<?php echo e($setting->chat_bubble); ?>" name="chat_bubble"
            placeholder="https://app.respond.io/facebook/chat/plugin/XXXX/XXXXXXXXXX" type="url"
            class="<?php echo e($errors->has('chat_bubble') ? ' is-invalid' : ''); ?> form-control">
        <small><?php echo e(__('Facebook Bubble Chat will not work on localhost (eg. xampp & wampp)')); ?></small>
        <br>
        <small><a target="__blank"
            href="https://app.respond.io/" title="<?php echo e(__('Get URL For Facebook Messenger Chat Bubble')); ?>"><?php echo e(__('Get URL For Facebook Messenger Chat Bubble')); ?></a></small>
    </div>
    <div class="form-group">
        <button type="reset" form="settingsform" class="btn btn-danger-rgba mr-1" title="<?php echo e(__('Reset')); ?>"><i class="fa fa-ban"></i>
            <?php echo e(__("Reset")); ?></button>
        <button type="submit" form="settingsform" class="btn btn-primary-rgba" title="<?php echo e(__('Save')); ?>"><i class="fa fa-check-circle"></i>
            <?php echo e(__("Save")); ?></button>
    </div>
</div><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/setting/facebook.blade.php ENDPATH**/ ?>