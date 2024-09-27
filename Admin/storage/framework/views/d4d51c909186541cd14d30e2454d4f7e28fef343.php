<form action="<?php echo e(route('update.mail.set')); ?>" method="POST">
	<?php echo e(csrf_field()); ?>

	<div class="row">
		<div class="col-md-3">
			<label class="text-dark" for=""><?php echo e(__('Sender Name')); ?> :</label>
			<input value="<?php echo e($env_files['MAIL_FROM_NAME']); ?>" type="text" name="MAIL_FROM_NAME" placeholder="<?php echo e(__('Enter sender name')); ?>" class="<?php echo e($errors->has('MAIL_FROM_NAME') ? ' is-invalid' : ''); ?> form-control">
			<?php if($errors->has('email')): ?>
                <span class="text-danger invalid-feedback" role="alert">
                    <strong><?php echo e($errors->first('MAIL_FROM_NAME')); ?></strong>
                </span>
            <?php endif; ?>
        </div>
        <div class="col-md-3">
            <label class="text-dark" for=""><?php echo e(__('Mail Driver')); ?> : <?php echo e(__('(ex. smtp, send mail, mail)')); ?></label>
			<input value="<?php echo e($env_files['MAIL_DRIVER']); ?>" type="text" name="MAIL_DRIVER" placeholder="<?php echo e(__('Enter mail driver')); ?>" class="<?php echo e($errors->has('MAIL_DRIVER') ? ' is-invalid' : ''); ?> form-control">
			<?php if($errors->has('MAIL_DRIVER')): ?>
                <span class="text-danger invalid-feedback" role="alert">
                    <strong><?php echo e($errors->first('MAIL_DRIVER')); ?></strong>
                </span>
            <?php endif; ?>
        </div>
        <div class="col-md-3">
            <label class="text-dark" for=""><?php echo e(__('Mail Host')); ?> : <span class="text-danger">*</span> <?php echo e(__('(ex. smtp.yourdomain.com)')); ?></label> 
			<input value="<?php echo e($env_files['MAIL_HOST']); ?>" type="text" name="MAIL_HOST" placeholder="<?php echo e(__('Enter mail host')); ?>" class="<?php echo e($errors->has('MAIL_HOST') ? ' is-invalid' : ''); ?> form-control" required>
			<?php if($errors->has('MAIL_HOST')): ?>
                <span class="text-danger invalid-feedback" role="alert">
                    <strong><?php echo e($errors->first('MAIL_HOST')); ?></strong>
                </span>
            <?php endif; ?>
        </div>
        <div class="col-md-2">
            <label class="text-dark" for=""><?php echo e(__('Mail Port')); ?> : <span class="text-danger">*</span> <?php echo e(__('(ex. 2525,467)')); ?></label>
			<input value="<?php echo e($env_files['MAIL_PORT']); ?>" type="text" name="MAIL_PORT" placeholder="<?php echo e(__('Enter mail port')); ?>" class="<?php echo e($errors->has('MAIL_PORT') ? ' is-invalid' : ''); ?> form-control" required>
			<?php if($errors->has('MAIL_PORT')): ?>
                <span class="text-danger invalid-feedback" role="alert">
                    <strong><?php echo e($errors->first('MAIL_PORT')); ?></strong>
                </span>
            <?php endif; ?>
        </div>
        <div class="col-md-3">
            <label class="text-dark" for=""><?php echo e(__('Mail Address')); ?> : <span class="text-danger">*</span></label>
            <input value="<?php echo e($env_files['MAIL_FROM_ADDRESS']); ?>" type="text" name="MAIL_FROM_ADDRESS" placeholder="<?php echo e(__('Enter mail address')); ?>" class="<?php echo e($errors->has('MAIL_FROM_ADDRESS') ? ' is-invalid' : ''); ?> form-control" required>
            <?php if($errors->has('MAIL_USERNAME')): ?>
                <span class="text-danger invalid-feedback" role="alert">
                    <strong><?php echo e($errors->first('MAIL_FROM_ADDRESS')); ?></strong>
                </span>
            <?php endif; ?>
        </div>
        <div class="col-md-3">
            <label class="text-dark" for=""><?php echo e(__('Mail User Name')); ?> : <span class="text-danger">*</span> </label>
			<input value="<?php echo e($env_files['MAIL_USERNAME']); ?>" type="text" name="MAIL_USERNAME" placeholder="<?php echo e(__('Enter mail username')); ?>" class="<?php echo e($errors->has('MAIL_USERNAME') ? ' is-invalid' : ''); ?> form-control" required>
			<?php if($errors->has('MAIL_USERNAME')): ?>
                <span class="text-danger invalid-feedback" role="alert">
                    <strong><?php echo e($errors->first('MAIL_USERNAME')); ?></strong>
                </span>
            <?php endif; ?>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-11">
                    <label class="text-dark" for=""><?php echo e(__('Mail Password')); ?> : <span class="text-danger">*</span> </label>
                    <input id="pass_log_id"  placeholder="<?php echo e(__('Please Enter Mail Password')); ?>" class="form-control" type="password" name="MAIL_PASSWORD" value="<?php echo e(env('MAIL_PASSWORD')); ?>">
                    <span toggle="#password-field" class="fa fa-fw fa-eye field_icon toggle-password"></span>
                    </div>
                </div>
                <?php if($errors->has('MAIL_PASSWORD')): ?>
                    <span class="text-danger invalid-feedback form-control" role="alert">
                        <strong><?php echo e($errors->first('MAIL_PASSWORD')); ?></strong>
                    </span>
                <?php endif; ?>
        </div>
        <div class="col-md-3">
            <label class="text-dark" for=""><?php echo e(__('Mail Encryption')); ?> : <?php echo e(__('(ex. TLS/SSL)')); ?></label>
			<input value="<?php echo e($env_files['MAIL_ENCRYPTION']); ?>" type="text" name="MAIL_ENCRYPTION" placeholder="<?php echo e(__('Enter mail encryption')); ?>" class="<?php echo e($errors->has('MAIL_ENCRYPTION') ? ' is-invalid' : ''); ?> form-control">
			<?php if($errors->has('MAIL_ENCRYPTION')): ?>
                <span class="text-danger invalid-feedback" role="alert">
                    <strong><?php echo e($errors->first('MAIL_ENCRYPTION')); ?></strong>
                </span>
            <?php endif; ?>
	
        </div>
	</div>
    <br/>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <button type="reset" class="btn btn-danger-rgba mr-1" title="<?php echo e(__('Reset')); ?>"><i class="fa fa-ban"></i> <?php echo e(__("Reset")); ?></button>
                <button type="submit" class="btn btn-primary-rgba" title="<?php echo e(__('Save')); ?>"><i class="fa fa-check-circle"></i>
                <?php echo e(__("Save")); ?></button>
            </div>
        </div>
    </div>
</form>


<?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/setting/mailsetting.blade.php ENDPATH**/ ?>