
<?php $__env->startSection('title','Edit User'); ?>
<?php $__env->startSection('maincontent'); ?>
<?php
$data['heading'] = 'Edit User';
$data['title'] = 'Setting';
$data['title1'] = 'Mobile Setting';
?>
<?php echo $__env->make('admin.layouts.topbar',$data, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="contentbar">
  <div class="row">
    <?php if($errors->any()): ?>  
  <div class="alert alert-danger" role="alert">
  <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>     
  <p><?php echo e($error); ?><button type="button" class="close" data-dismiss="alert" aria-label="Close" title="<?php echo e(__('Close')); ?>">
  <span aria-hidden="true" style="color:red;">&times;</span></button></p>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>  
  </div>
  <?php endif; ?>
    <div class="col-lg-12">
      <div class="card dashboard-card m-b-30">
        <div class="card-header">
          <h5 class="box-title"><?php echo e(__('Edit User')); ?></h5>
          <div class="widgetbar">
            <a href="<?php echo e(route('user.index')); ?>" class="float-right btn btn-primary-rgba mr-2" title="<?php echo e(__('Back')); ?>"><i
                class="feather icon-arrow-left mr-2"></i><?php echo e(__('Back')); ?></a>
          </div>
        </div>
        <div class="card-body ml-2">
          <form action="<?php echo e(route('user.update',$user->id)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo e(csrf_field()); ?>

            <?php echo e(method_field('PUT')); ?>

            <div class="bg-info-rgba p-4 mb-4">
              <h4 class="pb-4"><?php echo e(__('Personal Details')); ?></h4>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="fname">
                      <?php echo e(__('First Name')); ?>:
                      <sup class="text-danger">*</sup>
                    </label>
                    <input value="<?php echo e($user->fname); ?>" autofocus required name="fname" type="text" class="form-control"
                      placeholder="<?php echo e(__('Please Enter First Name')); ?>" />
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="lname">
                      <?php echo e(__('Last Name')); ?>:
                      <sup class="text-danger">*</sup>
                    </label>
                    <input value="<?php echo e($user->lname); ?>" required name="lname" type="text" class="form-control"
                      placeholder="<?php echo e(__('Enter Last Name')); ?>" />
                  </div>
                </div>
                <div class="col-md-3">            
                  <div class="form-group">
                    <label for="mobile"><?php echo e(__('Email')); ?>:<sup class="text-danger">*</sup> </label>
                    <input value="<?php echo e($user->email); ?>" required type="email" name="email"
                      placeholder="<?php echo e(__('Enter Email')); ?>"
                      class="form-control">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="mobile"> <?php echo e(__('Mobile')); ?>:</label>
                    <input value="<?php echo e($user->mobile); ?>" type="text" name="mobile"
                      placeholder="<?php echo e(__('Enter Mobile')); ?>"
                      class="form-control">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="role"><?php echo e(__('Select Role')); ?>:</label>
                    <?php if(Auth::User()->role=="admin"): ?>
                    <select class="form-control select2" name="role">
                      <option value=""><?php echo e(__("Please select role")); ?></option>
                      <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <option <?php echo e($user->getRoleNames()->contains($role->name) ? 'selected' : ""); ?>

                        value="<?php echo e($role->name); ?>"><?php echo e($role->name); ?></option>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php endif; ?>
                    <?php if(Auth::User()->role=="instructor"): ?>
                    <select class="form-control select2" name="role">
                      <option <?php echo e($user->role == 'user' ? 'selected' : ''); ?> value="user"><?php echo e(__('User')); ?>

                      </option>
                      <option <?php echo e($user->role == 'instructor' ? 'selected' : ''); ?> value="instructor">
                        <?php echo e(__('Instructor')); ?></option>
                    </select>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                    <label for="detail"><?php echo e(__('Detail')); ?>:<sup class="text-danger">*</sup></label>
                    <textarea id="detail" name="detail" class="form-control" rows="5"
                      placeholder="<?php echo e(__('Enter Detail')); ?>"
                      value=""><?php echo e($user->detail); ?></textarea>
                  </div>
                </div>
              </div>
            </div>
            <div class="bg-info-rgba p-4 mb-4">
              <h4 class="pb-4"><?php echo e(__('Address')); ?></h4>
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="address"><?php echo e(__('Address')); ?>: </label>
                    <textarea name="address" class="form-control" rows="1"
                      placeholder="<?php echo e(__('Enter Adderss')); ?>" value=""><?php echo e($user->address); ?></textarea>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                      <label class="text-dark" for="city_id"><?php echo e(__('City')); ?>: </label>
                      <input type="text" class="form-control" placeholder="<?php echo e(__('Please')); ?> <?php echo e(__('Enter City')); ?>" onchange="get_state_country(this.value)" value="<?php echo e($cities?$cities->name:''); ?>">
                      <input type="hidden" name="city_id" value="<?php echo e($user->id); ?>" class="city_id"> 
                      <span class="error text-danger"></span>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="text-dark" for="state_id"><?php echo e(__('State')); ?>: </label>
                    <input type="text" class="form-control state" placeholder="<?php echo e(__('Please Enter State')); ?>" value="<?php echo e($states?$states->name:''); ?>" readonly>     
                    <input type="hidden" name="state_id" value="<?php echo e($user->state_id); ?>" class="state_id"> 
                  </div>
                </div>
                <div class="col-md-3">
                 <div class="form-group">
                    <label class="text-dark" for="city_id"><?php echo e(__('Country')); ?>: </label> 
                    <input type="text" class="form-control country" placeholder="<?php echo e(__('Please Enter Country')); ?>" value="<?php echo e($countries?$countries->name:''); ?>" readonly>     
                    <input type="hidden" name="country_id" value="<?php echo e($user->country_id); ?>" class="country_id"> 
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <label for="pin_code"><?php echo e(__('Pincode')); ?>:</label>
                    <input value="<?php echo e($user->pin_code); ?>"
                      placeholder="<?php echo e(__('Enter Pincode')); ?>" type="text"
                      name="pin_code" class="form-control">
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="text-dark" for="exampleInputSlug"><?php echo e(__('Image')); ?>: </label>
                    <small class="text-muted"><i class="fa fa-question-circle"></i>
                      <?php echo e(__('Recommended size')); ?> (410 x 410px)</small>
                    <div class="input-group mb-3">
                      <div class="input-group-prepend">
                        <span class="input-group-text" id="inputGroupFileAddon01"><?php echo e(__('Upload')); ?></span>
                      </div>
                      <div class="custom-file">
                        <input type="file" name="user_img" class="custom-file-input" id="user_img_one" aria-describedby="inputGroupFileAddon01" onchange="readURL(this);">
                        <label class="custom-file-label" for="inputGroupFile01"><?php echo e(__('Choose file')); ?></label>
                      </div>
                    </div>
                    <div class="thumbnail-img-block mb-3">
                      <?php if($user->user_img != null || $user->user_img !=''): ?>
                      <div class="edit-user-img">
                        <img src="<?php echo e(url('/images/user_img/'.$user->user_img)); ?>"  alt="User Image"  id="user_img" class="img-responsive image_size">
                      </div>
                      <?php else: ?>
                      <div class="edit-user-img">
                        <img src="<?php echo e(asset('images/default/user.jpg')); ?>"  alt="User Image"  id="user_img" class="img-responsive img-circle">
                      </div>
                      <?php endif; ?>                 
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
                    <label for="fb_url">
                      <?php echo e(__('Facebook URL')); ?>:
                    </label>
                    <input autofocus name="fb_url" value="<?php echo e($user->fb_url); ?>" type="text" class="form-control"
                      placeholder="Facebook.com/" />
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="youtube_url">
                      <?php echo e(__('YouTube URL')); ?>:
                    </label>
                    <input autofocus name="youtube_url" value="<?php echo e($user->youtube_url); ?>" type="text" class="form-control"
                      placeholder="youtube.com/" />
                    </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label for="twitter_url">
                      <?php echo e(__('Twitter URL')); ?>:
                    </label>
                    <input autofocus name="twitter_url" value="<?php echo e($user->twitter_url); ?>" type="text" class="form-control"
                      placeholder="Twitter.com/" />
                  </div>
                </div>
                <div class="col-md-3">
                   <div class="form-group">
                    <label for="linkedin_url">
                      <?php echo e(__('LinkedIn URL')); ?>:
                    </label>
                    <input autofocus name="linkedin_url" value="<?php echo e($user->linkedin_url); ?>" type="text"
                      class="form-control" placeholder="Linkedin.com/" />
                  </div>
              </div>              
              <div class="col-md-4">
                  <div class="form-group">
                    <label for="exampleInputDetails"><?php echo e(__('Verified')); ?>:<sup class="redstar text-danger">*</sup></label><br>
                    <input id="verified" type="checkbox" class="custom_toggle" name="verified" <?php echo e($user->email_verified_at != NULL ? 'checked' : ''); ?> />
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="form-group">
                    <label for="exampleInputTit1e"><?php echo e(__('Status')); ?>:<sup
                        class="text-danger">*</sup></label><br>
                    <input type="checkbox" class="custom_toggle" name="status"
                      <?php echo e($user->status == '1' ? 'checked' : ''); ?> />

                  </div>
              </div>
              <div class="col-md-4">                                
                <div class="form-group">                  

                  <div class="row">
                    <div class="col-md-12">
                      <div class="update-password">
                        <label for="box1"> <?php echo e(__('Update Password')); ?>:</label>
                        <br>
                        <input type="checkbox" id="myCheck" name="update_pass" class="custom_toggle" onclick="myFunction()">
                      </div>
                    </div>
                  </div>
                  <div style="display: none" id="update-password">
                  <div class="form-group">
                    <label><?php echo e(__('Password')); ?> <sup class="text-danger">*</sup></label>
                    <input type="password" name="password" class="form-control"
                      placeholder="<?php echo e(__('Enter')); ?> <?php echo e(__('Password')); ?>">
                  </div>            
              
                <div class="form-group" >
                  <label><?php echo e(__('Confirm Password')); ?> <sup class="text-danger">*</sup></label>
                  <input type="password" name="confirmpassword" class="form-control"
                    placeholder="<?php echo e(__('Confirm Password')); ?>">
                </div>
              </div>               
              </div>               
              </div>
            </div>
            </div>           
            <div class="form-group">
              <button type="reset" class="btn btn-danger-rgba" title="<?php echo e(__('Reset')); ?>"><i class="fa fa-ban"></i>
                <?php echo e(__('Reset')); ?></button>
              <button type="submit" class="btn btn-primary-rgba" title="<?php echo e(__('Update')); ?>"><i class="fa fa-check-circle"></i>
                <?php echo e(__('Update')); ?></button>
            </div>
            <div class="clear-both"></div>           
        </div>
      </div>
    </div>
  </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script>
  (function ($) {
    "use strict";

    $(function () {
      $("#dob,#doa").datepicker({
        changeYear: true,
        yearRange: "-100:+0",
        dateFormat: 'yy/mm/dd',
      });
    });
    $('#married_status').change(function () {

      if ($(this).val() == 'Married') {
        $('#doaboxxx').show();
      } else {
        $('#doaboxxx').hide();
      }
    });

    $(function () {
      var urlLike = '<?php echo e(url('
      country / dropdown ')); ?>';
      $('#country_id').change(function () {
        var up = $('#upload_id').empty();
        var cat_id = $(this).val();
        if (cat_id) {
          $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "GET",
            url: urlLike,
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
      var urlLike = '<?php echo e(url('
      country / gcity ')); ?>';
      $('#upload_id').change(function () {
        var up = $('#grand').empty();
        var cat_id = $(this).val();
        if (cat_id) {
          $.ajax({
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: "GET",
            url: urlLike,
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

  

</script>


<script>
  (function($) {
    "use strict";
    $(function(){
        $('#myCheck').change(function(){
          if($('#myCheck').is(':checked')){
            $('#update-password').show('fast');
          }else{
            $('#update-password').hide('fast');
          }
        });
        
    });
  })(jQuery);
  </script>

  <script>
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
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/user/edit.blade.php ENDPATH**/ ?>