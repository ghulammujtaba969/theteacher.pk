<form id="settingsform" class="form" action="<?php echo e(route('setting.store')); ?>" method="POST" novalidate
    enctype="multipart/form-data">
    <?php echo csrf_field(); ?>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="text-dark" for="status"><?php echo e(__('Text Logo')); ?> :</label><br>
                <input type="checkbox" class="custom_toggle" id="customSwitch1" name="<?php echo e(__('project_logo')); ?>"
                    <?php echo e($gsetting->logo_type == 'L' ? 'checked' : ''); ?> />
                <input type="hidden" name="free" value="0" for="customSwitch1" id="customSwitch1">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="text-dark"><?php echo e(__('Project Title')); ?>:</label>
                <input name="project_title" autofocus="" type="text"
                    class="<?php echo e($errors->has('project_title') ? ' is-invalid' : ''); ?> form-control"
                    placeholder="<?php echo e(__('Enter project title')); ?>" value="<?php echo e($setting->project_title); ?>" required="">
                <div class="invalid-feedback">
                    <?php echo e(__('Please enter Project Title !')); ?>.
                </div>
            </div>
        </div>
        <!-- ============ Project Title end =========================-->
        <!-- ============ APP URL start =============================-->
        <div class="col-md-3">
            <div class="form-group">
                <label class="text-dark"><?php echo e(__('APP URL')); ?>:</label>
                <input name="APP_URL" autofocus="" type="text"
                    class="<?php echo e($errors->has('APP_URL') ? ' is-invalid' : ''); ?> form-control"
                    placeholder="http://localhost/" value="<?php echo e($env_files['APP_URL']); ?>" required="">
                <div class="invalid-feedback">
                    <?php echo e(__('Please Enter APP URL !')); ?>.
                </div>
            </div>
        </div>
        <!-- ============ APP URL end =============================-->
        <!-- ============ email start =============================-->
        <div class="col-md-3">
            <div class="form-group">
                <label class="text-dark" for="wel_email"><?php echo e(__('Email')); ?> : <span
                        class="text-danger">*</span></label>
                <input value="<?php echo e($setting->wel_email); ?>" name="wel_email" placeholder="<?php echo e(__('Enter your email')); ?>" type="text"
                    class="<?php echo e($errors->has('wel_email') ? ' is-invalid' : ''); ?> form-control" required>
                <div class="invalid-feedback">
                    <?php echo e(__('Please Enter Email !')); ?>.
                </div>
            </div>
        </div>
        <!-- ============ email end =============================-->
        <!-- ============ Contact start =============================-->
        <div class="col-md-3">
            <div class="form-group">
                <label class="text-dark" for="phone"><?php echo e(__('Contact')); ?> : <span
                        class="text-danger">*</span></label>
                <input value="<?php echo e($setting->default_phone); ?>" name="default_phone" placeholder="<?php echo e(__('Enter contact No.')); ?>"
                    type="text" class="<?php echo e($errors->has('default_phone') ? ' is-invalid' : ''); ?> form-control" required>
                <div class="invalid-feedback">
                    <?php echo e(__('Please Enter Contact !')); ?>.
                </div>
            </div>
        </div>
        <!-- ============ Contact end =============================-->        
        <!-- ============ Copyright Text start ==================-->
        <div class="col-md-3">
            <div class="form-group">
                <label class="text-dark" for="cpy_txt"><?php echo e(__('Copyright Text')); ?> : <span
                        class="text-danger">*</span></label>
                <input value="<?php echo e($setting->cpy_txt); ?>" name="cpy_txt" placeholder="Enter Copyright Text" type="text"
                    required class="<?php echo e($errors->has('cpy_txt') ? ' is-invalid' : ''); ?> form-control">
                <div class="invalid-feedback">
                    <?php echo e(__('Please Enter Copyright Text !')); ?>.
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="text-dark" for="feature_amount"><?php echo e(__('Amount to feature a course')); ?> </label>
                <input min="1" class="form-control" name="feature_amount" type="number"
                    value="<?php echo e($setting->feature_amount); ?>" id="duration"
                    placeholder="<?php echo e(__('Enter amount to feature course')); ?> ex: 100"
                    class="<?php echo e($errors->has('feature_amount') ? ' is-invalid' : ''); ?> form-control">
                <small><?php echo e(__('(Instructor can feature its course, by paying this amount)')); ?></small>
            </div>
        </div>
        <!-- ============ Address start =========-->
        <div class="col-md-3">
            <div class="form-group">
                <label class="text-dark" for="exampleInputDetails"><?php echo e(__('Address')); ?> : <span
                        class="text-danger">*</span></label>
                <textarea name="default_address" rows="2" class="form-control" placeholder="<?php echo e(__('Enter your address')); ?>"
                    required><?php echo e($setting->default_address); ?></textarea>
            </div>
        </div>
        <!-- ============ Address end =========-->
        <!-- ============ MapCoordinates start =========-->
        <div class="bg-info-rgba col-md-12 ml-3 mr-3 mb-3">
            <h4 class="mt-3 mb-3"><i class="feather icon-map-pin" aria-hidden="true"></i><?php echo e(__(' Map Coordinates Settings')); ?></h4>
            <div class="col-md-12">
                <div class="form-group">
                    <label class="text-dark" for="map_lat"><?php echo e(__('Map Enable')); ?>:</label><br>
                    <input type="checkbox" class="custom_toggle" id="customSwitch2" name="map_enable"
                        <?php echo e($gsetting->map_enable == 'map' ? 'checked' : ''); ?> />
                    <input type="hidden" name="free" value="0" for="customSwitch2" id="customSwitch2">
                    <div>
                        <small><?php echo e(__('(Enable Map on contact page)')); ?></small>
                    </div>
                </div>
            </div>
            <!-- ============ MapCoordinates end =========-->
            <!-- contact page start -->
            <div class="col-md-12">
                <div class="row" style="<?php echo e($setting['map_enable'] == 'image' ? '' : 'display:none'); ?>" id="sec_one">
                    <div class="col-md-12">
                        <label class="text-dark"><?php echo e(__('ContactPageImage')); ?> :</label>
                        <small><?php echo e(__('(Note - Recommended Size : 300x90PX)')); ?></small>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroupFileAddon01"><?php echo e(__('Upload')); ?></span>
                            </div>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="inputGroupFile01" name="contact_image"
                                    aria-describedby="inputGroupFileAddon01">
                                <label class="custom-file-label" for="inputGroupFile01"><?php echo e(__('Choose file')); ?></label>
                            </div>
                        </div>
                        <?php if($image = @file_get_contents('../public/images/contact/'.$setting->contact_image)): ?>
                        <img src="<?php echo e(url('/images/contact/'.$setting->contact_image)); ?>" class="image_size" alt="{ __('Contact') }}" />
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- contact page end -->
            <div class="col-md-12">
                <div class="row" style="<?php echo e($setting['map_enable'] == 'map' ? '' : 'display:none'); ?>" id="sec1_one">
                    <div class="col-md-4">
                        <label class="text-dark" for="map_url"><?php echo e(__('Iframe URL')); ?>:</label>
                        <input value="<?php echo e($setting->map_url); ?>" name="map_url" placeholder="<?php echo e(__('Enter the iframe URL')); ?>" type="text"
                            class="<?php echo e($errors->has('map_url') ? ' is-invalid' : ''); ?> form-control">
                    </div>
                    
                    <div class="col-md-4">
                        <label class="text-dark" for="map_api"><?php echo e(__('Map Api Key')); ?>:</label>
                        <input value="<?php echo e($setting->map_api); ?>" name="map_api" placeholder="<?php echo e(__('Enter Map Api')); ?>" type="text"
                            class="<?php echo e($errors->has('map_api') ? ' is-invalid' : ''); ?> form-control">
                    </div>
                </div>
            </div>
            <br>
        </div>
        <div class="bg-info-rgba col-md-12 ml-3 mr-3 mb-3">

            <h4 class="mt-3 mb-3"><i class="feather icon-server"
                    aria-hidden="true"></i> <?php echo e(__('Promo Bar')); ?></h4>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="text-dark" for="promo_enable"><?php echo e(__('Promo Enable')); ?>

                            :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch3" name="promo_enable"
                            <?php echo e($gsetting->promo_enable == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch3" id="customSwitch3">
                        <div>
                            <small><?php echo e(__('(Enable Promo Bar on site)')); ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ============================ -->
            <div class="row" style="<?php echo e($setting['promo_enable'] == 1 ? '' : 'display:none'); ?>" id="sec2_one">
                <div class="col-md-6">
                    <label for="promo_text"><?php echo e(__('Promo Text')); ?>:</label>
                    <input value="<?php echo e($setting->promo_text); ?>" name="promo_text" placeholder="<?php echo e(__('Enter Promo Text')); ?>"
                        type="text" class="<?php echo e($errors->has('promo_text') ? ' is-invalid' : ''); ?> form-control">
                </div>
                <div class="col-md-6">
                    <label for="promo_link"><?php echo e(__('Promo Link')); ?>:</label>
                    <input value="<?php echo e($setting->promo_link); ?>" name="promo_link" placeholder="<?php echo e(__('Enter Promo Text Link')); ?>"
                        type="text" class="<?php echo e($errors->has('promo_link') ? ' is-invalid' : ''); ?> form-control">
                </div>
            </div>
            <br>
        </div>

        <div class="bg-info-rgba col-md-12 ml-3 mr-3 mb-3">
            <h4 class="mt-3 mb-3"><i class="feather icon-smartphone" aria-hidden="true"></i> <?php echo e(__('APP Settings')); ?></h4>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="text-dark" for="status"><?php echo e(__('APP Store')); ?></label>
                        <br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch4" name="app_download"
                            <?php echo e($gsetting->app_download == '1' ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch4" id="customSwitch4">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Link:')); ?></label>
                        <input name="app_link" autofocus="" type="text"
                            class="<?php echo e($errors->has('app_link') ? ' is-invalid' : ''); ?> form-control"
                            placeholder="<?php echo e(__('Please Enter APP Store Link')); ?>" value="<?php echo e($setting->app_link); ?>">
                        <div class="invalid-feedback">
                            <?php echo e(__('Please Enter APP Store Link !')); ?>.
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Play Store')); ?></label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch5" name="play_download"
                            <?php echo e($setting->play_download == '1' ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch5" id="customSwitch5">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Link:')); ?></label>
                        <input name="play_link" autofocus="" type="text" class="form-control"
                            placeholder="<?php echo e(__('Please Enter Play Store Link')); ?>" value="<?php echo e($setting->play_link); ?>">
                        <div class="invalid-feedback">
                            <?php echo e(__('Please Enter Play Store Link !')); ?>.
                        </div>
                    </div>
                </div>
            </div>
            <!-- ======= Donation link start ========== -->
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Donation Link')); ?>: </label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch6" name="donation_enable"
                            <?php echo e($setting->donation_enable == '1' ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch6" id="customSwitch6">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Link')); ?> :</label>
                        <input name="donation_link" autofocus="" type="text" class="form-control"
                            placeholder="<?php echo e(__('Please Enter PayPal.me Link')); ?>" value="<?php echo e($setting->donation_link); ?>">
                        <small><?php echo e(__('Get Donation link by register on ')); ?><a target="__blank"
                                href="https://www.paypal.com/in/webapps/mpp/paypal-me"> <?php echo e(__('PayPal.me')); ?></a></small>
                        <div class="invalid-feedback">
                            <?php echo e(__('Please Enter PayPal.me Link !')); ?>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-info-rgba col-md-12 ml-3 mr-3 mb-3">
            <h4 class="mt-3 mb-3"><i class="feather icon-loader" aria-hidden="true"></i> <?php echo e(__('Miscellaneous Settings')); ?>

            </h4>
            <!-- ======= Donation link end ========== -->
            <!-- ======= section 1 start ========== -->
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Right Click')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch7" name="rightclick"
                            <?php echo e($gsetting->rightclick == '0' ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch7" id="customSwitch7">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Inspect Element')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch8" name="inspect"
                            <?php echo e($setting->inspect == '0' ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch8" id="customSwitch8">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Preloader Enable')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch9" name="preloader_enable"
                            <?php echo e($gsetting->preloader_enable == '1' ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch9" id="customSwitch9">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('APP Debug')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch10" name="APP_DEBUG"
                            <?php echo e(env('APP_DEBUG') == true ? "checked" : ""); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch10" id="customSwitch10">
                    </div>
                </div>
            </div>
            <!-- ======= section 1 end ========== -->
            <!-- ======= section 2 start ========== -->
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Welcome Email')); ?> : </label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch11" name="w_email_enable"
                            <?php echo e($gsetting->w_email_enable == '1' ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch11" id="customSwitch11">
                        <div>
                            <small><?php echo e(__('(If you enable it, a welcome email will be sent to user\'s register email id,<br> make sure you updated your mail setting in Site Setting >> Mail Settings before enable it.)')); ?></small>
                            <small class="text-danger"><?php echo e($errors->first('color')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Verify Email')); ?> : </label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch12" name="verify_enable"
                            <?php echo e($gsetting->verify_enable == '1' ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch12" id="customSwitch12">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Become An Instructor')); ?>: </label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch13" name="instructor_enable"
                            <?php echo e($gsetting->instructor_enable == '1' ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch13" id="customSwitch13">
                        <div>
                            <small><?php echo e(__('(Enable Become an instructor option for users)')); ?></small>
                            <small class="text-danger"><?php echo e($errors->first('color')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Categories For Instructor')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch14" name="cat_enable"
                            <?php echo e($gsetting->cat_enable == '1' ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch14" id="customSwitch14">
                        <div>
                            <small><?php echo e(__('(If you enable it, Instructor Able to Add Categories)')); ?></small>
                            <small class="text-danger"><?php echo e($errors->first('color')); ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ======= section 2 end ========== -->
            <!-- ======= section 3 start ========== -->
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Mobile No. on SignUp')); ?> : </label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch15" name="mobile_enable"
                            <?php echo e($gsetting->mobile_enable == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch15" id="customSwitch15">
                        <div>
                            <small><?php echo e(__('(Enable ask mobile no. on SignUp page)')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Device Control')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch16" name="device_enable"
                            <?php echo e($gsetting->device_control == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch16" id="customSwitch16">
                        <div>
                            <small><?php echo e(__('(Enable Device Control)')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Cookie Notice')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch17" name="cookie_enable"
                            <?php echo e($gsetting->cookie_enable == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch17" id="customSwitch17">
                        <div>
                            <small><?php echo e(__('(Enable Cookie Notice on Site)')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('IP Block')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch18" name="ipblock_enable"
                            <?php echo e($gsetting->ipblock_enable == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch18" id="customSwitch18">
                        <div>
                            <small><?php echo e(__('(Enable IP block on portal)')); ?></small>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ======= section 3 end ========== -->
            <!-- ======= section 4 start ========== -->
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Activity Log')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch19" name="activity_enable"
                            <?php echo e($gsetting->activity_enable == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch19" id="customSwitch19">
                        <div>
                            <small><?php echo e(__('(Enable Users Activity Logs for Login/Register)')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Assignments')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch20" name="assignment_enable"
                            <?php echo e($gsetting->assignment_enable == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch20" id="customSwitch20">
                        <div>
                            <small><?php echo e(__('(Enable Assignments on Course)')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Appointments')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch21" name="appointment_enable"
                            <?php echo e($gsetting->appointment_enable == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch21" id="customSwitch21">
                        <div>
                            <small><?php echo e(__('(Enable Appointments on Course)')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Certificates ')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch22" name="certificate_enable"
                            <?php echo e($gsetting->certificate_enable == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch22" id="customSwitch22">
                        <div>
                            <small><?php echo e(__('(Enable Certificates on Course)')); ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ======= section 4 end ========== -->
            <!-- ======= section 5 start ========== -->
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Hide Identity')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch23" name="hide_identity"
                            <?php echo e($gsetting->hide_identity == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch23" id="customSwitch23">
                        <div>
                            <small><?php echo e(__('(Hide Users Identity from Instructors)')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Course Hover')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch24" name="course_hover"
                            <?php echo e($gsetting->course_hover == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch24" id="customSwitch24">
                        <div>
                            <small><?php echo e(__('(Enable/Disable Hover from home page sliders)')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 d-none">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Currency Swipe')); ?> : </label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch25" name="currency_swipe"
                            <?php echo e($gsetting->currency_swipe == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch25" id="customSwitch25">
                        <div>
                            <small><?php echo e(__('(Swipe currency before/after icon)')); ?></small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Attendance')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch26" name="attandance_enable"
                            <?php echo e($gsetting->attandance_enable == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch26" id="customSwitch26">
                        <div>
                            <small><?php echo e(__('(Enable Attendance on Courses)')); ?></small>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Guest Checkout')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch26" name="guest_enable"
                            <?php echo e($gsetting->guest_enable == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch26" id="customSwitch26">
                        <div>
                            <small><?php echo e(__('(Enable Guest checkout)')); ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ======= section 5 end ========== -->
            <!-- ======= section 6 start ========== -->
        </div>
        <div class="bg-info-rgba col-md-12 ml-3 mr-3 mb-3">
            <h4 class="mt-3 mb-3"><i class="feather icon-video" aria-hidden="true"></i> <?php echo e(__('Meetings Settings')); ?>

            </h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Enable Zoom On Portal')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch27" name="zoom_enable"
                            <?php echo e($gsetting->zoom_enable == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch27" id="customSwitch27">
                        <div>
                            <small><?php echo e(__('( Enable Live zoom meetings on portal )')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Enable BigBlueButton Meetings')); ?> : </label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch28" name="bbl_enable"
                            <?php echo e($gsetting->bbl_enable == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch28" id="customSwitch28">
                        <div>
                            <small><?php echo e(__('(Enable BigBlueButton meetings on portal)')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Google Meet')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch29" name="googlemeet_enable"
                            <?php echo e($gsetting->googlemeet_enable == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch29" id="customSwitch29">
                        <div>
                            <small><?php echo e(__('(Enable Google Meet on portal)')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Jitsi Meeting')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch30" name="jitsimeet_enable"
                            <?php echo e($gsetting->jitsimeet_enable == 1 ? 'checked' : ''); ?> />
                        <input type="hidden" name="free" value="0" for="customSwitch30" id="customSwitch30">
                        <div>
                            <small><?php echo e(__('(Enable Jitsi Meeting on Portal)')); ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-info-rgba col-md-12 ml-3 mr-3 mb-3">
            <h4 class="mt-3 mb-3"><i class="feather icon-toggle-right" aria-hidden="true"></i> <?php echo e(__('Slider On/Off')); ?>

            </h4>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Category Slider')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="customSwitch50" name="category_enable"
                            <?php echo e($gsetting->category_enable == 1 ? 'checked' : ''); ?> />
                        <div>
                            <small><?php echo e(__('( Enable for Category Slider)')); ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                      <label>
                        <?php echo e(__('Enable Price with comma notation')); ?> :
                      </label>
                      <br>
                      <label class="switch">
                        <input type="checkbox" class="custom_toggle" id="customSwitch51"<?php echo e(env('PRICE_DISPLAY_FORMAT') == 'comma' ? 'checked' : ''); ?>  name="PRICE_DISPLAY_FORMAT">
                      </label>
                      <br>
                      <small class="text-muted"><i class="fa fa-question-circle"></i> <?php echo e(__('By toggling it price will display on front end with comma')); ?> eg : 1000.12 <?php echo e(__('will show')); ?> <b>1 000,50</b>.</small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Watch Video')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="myCheck" name="watch_enable"
                            <?php echo e($gsetting->watch_enable == 1 ? 'checked' : ''); ?> onclick="myFunction()"/>
                        <div>
                            <small><?php echo e(__('( Enable for watching a video for times)')); ?></small>
                        </div>
                    </div>
                    <div style="<?php echo e($gsetting->watch_enable == 1 ? '' : 'display: none'); ?>" id="update-password">
                        <div class="form-group">
                          <label><?php echo e(__('Times a Video')); ?></label>
                          <input type="number" name="" value="" class="form-control"
                            placeholder="<?php echo e(__('Please Enter times for watching a video')); ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="text-dark"><?php echo e(__('Enable for OpenAI Chatgpt Integration')); ?> :</label><br>
                        <input type="checkbox" class="custom_toggle" id="myCheck1" name="api_enable"
                            <?php echo e($gsetting->api_enable == 1 ? 'checked' : ''); ?> onclick="myFunction()"/>
                        <div>
                            <small><?php echo e(__('( Enable for Api Integration then please Update your Key.)')); ?></small>
                        </div>
                    </div>
                    <div style="<?php echo e($gsetting->api_enable == 1 ? '' : 'display: none'); ?>" id="update-password1">
                        <div class="form-group openai-form-group">
                          <label><?php echo e(__('OpenAi Api Key')); ?></label>
                          <input type="password"  name="api_key" value="<?php echo e($gsetting->api_key); ?>" class="form-control"
                            placeholder="<?php echo e(__('Please Enter Api Key')); ?>">
                            <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-9">
                    <label class="text-dark"><?php echo e(__('Logo')); ?> :</label>
                    <small><?php echo e(__('(Note - Recommended Size : 300x90PX)')); ?></small>
                    <div class="input-group">
                        <input required readonly id="image" for="logo" name="logo" type="text" class="form-control">
                        <div class="input-group-append">
                            <span data-input="image"
                                class="bg-primary text-light midia-toggle input-group-text"><?php echo e(__('Browse')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 p-3 bg-primary-rgba">
                    <img src="<?php echo e(url('/images/logo/'.$setting->logo)); ?>" class="img-fluid" alt="<?php echo e(__('Logo')); ?>"/>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row mb-4">
                <div class="col-md-9">
                    <label class="text-dark"><?php echo e(__('Footer Logo')); ?> :</label>
                    <small><?php echo e(__('(Note - Recommended Size : 300x90PX)')); ?></small>
                    <div class="input-group">
                        <input required readonly id="footer_logo" for="footer_logo" name="footer_logo" type="text"
                            class="form-control">
                        <div class="input-group-append">
                            <span data-input="footer_logo"
                                class="bg-primary text-light midia-toggle input-group-text"><?php echo e(__('Browse')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="bg-primary-rgba p-3 col-md-3">
                    <?php if($image = @file_get_contents(public_path().'/images/favicon/'.$setting->favicon)): ?>
                    <img src="<?php echo e(url('/images/favicon/'.$setting->favicon)); ?>"
                        class="logo_img img-fluid" alt="<?php echo e(__('Footer Logo')); ?>"/>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <!-- ============ Preloader logo start =========================== -->
        <div class="form-group col-md-6">
                    
            <div class="row">
                <div class="col-md-9">
                    <label class="text-dark"><?php echo e(__('Preloader logo')); ?> :</label>
                    <small><?php echo e(__('(Note - Recommended Size : 300x90PX)')); ?></small>
                    <div class="input-group">
                        <input required readonly id="preloader_logo" for="preloader_logo" name="preloader_logo"
                            type="text" class="form-control">
                        <div class="input-group-append">
                            <span data-input="preloader_logo"
                                class="bg-primary text-light midia-toggle input-group-text"><?php echo e(__('Browse')); ?></span>
                        </div>
                    </div>
                </div>             
            </div>            
        </div>
        <div class="form-group col-md-6">
            <div class="row">
                <div class="col-md-9">
                    <label class="text-dark"><?php echo e(__('Favicon')); ?> :</label>
                    <small><?php echo e(__('(Note - Recommended Size : 35x35PX)')); ?></small>
                    <div class="input-group">
                        <input required readonly id="favicon" for="favicon" name="favicon" type="text"
                            class="form-control">
                        <div class="input-group-append">
                            <span data-input="favicon"
                                class="bg-primary text-light midia-toggle2 input-group-text"><?php echo e(__('Browse')); ?></span>
                        </div>
                    </div>
                </div>
                <div class="bg-primary-rgba p-3 col-md-3">
                    <?php if($image = @file_get_contents('../public/images/favicon/'.$setting->favicon)): ?>
                    <img src="<?php echo e(url('/images/favicon/'.$setting->favicon)); ?>" class="logo_img img-fluid" alt="<?php echo e(__('Favicon')); ?>"/>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-12 form-group ml-3">            
            <div class="form-group">
                <button type="reset" class="btn btn-danger-rgba mr-1" title="<?php echo e(__('Reset')); ?>"><i class="fa fa-ban"></i>
                    <?php echo e(__("Reset")); ?></button>
                <button type="submit" class="btn btn-primary-rgba" title="<?php echo e(__('Save')); ?>"><i class="fa fa-check-circle"></i>
                    <?php echo e(__("Save")); ?></button>
            </div>
        </div>
    </div>
</form>
<style>
    .image_size {
        height: 80px;
        width: 200px;
    }
</style>
<?php $__env->startSection('scripts'); ?>
<script>
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
   
</script>
<script>
    "use strict";
    $(function(){
      $('#myCheck1').change(function(){
        if($('#myCheck1').is(':checked')){
          $('#update-password1').show('fast');
        }else{
          $('#update-password1').hide('fast');
        }
      });
      
    });
   
</script>
<?php $__env->stopSection(); ?> <?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/setting/genral.blade.php ENDPATH**/ ?>