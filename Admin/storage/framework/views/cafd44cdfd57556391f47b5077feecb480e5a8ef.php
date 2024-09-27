
<?php $__env->startSection('title',__('Admin Dashboard')); ?>
<?php $__env->startSection('maincontent'); ?>
<?php echo $__env->make('admin.layouts.topbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div class="contentbar bardashboard-card">
    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('dashboard.manage')): ?>
        <!-- Start row -->
        <div class="row">
            <!-- Start col -->
         

            <div class="col-lg-12">
                <div>
                    <!-- Start row -->
                    <div class="row">
                        <!-- Start col -->
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4><?php echo e($userss); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('Users')); ?></p>
                                        </div> 
                                        <div class="col-6 text-right">
                                            <a href="<?php echo e(route('user.index')); ?>" title="<?php echo e(__('Users')); ?>">
                                                <i class="text-info feather icon-users icondashboard"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4><?php echo e($instructor); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('Instructors')); ?></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <a href="<?php echo e(url('allinstructor')); ?>" title="<?php echo e(__('Instructors')); ?>">
                                                <i class="text-warning feather icon-user icondashboard"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4><?php echo e($courses); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('Courses')); ?></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <a href="<?php echo e(route('course.index')); ?>" title="<?php echo e(__('Courses')); ?>">
                                                <i class="text-success feather icon-book-open icondashboard"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4><?php echo e($categories); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('Categories')); ?></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <a href="<?php echo e(route('category.index')); ?>" title="<?php echo e(__('Categories')); ?>">
                                                <i class="text-warning feather icon-list icondashboard"></i>
                                            </a>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php if(isset($zoom_enable) && $zoom_enable == 1): ?>
                        <div class="col-lg-3 col-md-6 col-12 mt-md-3">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-7">
                                            <h4><?php echo e($zoom); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('Zoom Meetings')); ?></p>
                                        </div>
                                        <?php if(Route::has('zoom.setting')): ?>
                                        <div class="col-5 text-right">
                                            <a href="<?php echo e(route('zoom.setting')); ?>">
                                                <i class="text-danger feather icon-maximize icondashboard" title="<?php echo e(__('Zoom Meetings')); ?>"></i>
                                            </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if(isset($gsetting) && $gsetting->bbl_enable == 1): ?>
                        <div class="col-lg-3 col-md-6 col-12 mt-md-3">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4><?php echo e($bbl); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('BB Meetings')); ?></p>
                                        </div>
                                        <?php if(Route::has('bbl.setting')): ?>
                                        <div class="col-6 text-right">
                                            <a href="<?php echo e(route('bbl.setting')); ?>" title="<?php echo e(__('BB Meetings')); ?>"><i
                                                class="text-primary feather icon-camera icondashboard"></i></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>


                        <?php if(isset($gsetting) && $gsetting->jitsimeet_enable == 1): ?>
                        <div class="col-lg-3 col-md-6 col-12 mt-md-3">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4><?php echo e($jitsi); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('Jitsi Meetings')); ?></p>
                                        </div>
                                        <?php if(Route::has('jitsi.dashboard')): ?>
                                        <div class="col-6 text-right">
                                            <a href="<?php echo e(route('jitsi.dashboard')); ?>" title="<?php echo e(__('Jitsi Meetings')); ?>"><i
                                                class="text-success feather icon-video icondashboard"></i></a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if(isset($gsetting) && $gsetting->googlemeet_enable == 1): ?>
                        <div class="col-lg-3 col-md-6 col-12 mt-md-3">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4><?php echo e($googlemeet); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('Google Meetings')); ?></p>
                                        </div>

                                        <?php if(Route::has('googlemeet.index')): ?>
                                        <div class="col-6 text-right">
                                            <a href="<?php echo e(route('googlemeet.index')); ?>" title="<?php echo e(__('Google Meetings')); ?>"><i
                                                class="text-warning feather icon-aperture icondashboard"></i></a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="col-lg-3 col-md-6 col-12 mt-md-3">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4><?php echo e($faq); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('Faq\'s')); ?></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <a href="<?php echo e(route('faq.index')); ?>" title="<?php echo e(__('Faqs')); ?>"><i
                                                class="text-secondary fa fa-question icondashboard"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-6 col-12 mt-md-3">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4><?php echo e($pages); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('Pages')); ?></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <a href="<?php echo e(route('page.index')); ?>" title="<?php echo e(__('Pages')); ?>"><i
                                                class="text-info feather icon-bookmark icondashboard"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 mt-md-3">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4><?php echo e($blogs); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('Blogs')); ?></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <a href="<?php echo e(route('blog.index')); ?>" title="<?php echo e(__('Blogs')); ?>"><i
                                                class="text-danger feather icon-message-square icondashboard"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 mt-md-3">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4><?php echo e($testimonial); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('Testimonials')); ?></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <a href="<?php echo e(route('testimonial.index')); ?>" title="<?php echo e(__('Testimonials')); ?>"><i
                                                class="text-primary feather icon-message-circle icondashboard"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 mt-md-3">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4><?php echo e($coupon); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('Coupons')); ?></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <a href="<?php echo e(route('coupon.index')); ?>" title="<?php echo e(__('Coupons')); ?>"><i
                                                class="text-secondary feather icon-percent icondashboard"></i></a>
                                            </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 col-12 mt-md-3">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4><?php echo e($orders); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('Orders')); ?></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <a href="<?php echo e(route('order.index')); ?>" title="<?php echo e(__('Orders')); ?>"><i
                                                class="text-success feather icon-shopping-cart icondashboard"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 mt-md-3">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4><?php echo e($refund); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('Refund Orders')); ?></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <a href="<?php echo e(route('refundorder.index')); ?>" title="<?php echo e(__('Refund Orders')); ?>"><i
                                                class="text-secondary  feather icon-trending-down icondashboard"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 mt-md-3">
                            <div class="card m-b-30 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <h4><?php echo e($follower); ?></h4>
                                            <p class="font-14 mb-0"><?php echo e(__('Followers')); ?></p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <a href="<?php echo e(route('follower.view')); ?>" title="<?php echo e(__('Followers')); ?>"><i
                                                class="text-danger  feather icon-user-check icondashboard"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?php if(!empty($topuser)): ?>
                    <div class="col-lg-12 col-xl-3 col-md-6 mt-md-3">
                        <div class="card m-b-30 shadow-sm">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-9">
                                        <h5 class="card-title mb-0"><?php echo e(__('Recent Users')); ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="user-slider">
                                <?php $__currentLoopData = $topuser; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topusers): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="user-slider">
                                    <div class="user-slider-item">
                                        <div class="card-body text-center">
                                            <span class="action-icon badge badge-primary-inverse">
                                                <?php if($topusers['user_img'] != null && $topusers['user_img'] !='' && @file_get_contents('images/user_img/'.$topusers['user_img'])): ?>
                                                    <img src="<?php echo e(url('images/user_img/'.$topusers['user_img'])); ?>" class="dashboard-imgs" alt="<?php echo e($topusers->fname); ?>">
                                                <?php else: ?>
                                                <img src="<?php echo e(Avatar::create($topusers->fname)->toBase64()); ?>" class="dashboard-imgs" alt="<?php echo e($topusers->fname); ?>">
                                                <?php endif; ?>
                                                
                                            </span>
                                            <h5><?php echo e($topusers->fname); ?></h5>
                                            <p><?php echo e($topusers->email); ?></p>
                                            <p><span class="badge badge-primary-inverse"><?php echo e(__('Verified')); ?>:<?php echo e($topusers['verified']); ?></span>
                                            </p>                                        
                                        </div>
                                        
                                    </div>

                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if(!empty($topinstructor)): ?>
                    <div class="col-lg-12 col-xl-3 col-md-6 mt-md-3">
                        <div class="card m-b-30">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-9">
                                        <h5 class="card-title mb-0"><?php echo e(__('Recent Instructors')); ?></h5>
                                    </div>                                
                                </div>
                            </div>
                            <div class="user-slider">
                                <?php $__currentLoopData = $topinstructor; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topinstructors): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="user-slider">
                                    <div class="user-slider-item">
                                        <div class="card-body text-center">
                                            <span class="action-icon badge badge-primary-inverse"><img src="<?php echo e(Avatar::create($topinstructors->fname)->toBase64()); ?>"
                                                    class="dashboard-imgs" alt="<?php echo e($topinstructors->fname); ?>"></span>
                                            <h5><?php echo e($topinstructors->fname); ?></h5>
                                            <p><?php echo e($topinstructors->email); ?></p>
                                            <p><span class="badge badge-primary-inverse"><?php echo e(__('Verified')); ?>:<?php echo e($topinstructors->verified); ?></span>
                                            </p>                                        
                                        </div>                                        
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if(!empty($topcourses)): ?>
                    <div class="col-lg-12 col-xl-3 col-md-6 mt-md-3">
                        <div class="card m-b-30">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-9">
                                        <h5 class="card-title mb-0"><?php echo e(__('Recent Courses')); ?></h5>
                                    </div>
                                
                                </div>
                            </div>
                            <div class="user-slider">
                                <?php $__currentLoopData = $topcourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $topcourses): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                <div class="user-slider">
                                    <div class="user-slider-item">
                                        <div class="card-body text-center">
                                            <span class="action-icon badge badge-primary-inverse">
                                                <?php if($topcourses['preview_image'] !== NULL && $topcourses['preview_image'] !== ''): ?>
                                                <img src="<?php echo e(asset('images/course/'.$topcourses['preview_image'])); ?>" class="dashboard-imgs" alt="<?php echo e($topcourses->title); ?>">
                                                
                                                <?php else: ?>
                                                <img src="<?php echo e(Avatar::create($topcourses->title)->toBase64()); ?>" class="dashboard-imgs" alt="<?php echo e($topcourses->title); ?>">
                                                <?php endif; ?>

                                            </span>
                                            <h5><?php echo e(str_limit($topcourses->title, $limit = 15, $end = '...')); ?></h5>
                                            <p><?php echo e(optional($topcourses->category)->title); ?></p>
                                            <p>
                                                <?php if($topcourses->discount_price == NULL): ?>
                                                <span class="badge badge-primary-inverse"><?php echo e(__('Price')); ?>:<?php echo e($topcourses->price); ?></span>
                                                <?php else: ?>
                                                <span class="badge badge-primary-inverse"><?php echo e(__('Price')); ?>:<?php echo e($topcourses->discount_price); ?></span>
                                                <?php endif; ?>
                                            </p>                                        
                                        </div>                                        
                                    </div>

                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if(!empty($toporder)): ?>
                    <div class="col-lg-12 col-xl-3 col-md-6 mt-md-3">
                        <div class="card m-b-30">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-9">
                                        <h5 class="card-title mb-0"><?php echo e(__('Recent Orders')); ?></h5>
                                    </div>
                                
                                </div>
                            </div>
                            <div class="user-slider">
                                <?php $__currentLoopData = $toporder; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $toporders): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!is_null($toporders->user)): ?>
                                <div class="user-slider">
                                    <div class="user-slider-item">
                                        <div class="card-body text-center">
                                            <span class="action-icon badge badge-primary-inverse"><img
                                                    src="<?php echo e(Avatar::create($toporders->user->fname)->toBase64()); ?>"
                                                    class="dashboard-imgs" alt="<?php echo e($toporders->user->fname); ?>"></span>
                                            <h5><?php echo e($toporders->user->fname); ?></h5>
                                            <p><?php echo e($toporders->payment_method); ?></p>
                                            <p><span class="badge badge-primary-inverse"><?php echo e(__('Price')); ?>:<?php echo e($toporders->total_amount); ?></span>
                                            </p>                                            
                                        </div>                                        
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="row">

                    <div class="col-md-12">
                        <div class="card m-b-30 mt-md-2">
                            <div class="card-header">
                                <h5 class="card-title"><?php echo e(__('Monthly Registered Users in')); ?> <?php echo e(date("Y")); ?></h5>
                            </div>
                            <div class="card-body">
                                <canvas height='180' id="chartjs-bar-chart" class="chartjs-chart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 mt-md-3 chart_height">
                        <div class="card m-b-30">
                            <div class="card-header">
                                <h5 class="card-title"><?php echo e(__('Total Orders in')); ?> <?php echo e(date("Y")); ?></h5>
                            </div>
                            <div class="card-body">
                                <div id="apex-chart">

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 mt-md-3 chart_height">
                        <div class="card m-b-30">
                            <div class="card-header">
                                <h5 class="card-title"><?php echo e(__('Users Distribution')); ?></h5>
                            </div>
                            <div class="card-body">
                                <div id="apex-circle-chart-custom-angel-chart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 mb-4">
                        <div class="card m-b-30">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-9">
                                        <h5 class="card-title mb-0"><?php echo e(__('Recent Courses')); ?></h5>
                                    </div>
                                    <div class="col-3">
                                        <div class="dropdown">
                                            <button class="btn btn-link p-0 font-18 float-right" type="button"
                                                id="upcomingTask" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false" title="<?php echo e(__('View All')); ?>">
                                                <i class="feather icon-more-horizontal-"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="upcomingTask">
                                                <a href="<?php echo e(url('course')); ?>" class="dropdown-item font-13" title="<?php echo e(__('View All')); ?>"><?php echo e(__('View All')); ?>

                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php
                                    $courses = App\Course::limit(5)->orderBy('id', 'DESC')->get()
                                    ?>
                                    <?php if(!$courses->isEmpty()): ?>
                                    <table class="table table-borderless">
                                        <thead></thead>
                                        <tbody>
                                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td>
                                                    <?php if($course['preview_image'] !== NULL && $course['preview_image'] !==
                                                    ''): ?>
                                                    <img src="images/course/<?php echo $course['preview_image'];  ?>"
                                                        alt="<?php echo e($course->title); ?>" class="img-responsive img-cousre">
                                                    <?php else: ?>
                                                    <img src="<?php echo e(Avatar::create($course->title)->toBase64()); ?>"
                                                        alt="<?php echo e($course->title); ?>" class="img-responsive img-cousre">
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <p><span class="text-dark"><?php echo e(str_limit($course['title'], $limit = 25, $end = '...')); ?></span><br>
                                                        <span class="product-description">
                                                            <?php echo e(str_limit($course->short_detail, $limit = 40, $end = '...')); ?>

                                                        </span>
                                                    </p>
                                                </td>
                                                <td><span class="badge badge-warning">
                                                    <?php if( $course->type == 1): ?>
                                                    <?php if($course->discount_price == !NULL): ?>
                                                    <?php if($gsetting['currency_swipe'] == 1): ?>
                                                    <i
                                                        class="<?php echo e($currency['icon']); ?>"></i><?php echo e($course['discount_price']); ?>

                                                    <?php else: ?>
                                                    <?php echo e($course['discount_price']); ?><i
                                                        class="<?php echo e($currency['icon']); ?>"></i>
                                                    <?php endif; ?>
                                                    <?php else: ?>
                                                    <?php if($gsetting['currency_swipe'] == 1): ?>
                                                    <i class="<?php echo e($currency['icon']); ?>"></i><?php echo e($course['price']); ?>

                                                    <?php else: ?>
                                                    <?php echo e($course['price']); ?><i class="<?php echo e($currency['icon']); ?>"></i>
                                                    <?php endif; ?>
                                                    <?php endif; ?>
                                                    <?php else: ?>
                                                    <?php echo e(__('Free')); ?>

                                                    <?php endif; ?>
                                                </span>
                                            </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12 mb-4">
                        <?php
                        $instructors = App\Instructor::limit(3)->orderBy('id', 'DESC')->get();
                        ?>
                        <?php if( !$instructors->isEmpty() ): ?>
                        <div class="card m-b-30">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-9">
                                        <h5 class="card-title mb-0"><?php echo e(__('Instructors Request')); ?></h5>
                                    </div>
                                    <div class="col-3">
                                        <div class="dropdown">
                                            <button class="btn btn-link p-0 font-18 float-right" type="button"
                                                id="upcomingTask" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false" title="<?php echo e(__('View All')); ?>">
                                                <i class="feather icon-more-horizontal-"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="upcomingTask">
                                                <a href="<?php echo e(route('all.instructor')); ?>" class="dropdown-item font-13" title="<?php echo e(__('View All')); ?>"><?php echo e(__('All Instructors')); ?></a>
                                                <a href="<?php echo e(url('requestinstructor')); ?>" class="dropdown-item font-13"><?php echo e(__('View All Instructors')); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php $__currentLoopData = $instructors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $instructor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($instructor->status == 0): ?>
                                    <div class="col-md-2 col-2">
                                        <img src="<?php echo e(asset('images/instructor/'.$instructor['image'])); ?>" alt="<?php echo e($instructor['fname']); ?>"
                                            class="online img-cousre">
                                    </div>
                                    <div class="col-md-10 col-10">
                                        <p>
                                            <span class="text-dark"><?php echo e($instructor['fname']); ?>&nbsp;<?php echo e($instructor['lname']); ?></span>
                                            <br>
                                            <span> <?php echo e(str_limit($instructor['detail'], $limit = 130, $end = '...')); ?></span>
                                        </p>
                                        <div class="text-right">
                                            <h6><?php echo e(__('Resume')); ?>:
                                                <a href="<?php echo e(asset('files/instructor/'.$instructor['file'])); ?>"
                                                    download="<?php echo e($instructor['file']); ?>"alt="<?php echo e(__('Download')); ?>" ><?php echo e(__('Download')); ?>

                                                    <i class="ion ion-md-download"></i>
                                                </a>
                                            </h6>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Start col -->
            <div class="col-lg-12">
            <h3> <?php echo e(auth()->user()->getRoleNames()[0]); ?> <?php echo e(__('Dashboard')); ?> </h3>
            </div>
            <div class="col-md-12">
            <div class="card bg-primary-rgba m-b-30">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-8">
                            <h5 class="card-title text-primary mb-1"><?php echo e(__('Welcome')); ?>, <?php echo e(Auth::user()->fname); ?>

                            </h5>
                            <p class="mb-0 text-primary font-14">"<?php echo e(__('May the sun shine brightest, Today')); ?>"</p>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script>
    var user = <?php echo json_encode($usergraph, 15, 512) ?>;
    var course = <?php echo json_encode($coursegraph, 15, 512) ?>;
    var category = <?php echo json_encode($categorygraph, 15, 512) ?>;
    var order = <?php echo json_encode($ordergraph, 15, 512) ?>;
    var refund = <?php echo json_encode($refundgraph, 15, 512) ?>;
    var coupon = <?php echo json_encode($coupongraph, 15, 512) ?>;
    var zoom = <?php echo json_encode($zoomgraph, 15, 512) ?>;
    var bbl = <?php echo json_encode($bblgraph, 15, 512) ?>;
    var jitsi = <?php echo json_encode($jitsigraph, 15, 512) ?>;
    var googlemeet = <?php echo json_encode($googlemeetgraph, 15, 512) ?>;
    var faq = <?php echo json_encode($faqgraph, 15, 512) ?>;
    var page = <?php echo json_encode($pagegraph, 15, 512) ?>;
    var blog = <?php echo json_encode($bloggraph, 15, 512) ?>;
    var testimonial = <?php echo json_encode($testimonialgraph, 15, 512) ?>;
    var instructor = <?php echo json_encode($instructorgraph, 15, 512) ?>;
    var instructor = <?php echo json_encode($instructorgraph, 15, 512) ?>;
    var follower = <?php echo json_encode($followergraph, 15, 512) ?>;
    var datas = <?php echo json_encode($datas, 15, 512) ?>;
    var adminchart = <?php echo json_encode($admincharts, 15, 512) ?>;

    "use strict";
    $(document).ready(function () {
        var barChartID = document.getElementById("chartjs-bar-chart").getContext('2d');
        var barChart = new Chart(barChartID, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                    'Dec'
                ],
                datasets: [{
                    label: 'Monthly Registred Users',
                    backgroundColor: ["#506fe4", "#506fe4", "#506fe4", "#506fe4", "#506fe4",
                        "#506fe4", "#506fe4", "#506fe4", "#506fe4", "#506fe4", "#506fe4",
                        "#506fe4", "#506fe4"
                    ],
                    borderColor: ["#506fe4", "#506fe4", "#506fe4", "#506fe4", "#506fe4",
                        "#506fe4", "#506fe4", "#506fe4", "#506fe4", "#506fe4", "#506fe4",
                        "#506fe4", "#506fe4"
                    ],
                    borderWidth: 1,
                    data: datas,

                }]
            },
            options: {


                responsive: true,
                legend: {
                    position: 'top',
                    height: 100
                },
                title: {
                    display: false,
                    text: 'Chart.js Bar Chart'
                },
                scales: {
                    xAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Month'
                        },
                        gridLines: {
                            color: 'rgba(0,0,0,0.05)',
                            lineWidth: 1,
                            borderDash: [0]
                        }
                    }],
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: 'Value'
                        },

                        gridLines: {
                            color: 'rgba(0,0,0,0.05)',
                            lineWidth: 1,
                            borderDash: [0]
                        }
                    }]
                }
            }
        });
    });
    var datas1 = <?php echo json_encode($datas1, 15, 512) ?>;
    "use strict";
    $(document).ready(function () {
        var options = {
            chart: {
                height: 285,
                type: 'area',
                toolbar: {
                    show: false
                },
                zoom: {
                    type: 'x',
                    enabled: false,
                    autoScaleYaxis: true
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
            },
            colors: ['#506fe4'],
            series: [{
                name: 'Orders',
                data: datas1
            }],
            legend: {
                show: false,
            },
            xaxis: {

                title: {
                    text: 'Months',
                },
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                    'Dec'
                ],
                axisBorder: {
                    show: true,
                    color: 'rgba(0,0,0,0.05)'
                },
                axisTicks: {
                    show: true,
                    color: 'rgba(0,0,0,0.05)'
                }
            },
            yaxis: {
                title: {
                    text: 'Orders',
                },
                min: 0
            },
            grid: {
                row: {
                    colors: ['transparent', 'transparent'],
                    opacity: .5
                },
                borderColor: 'rgba(0,0,0,0.05)'
            },


        }
        var chart = new ApexCharts(
            document.querySelector("#apex-chart"),
            options
        );
        chart.render();
    });
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>