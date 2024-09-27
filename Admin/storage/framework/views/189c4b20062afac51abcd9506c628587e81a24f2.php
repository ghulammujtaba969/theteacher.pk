<?php if($image = @file_get_contents('../public/images/user_img/'.$user_img)): ?>
    <img <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        src="<?php echo e(url('images/user_img/'.$user_img)); ?>" alt="profilephoto"
        class="img-responsive img-circle" data-toggle="modal"
        data-target="#exampleStandardModal<?php echo e($id); ?>">
<?php else: ?>
<img <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        src="<?php echo e(Avatar::create($fname)->toBase64()); ?>" alt="profilephoto"
        class="img-responsive img-circle" data-toggle="modal"
        data-target="#exampleStandardModal<?php echo e($id); ?>">

<?php endif; ?>
<div class="modal fade" id="exampleStandardModal2<?php echo e($id); ?>" tabindex="-1"
        role="dialog" aria-labelledby="exampleStandardModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleStandardModalLabel">
                        <?php echo e($fname); ?></h5>
                    <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="col-lg-12">
                        <div class="card m-b-30">
                            <div class="card-body py-5">
<div class="row">
<div class="user-modal">
<?php if($image =
@file_get_contents('../public/images/user_img/'.$user_img)): ?>
<img <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
src="<?php echo e(url('images/user_img/'.$user_img)); ?>"
alt="profilephoto"
class="img-responsive img-circle">
<?php else: ?>
<img <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
src="<?php echo e(Avatar::create($fname)->toBase64()); ?>"
alt="profilephoto"
class="img-responsive img-circle">
<?php endif; ?>
</div>
<div class="col-lg-12">
<h4 class="text-center">
<?php echo e($fname); ?><?php echo e($lname); ?>

</h4>
<div class="button-list mt-4 mb-3">
<button type="button"
class="btn btn-primary-rgba"><i
class="feather icon-email mr-2"></i><?php echo e($email); ?></button>
<button type="button"
class="btn btn-success-rgba"><i
class="feather icon-phone mr-2"></i><?php echo e($mobile); ?></button>
</div>
<div class="table-responsive">
<table
class="table table-borderless mb-0 user-table">
<tbody>
<?php if(isset($dob)): ?>
<tr>
    <th scope="row" class="p-1">
        <?php echo e(__('Date Of Birth :')); ?></th>
    <td class="p-1">
        <?php echo e($dob); ?></td>
</tr>
<?php endif; ?>
<?php if(isset($address)): ?>

<tr>
    <th scope="row" class="p-1">
        <?php echo e(__('Address')); ?> :</th>
    <td class="p-1">
        <?php echo e($address); ?></td>
</tr>
<?php endif; ?>
<?php if(isset($gender)): ?>

<tr>
    <th scope="row" class="p-1">
        <?php echo e(__('Gender')); ?> :</th>
    <td class="p-1">
        <?php echo e($gender); ?></td>
</tr>
<?php endif; ?>

<tr>
    <th scope="row" class="p-1">
        <?php echo e(__('Role')); ?> :</th>
    <td class="p-1">
        <?php echo e($role); ?></td>
</tr>
<?php if($youtube_url != '' & $youtube_url != NULL): ?>

<tr>
    <th scope="row" class="p-1">
        <?php echo e(__('Youtube URL')); ?></th>
    <td class="p-1">
        <a
            href="<?php echo e($youtube_url); ?>"><?php echo e(str_limit($youtube_url, '30')); ?></a>
    </td>
</tr>
<?php endif; ?>

<?php if(isset($fb_url)): ?>

<tr>
    <th scope="row" class="p-1">
       <?php echo e(__(' Facebook URL')); ?></th>
    <td class="p-1">
        <a
            href="<?php echo e($fb_url); ?>"><?php echo e(str_limit($fb_url, '30')); ?></a>
    </td>
</tr>
<?php endif; ?>

<?php if(isset($twitter_url)): ?>

<tr>
    <th scope="row" class="p-1">
        <?php echo e(__('Twitter URL')); ?></th>
    <td class="p-1">
        <a
            href="<?php echo e($twitter_url); ?>"><?php echo e(str_limit($twitter_url, '30')); ?></a>
    </td>
</tr>
<?php endif; ?>

<?php if(isset($linkedin_url)): ?>

<tr>
    <th scope="row" class="p-1">
       <?php echo e(__(' Linkedin URL')); ?></th>
    <td class="p-1">
        <a
            href="<?php echo e($linkedin_url); ?>"><?php echo e(str_limit($linkedin_url, '30')); ?></a>
    </td>
</tr>
<?php endif; ?>

</tbody>
</table>
</div>
</div>
</div>
                            </div><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/allinstructor/image.blade.php ENDPATH**/ ?>