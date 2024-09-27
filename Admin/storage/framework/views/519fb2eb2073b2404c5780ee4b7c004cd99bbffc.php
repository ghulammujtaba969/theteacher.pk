<!-- Start Topbar Mobile -->
<div class="topbar-mobile">
    <div class="row align-items-center">
        <div class="col-md-12">
            <div class="mobile-logobar">
                <a href="<?php echo e(url('/')); ?>" class="mobile-logo"><img src="<?php echo e(url('images/favicon/'.$gsetting->favicon)); ?>" class="img-fluid" alt="logo"></a>
            </div>
            <div class="mobile-togglebar">
                <ul class="list-inline mb-0">
                    
                    <li class="list-inline-item">
                        <div class="topbar-toggle-icon">
                            <a class="topbar-toggle-hamburger" href="javascript:void();">
                                <img src="<?php echo e(url('admin_assets/assets/images/svg-icon/horizontal.svg')); ?>" class="img-fluid menu-hamburger-horizontal" alt="horizontal">
                                <img src="<?php echo e(url('admin_assets/assets/images/svg-icon/verticle.svg')); ?>" class="img-fluid menu-hamburger-vertical" alt="verticle">
                             </a>
                         </div>
                    </li>
                    <li class="list-inline-item">
                        <div class="menubar">
                            <a class="menu-hamburger" href="javascript:void();">
                                <img src="<?php echo e(url('admin_assets/assets/images/svg-icon/menu.svg')); ?>" class="img-fluid menu-hamburger-collapse" alt="collapse">
                                <img src="<?php echo e(url('admin_assets/assets/images/svg-icon/close.svg')); ?>" class="img-fluid menu-hamburger-close" alt="close">
                             </a>
                         </div>
                    </li>                                
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- Start Topbar -->
<div class="topbar">
    <!-- Start row -->
    <div class="row align-items-center">
        <!-- Start col -->
        <div class="col-md-12 align-self-center">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="togglebar">
                        <div class="breadcrumbbar">
                            <h4 class="page-title"><?php echo e($heading ??''); ?></h4>
                            <div class="breadcrumb-list">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo e(url('/')); ?>"><?php echo e(__('Dashboard')); ?></a></li>
                                    <?php if(isset($title)): ?>
                                    <li class="breadcrumb-item "><?php echo e($title ?? ''); ?></li>
                                    <?php endif; ?>
                                    <?php if(isset($title1)): ?>
                                    <li class="breadcrumb-item "><?php echo e($title1 ?? ''); ?></li>
                                    <?php endif; ?>
                                    <?php if(isset($title2)): ?>
                                    <li class="breadcrumb-item active"><?php echo e($title2 ?? ''); ?></li>
                                    <?php endif; ?>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="infobar">
                        <?php echo $__env->make('sweetalert::alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <ul class="list-inline mb-0">
                            <!-- - site visit start -->
                            <li class="mt-2 mr-2 list-inline-item">
                                <div class="languagebar">
                                <a href="<?php echo e(url('/')); ?>" target="_blank"><span class="live-icon"><?php echo e(__('Visit Site')); ?></span>&nbsp;<i class="feather icon-external-link" aria-hidden="true"></i></a>   
                                </div>
                                
                            </li>
                            <li class="mt-2 mr-2 list-inline-item">        
                                <a href="#" class="menu-tigger infobar-icon"><img src="<?php echo e(url('admin_assets/assets/images/svg-icon/ai.svg')); ?>" class="img-fluid" alt="<?php echo e(__('Ai Tool')); ?>"><span><?php echo e(__('Ai Tool')); ?></span></a>
                            </li>
                            <!-- = site visit end -->
                            <!-- notification start -->
                            <?php if(Auth()->user()->role == "admin"): ?>
                            <li class="list-inline-item">
                                <div class="notifybar">
                                    <div class="dropdown">
                                        <a class="dropdown-toggle infobar-icon" href="#" role="button" id="notoficationlink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="<?php echo e(url('admin_assets/assets/images/svg-icon/notifications.svg')); ?>" class="img-fluid" alt="notifications">
                                        <span class="live-icon"><?php echo e(Auth()->user()->unreadNotifications->where('type', 'App\Notifications\AdminOrder')->count()); ?></span></a>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="notoficationlink">
                                            <div class="notification-dropdown-title">
                                                <h6><?php echo e(__('You have')); ?> <?php echo e(Auth()->user()->unreadNotifications->where('type', 'App\Notifications\AdminOrder')->count()); ?> <?php echo e(__('notifications')); ?></h6>                            
                                            </div>
                                            <ul class="list-unstyled">  
                                            <?php $i=0;?>
                                            <?php $__currentLoopData = Auth()->user()->unreadNotifications->where('type', 'App\Notifications\AdminOrder'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php $i++;?>
                                                                                            
                                                <li class="media dropdown-item">
                                                    
                                                    <span class="mr-3 action-icon badge badge-warning-inverse"><?php echo $i; ?></span>
                                                    <div class="media-body">
                                                        <a href="#"><p><span class="timing"><?php echo e($notification->data['data']); ?></span></p></a>
                                                    </div>
                                                </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                                
                                            </ul>
                                            <div class="notification-dropdown-title">
                                            <a href="<?php echo e(route('deleteNotification')); ?>"><p><?php echo e(__('Clear all')); ?></p></a>                            
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <?php endif; ?>
                            <!-- notification end -->
                            <!-- language start -->
                            <?php
                            $languages = App\Language::all(); 
                            ?>
                            <li class="list-inline-item">
                                <div class="languagebar">
                                    <div class="dropdown">
                                        <a class="dropdown-toggle" href="#" role="button" id="languagelink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="live-icon"><?php echo e(Session::has('changed_language') ? Session::get('changed_language') : ''); ?></span><span class="feather icon-chevron-down live-icon"></span></a>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="languagelink">
                                            <?php if(isset($languages) && count($languages) > 0): ?>
                                            <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a class="dropdown-item" href="<?php echo e(route('languageSwitch', $language->local)); ?>">
                                                <i class="feather icon-globe"></i>
                                                <?php echo e($language->name); ?> (<?php echo e($language->local); ?>)</a>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            <?php endif; ?>
                                            
                                        </div>
                                    </div>
                                </div>                                   
                            </li>
                                <!-- language end -->
                            <li class="list-inline-item">
                                <div class="profilebar">
                                    <div class="dropdown">
                                        <a class="dropdown-toggle" href="#" role="button" id="profilelink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                                        <?php if(Auth()->User()['user_img'] != null && Auth()->User()['user_img'] !='' && @file_get_contents('images/user_img/'.Auth::user()['user_img'])): ?>
                                            <img src="<?php echo e(url('images/user_img/'.Auth()->User()['user_img'])); ?>" alt="profilephoto" class="rounded img-fluid">
                                        <?php else: ?>
                                            <img <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> src="<?php echo e(Avatar::create(Auth::user()->fname)->toBase64()); ?>" alt="profilephoto" class="rounded img-fluid">
                                        <?php endif; ?>

                                        <span class="live-icon"><?php echo e(__('Hi')); ?> <?php echo e(Auth::user()->fname); ?></span><span class="feather icon-chevron-down live-icon"></span></a>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="profilelink">
                                            <div class="dropdown-item">
                                                <div class="profilename">
                                                    <h5><?php echo e(Auth::user()->fname); ?></h5>
                                                </div>
                                            </div>
                                            <div class="userbox">
                                                <ul class="list-unstyled mb-0">
                                                    <li class="media dropdown-item">
                                                        <a href="<?php echo e(url('user/edit/'.Auth()->User()->id)); ?>" class="profile-icon"><img src="<?php echo e(url('admin_assets/assets/images/svg-icon/crm.svg')); ?>" class="img-fluid" alt="user"><?php echo e(__('My Profile')); ?></a>
                                                    </li>
                                                                                                    
                                                    <li class="media dropdown-item">
                                                        <a href="<?php echo e(route('logout')); ?>" class="profile-icon"  onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();"><img src="<?php echo e(url('admin_assets/assets/images/svg-icon/logout.svg')); ?>" class="img-fluid" alt="logout"><?php echo e(__('Logout')); ?></a>

                                                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                                            <?php echo csrf_field(); ?>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                   
                            </li>
                        </ul>
                    </div>
                    <?php echo $__env->make('admin.openai.topbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        </div>
        <!-- End col -->
    </div> 
    <!-- End row -->
</div>
<!-- End Topbar -->
<!-- Start Breadcrumbbar -->                    
<?php echo $__env->yieldContent('breadcum'); ?>
<!-- End Breadcrumbbar -->
<?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/admin/layouts/topbar.blade.php ENDPATH**/ ?>