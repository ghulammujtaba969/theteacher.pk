<form action="<?php echo e(route('seo.set')); ?>" method="POST">
	<?php echo csrf_field(); ?>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group<?php echo e($errors->has('meta_data_desc') ? ' has-error' : ''); ?>">
				<label class="text-dark" for=""><?php echo e(__('Meta Data Description')); ?> :</label>
				<textarea class="form-control" name="meta_data_desc" id="inputTextarea" rows="3" placeholder="<?php echo e(__('Enter description')); ?>"><?php echo e($setting->meta_data_desc); ?></textarea>
				<small class="text-danger"><?php echo e($errors->first('meta_data_desc','Meta data description is required !')); ?></small>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group<?php echo e($errors->has('meta_data_keyword') ? ' has-error' : ''); ?>">
				<label class="text-dark" for=""><?php echo e(__('Meta Data Keywords')); ?> :</label>
				<textarea class="form-control" name="meta_data_keyword" id="inputTextarea" rows="3" placeholder="<?php echo e(__('Use Comma to seprate keyword')); ?>"><?php echo e($setting->meta_data_keyword); ?></textarea>
				<small class="text-danger"><?php echo e($errors->first('meta_data_keyword','Meta Keyword Cannot be blank !')); ?></small>
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-6">
			<div class="form-group<?php echo e($errors->has('google_ana') ? ' has-error' : ''); ?>">
				<label class="text-dark" for=""><?php echo e(__('Google Analytic Key')); ?> :</label>
				<input type="text" class="form-control" placeholder="<?php echo e(__('Enter Google analytic key')); ?>" name="google_ana" value="<?php echo e($setting->google_ana); ?>">
				<small class="text-danger"><?php echo e($errors->first('google_ana','Google analytic Code is required !')); ?></small>
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group<?php echo e($errors->has('fb_pixel') ? ' has-error' : ''); ?>">
				<label class="text-dark" for=""><?php echo e(__('Facebook Pixel Code')); ?> :</label>
				<input type="text" name="fb_pixel" class="form-control" placeholder="<?php echo e(__('Enter Facebook Pixel Code')); ?>" value="<?php echo e($setting->fb_pixel); ?>">
				<small class="text-danger"><?php echo e($errors->first('fb_pixel','Facebook Pixel Code is required !')); ?></small>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<button type="reset" class="btn btn-danger-rgba mr-1" title="<?php echo e(__('Reset')); ?>"><i class="fa fa-ban"></i> <?php echo e(__("Reset")); ?></button>
		<button type="submit" class="btn btn-primary-rgba" title="<?php echo e(__('Save')); ?>"><i class="fa fa-check-circle"></i>
		<?php echo e(__("Save")); ?></button>
	</div>
	
</form>


<?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/setting/seo.blade.php ENDPATH**/ ?>