<form class="form" action="<?php echo e(action('WhatsappButtonController@update')); ?>" method="POST" novalidate enctype="multipart/form-data">
    <?php echo e(csrf_field()); ?>

    <?php echo e(method_field('POST')); ?>

    <div class="row">
        <div class="col-md-3">            
            <div class="form-group">
                <label class="text-dark"><?php echo e(__('Header Title')); ?> : </label>
                <input name="wapp_title" autofocus="" type="text" class="form-control" placeholder="<?php echo e(__('Enter Header Title')); ?>"  value="<?php echo e($setting['wapp_title']); ?>">
                <div class="invalid-feedback">
                    <?php echo e(__('Please Enter Header Title !')); ?>.
                </div>
            </div>
        </div>
        <div class="col-md-4">            
            <div class="form-group">
                <label class="text-dark"><?php echo e(__('WhatsApp Phone No.')); ?> <?php echo e(__('(with country code)')); ?>:</label>
                <input name="wapp_phone" autofocus="" type="text" class="form-control" placeholder="<?php echo e(__('Please Enter WhatsApp Phone Number')); ?>" value="<?php echo e($setting['wapp_phone']); ?>">
                <div class="invalid-feedback">
                    <?php echo e(__('Please Enter WhatsApp Phone Number !')); ?>.
                </div>
            </div>
        </div>
        <div class="col-md-3">
        <div class="form-group">
                <label class="text-dark"><?php echo e(__('WhatsApp PopUp Msg')); ?> :</label>
                <input name="wapp_popup_msg" autofocus="" type="text" class="form-control" placeholder="<?php echo e(__('PopUp Message')); ?>"  value="<?php echo e($setting['wapp_popup_msg']); ?>">
                <div class="invalid-feedback">
                    <?php echo e(__('Please Enter WhatsApp PopUp Message !')); ?>.
                </div>
            </div>
        </div>
        <div class="col-md-2">            
            <div class="form-group">
                <label class="text-dark" for="number"><?php echo e(__('WhatsApp Color')); ?> :</label>
                <input name="wapp_color" autofocus="" type="color" class="form-control my-colorpicker1" placeholder="<?php echo e(__('Choose color')); ?>" value="<?php echo e($setting['wapp_color']); ?>">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="text-dark"><?php echo e(__('Button Position')); ?> (<?php echo e(__('Right/left')); ?>):</label><br>
                <input  class="custom_toggle"  type="checkbox" name="wapp_position"  <?php echo e($setting['wapp_position'] == 'left' ? 'checked' : ''); ?> />
                <input type="hidden"  name="free" value="0" for="left" id="left">              
            </div>
        </div>
        <div class="form-group col-md-3">
            <label class="text-dark" for="exampleInputDetails"><?php echo e(__('Enable WhatsApp Chat Button')); ?> :</label><br>
            <input type="checkbox" class="custom_toggle" name="wapp_enable" <?php echo e($setting['wapp_enable'] == '1' ? 'checked' : ''); ?> />
            <input type="hidden"  name="free" value="0" for="status" id="status">
        </div>
    </div>
    <div class="form-group">
        <button type="reset" class="btn btn-danger-rgba mr-1" title="<?php echo e(__('Reset')); ?>"><i class="fa fa-ban"></i> <?php echo e(__("Reset")); ?></button>
        <button type="submit" class="btn btn-primary-rgba" title="<?php echo e(__('Save')); ?>"><i class="fa fa-check-circle"></i>
            <?php echo e(__("Save")); ?></button>
    </div>
    
</form><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/setting/whatsapp.blade.php ENDPATH**/ ?>