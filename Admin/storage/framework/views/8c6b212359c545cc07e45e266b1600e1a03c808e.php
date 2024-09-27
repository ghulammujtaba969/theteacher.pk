
<?php $__env->startSection('title','Create a new user'); ?>
<?php $__env->startSection('maincontent'); ?>
<?php
$data['heading'] = 'Create A New User';
$data['title'] = 'User';
$data['title1'] = 'Create A New User';
?>
<?php echo $__env->make('admin.layouts.topbar',$data, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="contentbar">
  <?php if($errors->any()): ?>
<div class="alert alert-danger">
  <ul>
    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <li><?php echo e($error); ?></li>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </ul>
</div>
<?php endif; ?>
  <div class="row">
    <div class="col-lg-12">
      <div class="card dashboard-card m-b-30">
        <div class="card-header">
          <h5 class="box-tittle"><?php echo e(__('Add')); ?> <?php echo e(__('User')); ?></h5>
          <div>
            <div class="widgetbar">
              <a href="<?php echo e(route('user.index')); ?>" class="float-right btn btn-primary-rgba mr-2" title="<?php echo e(__('Back')); ?>"><i
                  class="feather icon-arrow-left mr-2"></i><?php echo e(__('Back')); ?></a> </div>
          </div>
        </div>
        <div class="card-body">
          <form action="<?php echo e(route('user.store')); ?>" method="POST" enctype="multipart/form-data">
            <?php echo e(csrf_field()); ?>

            <div class="bg-info-rgba p-4 mb-4">
              <h4 class="pb-4"><?php echo e(__('Personal Details')); ?></h4>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="text-dark" for="fname">
                      <?php echo e(__('First Name')); ?>:<sup class="text-danger">*</sup>
                    </label>
                    <input value="<?php echo e(old('fname')); ?>" autofocus required name="fname" type="text" class="form-control"
                      placeholder="<?php echo e(__('Please')); ?> <?php echo e(__('Enter First Name')); ?>" />
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="text-dark" for="lname">
                      <?php echo e(__('Last Name')); ?>:<sup class="text-danger">*</sup>
                    </label>
                    <input value="<?php echo e(old('lname')); ?>" required name="lname" type="text" class="form-control"
                      placeholder="<?php echo e(__('Please')); ?> <?php echo e(__('Enter Last Name')); ?>" />
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="text-dark" for="mobile"><?php echo e(__('Email')); ?>: <sup
                        class="text-danger">*</sup></label>
                    <input value="<?php echo e(old('email')); ?>" required type="email" name="email"
                      placeholder=" <?php echo e(__('Please')); ?> <?php echo e(__('Enter Email')); ?>"
                      class="form-control">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="text-dark" for="mobile"><?php echo e(__('Mobile')); ?>: <sup class="text-danger">*</sup></label>
                    <input value="<?php echo e(old('mobile')); ?>" required type="text" name="mobile"
                      placeholder="<?php echo e(__('Please')); ?> <?php echo e(__('Enter Mobile')); ?>"
                      class="form-control">
                  </div>
                </div>                
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="text-dark" for="role"><?php echo e(__('Select User Role')); ?>: <sup
                        class="text-danger">*</sup></label>
                    <select class="form-control select2" name="role" required>
                      <option value="none" selected disabled hidden>
                        <?php echo e(__('Please')); ?> <?php echo e(__('Select an Option')); ?>

                      </option>
                      <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option <?php echo e(app('request')->input('type') == $role->name ? "selected" : ""); ?> value="<?php echo e($role->name); ?>"><?php echo e($role->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                  </div>
                </div>                
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="text-dark" for="mobile"><?php echo e(__('Password')); ?>: <sup
                        class="text-danger">*</sup> </label>
                    <input required type="password" name="password"
                      placeholder="<?php echo e(__('Please')); ?> <?php echo e(__('Enter Password')); ?>"
                      class="form-control">
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label class="text-dark" for="exampleInputDetails"><?php echo e(__('Detail')); ?>:</label>
                    <textarea id="detail" name="detail" rows="1" class="form-control"
                      placeholder="<?php echo e(__('Please')); ?> <?php echo e(__('Enter Detail')); ?>"></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-info-rgba p-4 mb-4">
              <h4 class="pb-4"><?php echo e(__('Address')); ?></h4>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="text-dark" for="exampleInputDetails"><?php echo e(__('Address')); ?>:</label>
                    <textarea name="address" rows="1" class="form-control"
                      placeholder="<?php echo e(__('Please Enter Address')); ?> "></textarea>
                  </div>                
                </div>
                 <div class="col-md-3">
                  <div class="form-group">
                    <label class="text-dark" for="city_id"><?php echo e(__('City')); ?>: </label>
                    <input type="text" class="form-control" placeholder="<?php echo e(__('Please Enter City')); ?>" onchange="get_state_country(this.value)" required>
                    <input type="hidden" name="city_id" class="city_id"> 
                    <span class="error text-danger"></span>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="text-dark" for="state_id"><?php echo e(__('State')); ?>: </label>
                    <input type="text" class="form-control state" placeholder="<?php echo e(__('Please Enter State')); ?>" readonly>     
                    <input type="hidden" name="state_id" class="state_id"> 
                  </div>
                </div>
                 <div class="col-md-3">
                  <div class="form-group">
                    <label class="text-dark" for="city_id"><?php echo e(__('Country')); ?>: </label> 
                    <input type="text" class="form-control country" placeholder="<?php echo e(__('Please Enter Country')); ?>" readonly>     
                    <input type="hidden" name="country_id" class="country_id">          
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label class="text-dark" for="pin_code"><?php echo e(__('Pincode')); ?>:</sup></label>
                    <input value="<?php echo e(old('pin_code')); ?>" placeholder="<?php echo e(__('Please Enter Pincode')); ?>"
                      type="text" name="pin_code" class="form-control">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="text-dark" for="exampleInputSlug"><?php echo e(__('Image')); ?>: </label>
                    <small class="text-muted"><i class="fa fa-question-circle"></i>
                      <?php echo e(__('Recommended size')); ?> (410 x 410PX)</small>
                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupFileAddon01"><?php echo e(__('Upload')); ?></span>
                      </div>
                      <div class="custom-file">
                        <input accept="image/*" type="file" name="user_img" class="custom-file-input" id="user_img_one" aria-describedby="inputGroupFileAddon01" onchange="readURL(this);">
                        <label class="custom-file-label" for="inputGroupFile01"><?php echo e(__('Choose file')); ?></label>
                      </div>
                    </div>
                    <div class="thumbnail-img-block mb-3">
                      <img src="<?php echo e(url('images/user_img/user.jpg')); ?>" id="user_img" class="img-fluid" alt="">
                    </div>   
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-info-rgba p-4 mb-4">
              <h4 class="pb-4"><?php echo e(__('Social Profile')); ?></h4>
              <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="text-dark" for="fb_url">
                        <?php echo e(__('Facebook URL')); ?>:
                      </label>
                      <input autofocus name="fb_url" type="text" class="form-control" placeholder="https://facebook.com/" />
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="text-dark" for="youtube_url">
                        <?php echo e(__('YouTube URL')); ?>:
                      </label>
                      <input autofocus name="youtube_url" type="text" class="form-control" placeholder="https://youtube.com/" />
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="text-dark" for="twitter_url">
                        <?php echo e(__('Twitter URL')); ?>:
                      </label>
                      <input autofocus name="twitter_url" type="text" class="form-control" placeholder="https://twitter.com/" />
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label class="text-dark" for="linkedin_url">
                        <?php echo e(__('LinkedIn URL')); ?>:
                      </label>
                      <input autofocus name="linkedin_url" type="text" class="form-control" placeholder="https://linkedin.com/" />
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-lg-12">
                <div class="form-group px-4">
                  <label for="exampleInputDetails"><?php echo e(__('Status')); ?></label><br>
                  <input id="status_toggle" type="checkbox" class="custom_toggle" name="status" checked />
                </div>
              </div>
            </div>
            <div class="form-group p-4">
              <button type="reset" class="btn btn-danger-rgba" title="<?php echo e(__('Reset')); ?>"><i class="fa fa-ban"></i> <?php echo e(__('Reset')); ?></button>
              <button type="submit" class="btn btn-primary-rgba" title=" <?php echo e(__('Create')); ?>"><i class="fa fa-check-circle"></i>
                <?php echo e(__('Create')); ?></button>
            </div>
            <div class="clear-both"></div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<script>
  (function ($) {
    "use strict";

    $('#married_status').change(function () {

      if ($(this).val() == 'Married') {
        $('#doaboxxx').show();
      } else {
        $('#doaboxxx').hide();
      }
    });

    $(function () {
      $("#dob,#doa").datepicker({
        changeYear: true,
        yearRange: "-100:+0",
        dateFormat: 'yy/mm/dd',
      });
    });
    $(function () {
      $('#country_id').change(function () {
        var up = $('#upload_id').empty();
        var cat_id = $(this).val();
        
        if (cat_id) {
          $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "GET",
            url: <?php echo json_encode(url('country/dropdown'), 15, 512) ?>,
            data: {
              catId: cat_id
            },
            success: function (data) {
              console.log(data);
              up.append('<option value="0">Please Choose</option>');
              $.each(data, function (id, title) {
                up.append($('<option>', {
                  value: id,
                  text: title
                }));
              });
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
              console.log(XMLHttpRequest);
            }
          });
        }
      });
    });

    $(function () {

      $('#upload_id').change(function () {
        var up = $('#grand').empty();
        var cat_id = $(this).val();
        if (cat_id) {
          $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "GET",
            url: <?php echo json_encode(url('country/gcity'), 15, 512) ?>,
            data: {
              catId: cat_id
            },
            success: function (data) {
              console.log(data);
              up.append('<option value="0">Please Choose</option>');
              $.each(data, function (id, title) {
                up.append($('<option>', {
                  value: id,
                  text: title
                }));
              });
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
              console.log(XMLHttpRequest);
            }
          });
        }
      });
    });
  })(jQuery);

  function get_state_country(params) {
    if(params){
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "GET",
        url: <?php echo json_encode(url('get/state/country'), 15, 512) ?>,
        data: {
          city: params
        },
        success: function (data) {
          if(data.status=='True'){
              $('.city_id').val(data.city_id);
              $('.state').val(data.state);
              $('.state_id').val(data.state_id);
              $('.country').val(data.country);
              $('.country_id').val(data.country_id);
              $('.error').hide();
          } else {
              $('.city_id').val('');
              $('.state').val('');
              $('.state_id').val('');
              $('.country').val('');
              $('.country_id').val('');
              $('.error').show();
              $('.error').text(data.msg);
          }
        },
        error: function (XMLHttpRequest, textStatus, errorThrown) {
          console.log(XMLHttpRequest);
        }
      });
    }
  }
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/user/adduser.blade.php ENDPATH**/ ?>