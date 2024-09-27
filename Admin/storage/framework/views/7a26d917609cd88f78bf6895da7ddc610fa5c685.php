<?php if($gsetting->promo_enable == 1): ?>
<div id="promo-outer">
    <div id="promo-inner">
        <a href="<?php echo e($gsetting['promo_link']); ?>" title="<?php echo e($gsetting['promo_text']); ?>"><?php echo e($gsetting['promo_text']); ?></a>
        <span id="close">x</span>
    </div>
</div>
<div id="promo-tab" class="display-none"><?php echo e(__('SHOW')); ?></div>
<?php endif; ?>

<section id="nav-bar" class="nav-bar-main-block" data-toggle="sticky-onscroll">
    <div class="container-xl">
        <!-- start navigation -->
        <div class="navigation fullscreen-search-block">
            <span style="font-size:30px;cursor:pointer" onclick="openNav()" class="hamburger">&#9776; </span>
            <div class="logo">
                  <?php if($gsetting->logo_type == 'L'): ?>
                    <a href="<?php echo e(url('/')); ?>" title="<?php echo e($gsetting->project_title); ?>"><img src="<?php echo e(asset('images/logo/'.$gsetting->logo)); ?>" class="img-fluid" alt="<?php echo e($gsetting->project_title); ?>"></a>
                <?php else: ?>
                    <a href="<?php echo e(url('/')); ?>" title="<?php echo e($gsetting->project_title); ?>"><b><div class="logotext"><?php echo e($gsetting->project_title); ?></div></b></a>
                <?php endif; ?>
            </div>
            <div class="nav-search nav-wishlist">                
                <a href="#find" title="<?php echo e(__('Search')); ?>"><i data-feather="search"></i></a>
            </div>
            <?php if(auth()->guard()->check()): ?>
            <div class="shopping-cart">
                <a href="<?php echo e(route('cart.show')); ?>" title="<?php echo e(__('Cart')); ?>"><i data-feather="shopping-cart"></i></a>
                <span class="red-menu-badge red-bg-success">
                    <?php
                        $item = App\Cart::where('user_id', Auth::User()->id)->count();
                        if($item>0){
                            echo $item;
                        }
                        else{
                            echo "0";
                        }
                    ?>
                </span>
            </div>
            <div class="nav-wishlist">
                <div id="notification_li">
                    <a href="<?php echo e(url('send')); ?>" id="notificationLinkk" title="<?php echo e(__('Notifications')); ?>"><i data-feather="bell"></i></a>
                    <span class="red-menu-badge red-bg-success">
                        <?php echo e(Auth()->user()->unreadNotifications->where('type', 'App\Notifications\UserEnroll')->count()); ?>

                    </span>
                    <div id="notificationContainerr">
                    <div id="notificationTitle"><?php echo e(__('Notifications')); ?></div>
                    <div id="notificationsBody" class="notifications">
                        <ul>
                            <?php $__currentLoopData = Auth()->user()->unreadNotifications->where('type', 'App\Notifications\UserEnroll'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="unread-notification">
                                    <a href="<?php echo e(url('notifications/'.$notification->id)); ?>">          
                                    <div class="notification-image">
                                        <?php if($notification->data['image'] !== NULL ): ?>
                                            <img src="<?php echo e(asset('images/course/'.$notification->data['image'])); ?>" alt="<?php echo e(__('Notifications')); ?>" class="img-fluid" >
                                        <?php else: ?>
                                            <img src="<?php echo e(Avatar::create($notification->data['id'])->toBase64()); ?>" alt="<?php echo e(__('Notifications')); ?>" class="img-fluid">
                                        <?php endif; ?>
                                    </div>
                                    <div class="notification-data">
                                        In <?php echo e(str_limit($notification->data['id'], $limit = 20, $end = '...')); ?>

                                        <br>
                                        <?php echo e(str_limit($notification->data['data'], $limit = 20, $end = '...')); ?>

                                    </div>
                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            <?php $__currentLoopData = Auth()->user()->readNotifications->where('type', 'App\Notifications\UserEnroll'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <a href="<?php echo e(route('mycourse.show')); ?>" title="<?php echo e(__('Notifications')); ?>">
                                    <div class="notification-image">
                                        <?php if($notification->data['image'] !== NULL ): ?>
                                            <img src="<?php echo e(asset('images/course/'.$notification->data['image'])); ?>" alt="<?php echo e(__('Notifications')); ?>" class="img-fluid" >
                                        <?php else: ?>
                                           <img src="<?php echo e(Avatar::create($notification->data['id'])->toBase64()); ?>" alt="<?php echo e(__('Notifications')); ?>" class="img-fluid">
                                        <?php endif; ?>
                                    </div>
                                    <div class="notification-data">
                                        In <?php echo e(str_limit($notification->data['id'], $limit = 20, $end = '...')); ?>

                                        <br>
                                        <?php echo e(str_limit($notification->data['data'], $limit = 20, $end = '...')); ?>

                                    </div>
                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <div id="notificationFooter"><a href="<?php echo e(route('deleteNotification')); ?>" title="<?php echo e(__('Clear All')); ?>"><?php echo e(__('Clear All')); ?></a></div>
                    </div>
                </div>
            </div>            
            <?php endif; ?>   
            <div id="mySidenav" class="sidenav">
              <a href="javascript:void(0)" class="closebtn" onclick="closeNav()" title="<?php echo e(__('Close')); ?>">&times;</a>
                <?php if(auth()->guard()->guest()): ?>
                <div class="login-block">
                    <a href="<?php echo e(route('register')); ?>" class="btn btn-primary" title="<?php echo e(__('Signup')); ?>"><?php echo e(__('Signup')); ?></a>
                    <a href="<?php echo e(route('login')); ?>" class="btn btn-secondary" title="<?php echo e(__('Login')); ?>"><?php echo e(__('Login')); ?></a>
                </div>
                <?php endif; ?>
                <?php if(auth()->guard()->check()): ?>

                <div id="notificationTitle">
                     <?php if(Auth::User()['user_img'] != null && Auth::User()['user_img'] !='' && @file_get_contents('images/user_img/'.Auth::user()['user_img'])): ?>
                      <img src="<?php echo e(url('/images/user_img/'.Auth::User()->user_img)); ?>" class="dropdown-user-circle" alt="<?php echo e(Auth::User()->fname); ?>">
                    <?php else: ?>
                        <img src="<?php echo e(asset('images/default/user.jpg')); ?>" class="dropdown-user-circle" alt="<?php echo e(Auth::User()->fname); ?>">
                    <?php endif; ?>
                    <div class="user-detailss">
                        Hi, <?php echo e(Auth::User()->fname); ?>                        
                    </div>                    
                </div>
                <div class="login-block">
                    <a href="<?php echo e(route('logout')); ?>" title="<?php echo e(__('Logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <div id="notificationFooter">
                            <?php echo e(__('Logout')); ?>

                            
                            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="display-none">
                                <?php echo csrf_field(); ?>
                            </form>
                        </div>
                    </a>
                </div>
                <?php endif; ?>
                <?php
                    $categories = App\Categories::orderBy('position','ASC')->with(['subcategory','subcategory.childcategory'])->get();
                ?>                
                <div class="wrapper center-block">                    
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    <?php $__currentLoopData = $categories->where('status', '1'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <div class="panel panel-default">
                        <div class="panel-heading active" role="tab" id="headingOne">
                            <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne<?php echo e($cate->id); ?>" aria-expanded="true" aria-controls="collapseOne">
                                <i class="fa <?php echo e($cate->icon); ?> rgt-10"></i> <label class="prime-cat" data-url="<?php echo e(route('category.page',['id' => $cate->id, 'category' => str_slug(str_replace('-','&',$cate->slug))])); ?>"><?php echo e(str_limit($cate->title, $limit = 20, $end = '..')); ?></label> 
                            </a>
                            </h4>
                        </div>                        
                        <div id="collapseOne<?php echo e($cate->id); ?>" class="subcate-collapse panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                        <?php $__currentLoopData = $cate->subcategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php if($sub->status ==1): ?>
                          <div class="panel-body">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingeleven">
                                  <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseeleven<?php echo e($sub->id); ?>" aria-expanded="false" aria-controls="collapseeleven">
                                      <i class="fa <?php echo e($sub->icon); ?> rgt-10"></i> <label class="sub-cate" data-url="<?php echo e(route('subcategory.page',['id' => $sub->id, 'category' => str_slug(str_replace('-','&',$sub->slug))])); ?>"><?php echo e(str_limit($sub->title, $limit = 15, $end = '..')); ?></label>

                                    </a>
                                  </h4>
                                </div>
                                <div id="collapseeleven<?php echo e($sub->id); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingeleven">
                                  <?php $__currentLoopData = $sub->childcategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                  <?php if($child->status ==1): ?>
                                  <div class="panel-body sub-cat">
                                    <i class="fa <?php echo e($child->icon); ?> rgt-10"></i> <label class="child-cate" data-url="<?php echo e(route('childcategory.page',['id' => $child->id, 'category' => str_slug(str_replace('-','&',$child->slug))])); ?>"><?php echo e($child->title); ?> </label>
                                  </div>
                                  <?php endif; ?>
                                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>                                
                            </div>
                          </div>
                          <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>                        
                      </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </div>                      
                </div>
                <div>
                    <?php
                        $menus = App\Menu::get();
                        $pages = App\Page::get();
                    ?>
                                            <?php if(isset($menus)): ?>
                    <ul>
                        <li>
                            <ul>
                                <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($menu->link_by == 'url'): ?>
                                <li><a href="<?php echo e($menu->url); ?>" title="<?php echo e($menu->title); ?>"><i data-feather="align-justify"></i><?php echo e($menu->title); ?></a></li>
                                <?php endif; ?>
                                <?php if($menu->link_by == 'page'): ?>
                                <li><a href="<?php echo e(route('page.show', $menu->page->slug)); ?>" title="<?php echo e($menu->title); ?>"><i data-feather="align-justify"></i><?php echo e($menu->title); ?></a></li>
                                <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </li>
                    </ul>
                    <?php endif; ?>
                </div>
                <?php if(auth()->guard()->check()): ?>              
    
                   <div class="sidebar-nav-icon">
                    <ul>
                        <?php if(Auth::User()->role == "admin" ): ?>
                        
                        <a target="_blank" href="<?php echo e(url('/admins')); ?>" title="<?php echo e(__('Admin Dashboard')); ?>"><li><i data-feather="pie-chart"></i><?php echo e(__('Admin Dashboard')); ?></li></a>                       
                        <?php endif; ?>
                        <?php if(Auth::User()->role == "instructor"): ?>
                        <a target="_blank" href="<?php echo e(url('/instructor')); ?>" title="<?php echo e(__('Instructor Dashboard')); ?>"><li><i data-feather="pie-chart"></i><?php echo e(__('Instructor Dashboard')); ?></li></a>
                        <?php endif; ?>
                        <a href="<?php echo e(route('mycourse.show')); ?>" title="<?php echo e(__('My Courses')); ?>"><li><i data-feather="book-open"></i><?php echo e(__('My Courses')); ?></li></a>
                        <a href="<?php echo e(route('wishlist.show')); ?>" title="<?php echo e(__('Wishlist')); ?>"><li><i data-feather="heart"></i><?php echo e(__('Wishlist')); ?></li></a>
                        <a href="<?php echo e(route('purchase.show')); ?>" title="<?php echo e(__('Purchased History')); ?>"><li><i data-feather="shopping-cart"></i><?php echo e(__('Purchased History')); ?></li></a>
                        <a href="<?php echo e(route('profile.show',Auth::User()->id)); ?>" title="<?php echo e(__('Profile')); ?>"><li><i data-feather="user"></i><?php echo e(__('Profile')); ?></li></a>
                        <?php if(Auth::User()->role == "user"): ?>
                        <?php if($gsetting->instructor_enable == 1): ?>
                        <a href="#" data-toggle="modal" data-target="#myModalinstructor" title="<?php echo e(__('Become An Instructor')); ?>"><li><i data-feather="shield"></i><?php echo e(__('Become An Instructor')); ?></li></a>
                        <?php endif; ?>
                        <?php endif; ?>
                        <a href="<?php echo e(route('flash.deals')); ?>" title="<?php echo e(__('Flash Deals')); ?>"><li><i data-feather="battery-charging"></i><?php echo e(__('Flash Deals')); ?></li></a>
                        <?php if(env('ENABLE_INSTRUCTOR_SUBS_SYSTEM') == 1): ?>
                        <?php if(Auth::User()->role == "instructor"): ?>
                        <a href="<?php echo e(route('plan.page')); ?>" title="<?php echo e(__('Instructor Plan')); ?>"><li><i data-feather="tag"></i><?php echo e(__('Instructor Plan')); ?></li></a>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php if(Auth::User()->role == "user" || Auth::User()->role == "instructor"): ?>
                        <?php if($gsetting->device_control == 1): ?>
                        <a href="<?php echo e(route('active.courses')); ?>"  title="<?php echo e(__('Watchlist')); ?>"><li><i data-feather="framer"></i><?php echo e(__('Watchlist')); ?></li></a>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php if($gsetting->donation_enable == 1): ?>
                        <a target="__blank" href="<?php echo e($gsetting->donation_link); ?>" title="<?php echo e(__('Donation')); ?>"><li><i data-feather="framer"></i><?php echo e(__('Donation')); ?></li></a>
                        <?php endif; ?>
                        <?php if(Schema::hasTable('affiliate') && Schema::hasTable('wallet_settings')): ?>
                        <?php
                            $affiliate = App\Affiliate::first();
                            $wallet_settings = App\WalletSettings::first();
                        ?>       
                        <?php if(isset($wallet_settings) && $wallet_settings->status == 1): ?>
                        <a href="<?php echo e(url('/wallet')); ?>" title="<?php echo e(__('Wallet')); ?>"><li><i class="icon-wallet icons"></i><?php echo e(__('Wallet')); ?></li></a>
                        <?php endif; ?>
                        <?php if(isset($affiliate) && $affiliate->status == 1): ?>
                        <a href="<?php echo e(route('get.affiliate')); ?>" title="<?php echo e(__('Affiliate')); ?>"><li><i data-feather="users"></i><?php echo e(__('Affiliate')); ?></li></a>
                        <?php endif; ?>
                        <?php endif; ?>
                        <a href="<?php echo e(route('compare.index')); ?>" title="<?php echo e(__('Compare')); ?>"><li><i data-feather="bar-chart"></i><?php echo e(__("Compare")); ?></li></a>
                        <?php if(Module::has('Resume') && Module::find('Resume')->isEnabled()): ?>
                            <?php echo $__env->make('resume::front.searchresume', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endif; ?>
                        <?php if(Module::has('Resume') && Module::find('Resume')->isEnabled()): ?>
                            <?php echo $__env->make('resume::front.job.icon', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php endif; ?>                       
                        <?php if(Module::find('Forum') && Module::find('Forum')->isEnabled()): ?>
                            <?php if($gsetting->forum_enable == 1): ?>
                                <?php echo $__env->make('forum::layouts.sidebar_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            <?php endif; ?>
                        <?php endif; ?>
                         <a href="<?php echo e(route('my.leaderboard')); ?>" title="<?php echo e(__('leader Board')); ?>"><li><i class="icon-chart icons"></i><?php echo e(__('Leader Board')); ?></li></a>
                        <?php if(Auth::User()->role == "user"): ?>
                        <a href="<?php echo e(route('studentprofile')); ?>" title="<?php echo e(__('Share Profile')); ?>"><li><i data-feather="share"></i><?php echo e(__('Share profile')); ?></li></a>
                       <?php endif; ?>
                       <a href="<?php echo e(route('affilate.report')); ?>" title="<?php echo e(__('Affiliate Dashboard')); ?>"><li><i data-feather="users"></i><?php echo e(__('Affiliate Dashboard')); ?></li></a>
                    </ul>
                </div>
                <div>
                    <?php if(Module::has('Ebook') && Module::find('Ebook')->isEnabled()): ?>
                    <?php echo $__env->make('ebook::sidebar.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>        
        <!-- end navigation -->
        <div class="row smallscreen-search-block">
            <div class="col-lg-5">
                <div class="row">
                    <div class="col-lg-5 col-md-4 col-sm-12">
                        <div class="logo">
                            <?php if($gsetting->logo_type == 'L'): ?>
                                <a href="<?php echo e(url('/')); ?>" title="<?php echo e($gsetting->project_title); ?>"><img src="<?php echo e(asset('images/logo/'.$gsetting->logo)); ?>" class="img-fluid" alt="<?php echo e($gsetting->project_title); ?>"></a>
                            <?php else: ?>
                                <a href="<?php echo e(url('/')); ?>"><b><div class="logotext"><?php echo e($gsetting->project_title); ?></div></b></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-4 col-sm-12">
                        <div class="navigation">
                            <div id="cssmenu">
                                <ul>
                                    <li class="mr-4"><a href="#" title="<?php echo e(__('Categories')); ?>"><i data-feather="grid"></i><?php echo e(__('Categories')); ?></a>                                       
                                        <ul>
                                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php if($cate->status == 1 ): ?>
                                            <li><a href="<?php echo e(route('category.page',['id' => $cate->id, 'category' => $cate->title])); ?>" title="<?php echo e($cate->title); ?>"><i class="fa <?php echo e($cate->icon); ?> rgt-20"></i><?php echo e(str_limit($cate->title, $limit = 25, $end = '..')); ?> <i data-feather="chevron-right" class="float-right"></i></a>
                                            <ul>   
                                                <?php $__currentLoopData = $cate->subcategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <?php if($sub->status ==1): ?>
                                                <li><a href="<?php echo e(route('subcategory.page',['id' => $sub->id, 'category' => $sub->title])); ?>" title="<?php echo e($sub->title); ?>"><i class="fa <?php echo e($sub->icon); ?> rgt-20"></i><?php echo e(str_limit($sub->title, $limit = 25, $end = '..')); ?>

                                                    <i data-feather="chevron-right" class="float-right"></i></a>
                                                    <ul>
                                                        <?php $__currentLoopData = $sub->childcategory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <?php if($child->status ==1): ?>
                                                        <li>
                                                            <a href="<?php echo e(route('childcategory.page',['id' => $child->id, 'category' => $child->title])); ?>" title="<?php echo e($child->title); ?>"><i class="fa <?php echo e($child->icon); ?> rgt-20"></i><?php echo e(str_limit($child->title, $limit = 25, $end = '..')); ?></a>
                                                        </li>
                                                        <?php endif; ?>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </ul>
                                                </li>
                                                <?php endif; ?>
                                               <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                            </li>
                                            <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </li>
                                    <?php if(Module::has('Ebook') && Module::find('Ebook')->isEnabled()): ?>
                                    <?php echo $__env->make('ebook::sidebar.nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <?php endif; ?>                              
                              </ul>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="col-lg-7">
                <?php if(auth()->guard()->guest()): ?>
                <div class="row">
                    <div class="col-lg-7 col-md-6">
                        <div class="learning-business">                            
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="shopping-cart">
                            <a href="<?php echo e(route('cart.show')); ?>" title="<?php echo e(__('Cart')); ?>"><i data-feather="shopping-cart"></i></a>
                            <span class="red-menu-badge red-bg-success">
                                <?php
                                    $item = session()->get('cart.add_to_cart');                                    
                                    if(isset($item) && count($item)>0){
                                        echo count(array_unique($item));
                                    }
                                    else{
                                        echo "0";
                                    }
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="search search-one" id="search">
                            <form method="GET" id="searchform" action="<?php echo e(route('search')); ?>">
                              <div class="search-input-wrap">
                                <input class="search-input" name="searchTerm" placeholder="<?php echo e(__('Search in Site')); ?>" type="text" id="course_name" autocomplete="off" />
                              </div>
                              <input class="search-submit" type="submit" id="go" value="">
                              <div class="icon"><i data-feather="search"></i></div>
                              <div id="course_data"></div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="Login-btn">
                            <a href="<?php echo e(route('login')); ?>" class="btn btn-secondary" title="<?php echo e(__('Login')); ?>"><?php echo e(__('Login')); ?></a>
                            <a href="<?php echo e(route('register')); ?>" class="btn btn-primary" title="<?php echo e(__('Signup')); ?>"><?php echo e(__('Signup')); ?></a>                            
                        </div> 
                    </div>
                <?php endif; ?>
                <?php if(auth()->guard()->check()): ?>
                <div class="row">         
                    <div class="col-lg-5 col-md-3 col-6">
                        <div class="learning-business learning-business-two">                           
                        </div>
                    </div>                  
                    <div class="col-lg-1 col-md-1 col-sm-2 col-2">
                        <div class="nav-wishlist">
                            <ul id="nav">
                                <li id="notification_li">
                                    <a href="<?php echo e(url('send')); ?>" id="notificationLink" title="<?php echo e(__('Notifications')); ?>"><i data-feather="bell"></i></a>
                                    <span class="red-menu-badge red-bg-success">
                                        <?php echo e(Auth()->user()->unreadNotifications->where('type', 'App\Notifications\UserEnroll')->count()); ?>

                                    </span>
                                    <div id="notificationContainer">
                                    <div id="notificationTitle"><?php echo e(__('Notifications')); ?></div>
                                    <div id="notificationsBody" class="notifications">
                                        <ul>
                                            <?php $__currentLoopData = Auth()->user()->unreadNotifications->where('type', 'App\Notifications\UserEnroll'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li class="unread-notification">
                                                    <a href="<?php echo e(url('notifications/'.$notification->id)); ?>" title="<?php echo e(__('Notifications')); ?>">          
                                                    <div class="notification-image">
                                                        <?php if($notification->data['image'] !== NULL ): ?>
                                                            <img src="<?php echo e(asset('images/course/'.$notification->data['image'])); ?>" alt="<?php echo e(__('Notifications')); ?>" class="img-fluid" >
                                                        <?php else: ?>
                                                            <img src="<?php echo e(Avatar::create($notification->data['id'])->toBase64()); ?>" alt="<?php echo e(__('Notifications')); ?>" class="img-fluid">
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="notification-data">
                                                        In <?php echo e(str_limit($notification->data['id'], $limit = 20, $end = '...')); ?>

                                                        <br>
                                                        <?php echo e(str_limit($notification->data['data'], $limit = 20, $end = '...')); ?>

                                                    </div>
                                                    </a>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                            <?php $__currentLoopData = Auth()->user()->readNotifications->where('type', 'App\Notifications\UserEnroll'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li>
                                                    <a href="<?php echo e(route('mycourse.show')); ?>">
                                                    <div class="notification-image">
                                                        <?php if($notification->data['image'] !== NULL ): ?>
                                                            <img src="<?php echo e(asset('images/course/'.$notification->data['image'])); ?>" alt="<?php echo e(__('Courses')); ?>" class="img-fluid" >
                                                        <?php else: ?>
                                                           <img src="<?php echo e(Avatar::create($notification->data['id'])->toBase64()); ?>" alt="<?php echo e(__('Courses')); ?>" class="img-fluid">
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="notification-data">
                                                        In <?php echo e(str_limit($notification->data['id'], $limit = 20, $end = '...')); ?>

                                                        <br>
                                                        <?php echo e(str_limit($notification->data['data'], $limit = 20, $end = '...')); ?>

                                                    </div>
                                                    </a>
                                                </li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </div>
                                    <div id="notificationFooter"><a href="<?php echo e(route('deleteNotification')); ?>" title="<?php echo e(__('Clear All')); ?>"><?php echo e(__('Clear All')); ?></a></div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-2 col-2">
                        <div class="nav-wishlist">
                            <a href="<?php echo e(route('wishlist.show')); ?>" title="Go to Wishlist"><i data-feather="heart"></i></a>
                            <span class="red-menu-badge red-bg-success">
                                <?php
                                    $wishlist = App\Wishlist::where('user_id', Auth::User()->id)->get();
                                    
                                ?>                             

                                <?php
                                    $counter = 0;
                                    foreach ($wishlist as $item) {
                                         if($item->courses->status == '1'){

                                              
                                          $counter++;
       
                                         }
                                    }

                                    echo  $counter; 
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-2 col-2">
                        <div class="shopping-cart">
                            <a href="<?php echo e(route('cart.show')); ?>" title="<?php echo e(__('Cart')); ?>"><i data-feather="shopping-cart"></i></a>
                            <span class="red-menu-badge red-bg-success">
                                <?php
                                    $item = App\Cart::where('user_id', Auth::User()->id)->count();
                                    if($item>0){
                                        echo $item;
                                    }
                                    else{
                                        echo "0";
                                    }
                                ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-2 col-2">
                        <div class="search search-one" id="search">
                            <form method="GET" id="searchform" action="<?php echo e(route('search')); ?>">
                              <div class="search-input-wrap">
                                <input class="search-input" name="searchTerm" placeholder="<?php echo e(__('Search In Site')); ?>" type="text" id="course_name" autocomplete="off" />
                              </div>
                              <input class="search-submit" type="submit" id="go" value="">
                              <div class="icon"><i data-feather="search"></i></div>
                              <div id="course_data"></div>
                            </form>
                        </div>                       
                    </div>
                            <?php
                                $menus = App\Menu::get();
                                $pages = App\Page::get();
                            ?>
                                <?php if(isset($menus) && $menus->isNotEmpty() && $pages->isNotEmpty()): ?>
                                <div class="col-lg-1">
                            <div class="navigation navigation-one">
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                        <i data-feather="align-justify"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($menu->position_menu == 'top'): ?>
                                        <?php if($menu->link_by == 'url'): ?>
                                        <li><a class="" href="<?php echo e($menu->url); ?>" title="<?php echo e($menu->title); ?>"><?php echo e($menu->title); ?></a></li>
                                        <?php endif; ?>
                                        <?php if($menu->link_by == 'page'): ?>
                                        <li><a class="" href="<?php echo e(route('page.show', $menu->page->slug)); ?>" title="<?php echo e($menu->title); ?>"><?php echo e($menu->title); ?></a></li>
                                        <?php endif; ?>
                                        <?php endif; ?>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>

                            </div>
                    </div>
                    <?php endif; ?>
                    <?php
                    $user = \Auth::user(); 
                    $roles = $user->getRoleNames();
                    $test_id = Spatie\Permission\Models\Role::select('id')->where('name',$roles[0])->get();
                    $dropdown = App\Dropdown::where('role_id', $test_id[0]['id'])->get();
                    ?>
                    <?php if($roles[0] != "admin" &&  $roles[0] != "instructor" && $roles[0] != "user"): ?>
                    <?php $__currentLoopData = $dropdown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $drop): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-2 col-md-3 col-sm-6 col-6">
                        <div class="my-container">
                          <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle  my-dropdown" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                <?php if(Auth::User()['user_img'] != null && Auth::User()['user_img'] !='' && @file_get_contents('images/user_img/'.Auth::user()['user_img'])): ?>
                                  <img src="<?php echo e(url('/images/user_img/'.Auth::User()->user_img)); ?>" class="circle" alt="<?php echo e(Auth::User()->fname); ?>">
                                <?php else: ?>
                                    <img src="<?php echo e(asset('images/default/user.jpg')); ?>"  class="circle" alt="<?php echo e(Auth::User()->fname); ?>">
                                <?php endif; ?>
                                <span class="dropdown__item name" id="name"><?php echo e(str_limit(Auth::User()->fname, $limit = 10, $end = '..')); ?></span>
                                <span class="dropdown__item caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right User-Dropdown U-open" aria-labelledby="dropdownMenu1">
                                <div id="notificationTitle">
                                     <?php if(Auth::User()['user_img'] != null && Auth::User()['user_img'] !='' && @file_get_contents('images/user_img/'.Auth::user()['user_img'])): ?>
                                      <img src="<?php echo e(url('/images/user_img/'.Auth::User()->user_img)); ?>" class="dropdown-user-circle" alt="<?php echo e(Auth::User()->fname); ?>">
                                    <?php else: ?>
                                        <img src="<?php echo e(asset('images/default/user.jpg')); ?>" class="dropdown-user-circle" alt="<?php echo e(Auth::User()->fname); ?>">
                                    <?php endif; ?>
                                    <div class="user-detailss">
                                        <?php echo e(Auth::User()->fname); ?>

                                        <br>
                                        <?php echo e(Auth::User()->email); ?>

                                    </div>                                    
                                </div>
                                <div class="scroll-down">
                                <?php if(Auth::User()->role == "admin" ): ?>                               
                                <a target="_blank" href="<?php echo e(url('/admins')); ?>" title="<?php echo e(__('Admin Dashboard')); ?>"><li><i data-feather="pie-chart"></i><?php echo e(__('Admin Dashboard')); ?></li></a>
                                <?php endif; ?>
                                <?php if(Auth::User()->role == "instructor"): ?>
                                <a target="_blank" href="<?php echo e(url('/instructor')); ?>" title="<?php echo e(__('Instructor Dashboard')); ?>"><li><i data-feather="pie-chart"></i><?php echo e(__('Instructor Dashboard')); ?></li></a>
                                <?php endif; ?>

                              
                                <?php if($drop->my_courses == '1'): ?>
                                <a href="<?php echo e(route('mycourse.show')); ?>" title="<?php echo e(__('My Courses')); ?>"><li><i data-feather="book-open"></i><?php echo e(__('My Courses')); ?></li></a>
                                <?php endif; ?>
                                <?php if($drop->my_wishlist == '1'): ?>
                                <a href="<?php echo e(route('wishlist.show')); ?>" title="<?php echo e(__('Wishlist')); ?>"><li><i data-feather="heart"></i><?php echo e(__('Wishlist')); ?></li></a>
                                <?php endif; ?>
                                <?php if($drop->purchased_history == '1'): ?>
                                <a href="<?php echo e(route('purchase.show')); ?>" title="<?php echo e(__('Purchased History')); ?>"><li><i data-feather="shopping-cart"></i><?php echo e(__('Purchased History')); ?></li></a>
                                <?php endif; ?>
                                <?php if($drop->my_profile == '1'): ?>
                                <a href="<?php echo e(route('profile.show',Auth::User()->id)); ?>" title="<?php echo e(__('Profile')); ?>"><li><i data-feather="user"></i><?php echo e(__('Profile')); ?></li></a>
                                <?php endif; ?>
                                <?php if(Auth::User()->role == "user"): ?>
                                <?php if($gsetting->instructor_enable == 1): ?>
                                <a href="#" data-toggle="modal" data-target="#myModalinstructor"  title="<?php echo e(__('Become An Instructor')); ?>"><li><i data-feather="shield"></i><?php echo e(__('Become An Instructor')); ?></li></a>
                                <?php endif; ?>
                        
                                <?php endif; ?>
                                <?php if($drop->flash_deal == '1'): ?>
                                <a href="<?php echo e(route('flash.deals')); ?>" title="<?php echo e(__('Flash Deal')); ?>"><li><i data-feather="battery-charging"></i><?php echo e(__('Flash Deals')); ?></li></a>
                                <?php endif; ?>
                                <?php if(env('ENABLE_INSTRUCTOR_SUBS_SYSTEM') == 1): ?>
                                <?php if(Auth::User()->role == "instructor"): ?>
                                <a href="<?php echo e(route('plan.page')); ?>" title="<?php echo e(__('Instructor Plan')); ?>"><li><i data-feather="tag"></i><?php echo e(__('Instructor Plan')); ?></li></a>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if(Auth::User()->role == "user" || Auth::User()->role == "instructor"): ?>
                                <?php if($gsetting->device_control == 1): ?>
                                <a href="<?php echo e(route('active.courses')); ?>" title="<?php echo e(__('Watchlist')); ?>"><li><i data-feather="framer"></i><?php echo e(__('Watchlist')); ?></li></a>
                                <?php endif; ?>
                                <?php endif; ?>                                
                                <?php if($gsetting->donation_enable == 1 && $drop->donation == '1'): ?>
                                <a target="__blank" href="<?php echo e($gsetting->donation_link); ?>" title="<?php echo e(__('Donation')); ?>"><li><i data-feather="framer"></i><?php echo e(__('Donation')); ?></li></a>
                                <?php endif; ?>
                                <?php if($gsetting->affilate == 1 && $drop->my_wallet == '1'): ?>
                                <?php if(Schema::hasTable('affiliate') && Schema::hasTable('wallet_settings')): ?>
                                <?php endif; ?>
                                <?php if(isset($wallet_settings) && $wallet_settings->status == 1): ?>
                                <a href="<?php echo e(url('/wallet')); ?>" title="<?php echo e(__('Wallet')); ?>"><li><i class="icon-wallet icons"></i><?php echo e(__('Wallet')); ?></li></a>
                                <?php endif; ?>
                                <?php if(isset($affiliate) && $affiliate->status == 1): ?>
                                <a href="<?php echo e(route('get.affiliate')); ?>" title="<?php echo e(__('Affiliate')); ?>"><li><i data-feather="users"></i><?php echo e(__('Affiliate')); ?></li></a>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if($drop->compare == '1'): ?>
                                <a href="<?php echo e(route('compare.index')); ?>" title="<?php echo e(__('Compare')); ?>"><li><i data-feather="bar-chart"></i><?php echo e(__("Compare")); ?></li></a>
                                <?php endif; ?>
                                <?php if($drop->search_job == '1'): ?>
                                <?php if(Module::has('Resume') && Module::find('Resume')->isEnabled()): ?>
                                    <?php echo $__env->make('resume::front.searchresume', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if($drop->job_portal == '1'): ?>
                                <?php if(Module::has('Resume') && Module::find('Resume')->isEnabled()): ?>
                                    <?php echo $__env->make('resume::front.job.icon', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if($drop->form_enable == '1'): ?>
                                <?php if(Module::find('Forum') && Module::find('Forum')->isEnabled()): ?>
                                    <?php if($gsetting->forum_enable == 1): ?>
                                        <?php echo $__env->make('forum::layouts.sidebar_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if($drop->my_leadership == '1'): ?>
                                <a href="<?php echo e(route('my.leaderboard')); ?>" title="<?php echo e(__('leader Board')); ?>"><li><i class="icon-chart icons"></i><?php echo e(__('Leader Board')); ?></li></a>
                                <?php endif; ?>
                                <?php if(Auth::User()->role == "user"): ?>
                                <a href="<?php echo e(route('studentprofile')); ?>" title="<?php echo e(__('Share Profile')); ?>"><li><i data-feather="share"></i><?php echo e(__('Share Profile')); ?></li></a>
                                <?php endif; ?>
                                <?php if($drop->affilate_dashboard == '1'): ?>
                                <a href="<?php echo e(route('affilate.report')); ?>"><li><i data-feather="users"></i><?php echo e(__('Affiliate Dashboard')); ?></li></a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('batch.front')); ?>" title="<?php echo e(__('Batch')); ?>"><li><i data-feather="book-open"></i><?php echo e(__('Batch')); ?></li></a>
                                </div>
                                <?php if(Module::has('Ebook') && Module::find('Ebook')->isEnabled()): ?>
                                <a href="<?php echo e(route('web.ebook.confirm-order')); ?>" title="<?php echo e(__('My eBook')); ?>"><li><i data-feather="book-open"></i><?php echo e(__('My eBook')); ?></li></a>
                                <?php endif; ?> 
                                <a href="<?php echo e(route('logout')); ?>" title="<?php echo e(__('Logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <div id="notificationFooter">
                                        <?php echo e(__('Logout')); ?>                                        
                                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="display-none">
                                            <?php echo csrf_field(); ?>
                                        </form>
                                    </div>
                                </a>
                            </ul>
                          </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                    <div class="col-lg-2 col-md-3 col-sm-6 col-6">
                        <div class="my-container">
                          <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle  my-dropdown" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                 <?php if(Auth::User()['user_img'] != null && Auth::User()['user_img'] !='' && @file_get_contents('images/user_img/'.Auth::user()['user_img'])): ?>
                                  <img src="<?php echo e(url('/images/user_img/'.Auth::User()->user_img)); ?>" class="circle" alt="<?php echo e(Auth::User()->fname); ?>">
                                <?php else: ?>
                                    <img src="<?php echo e(asset('images/default/user.jpg')); ?>"  class="circle" alt="<?php echo e(Auth::User()->fname); ?>">
                                <?php endif; ?>
                                <span class="dropdown__item name" id="name"><?php echo e(str_limit(Auth::User()->fname, $limit = 10, $end = '..')); ?></span>
                                <span class="dropdown__item caret"></span>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-right User-Dropdown U-open" aria-labelledby="dropdownMenu1">
                                <div id="notificationTitle">
                                     <?php if(Auth::User()['user_img'] != null && Auth::User()['user_img'] !='' && @file_get_contents('images/user_img/'.Auth::user()['user_img'])): ?>
                                      <img src="<?php echo e(url('/images/user_img/'.Auth::User()->user_img)); ?>" class="dropdown-user-circle" alt="<?php echo e(Auth::User()->fname); ?>">
                                    <?php else: ?>
                                        <img src="<?php echo e(asset('images/default/user.jpg')); ?>" class="dropdown-user-circle" alt="<?php echo e(Auth::User()->fname); ?>">
                                    <?php endif; ?>
                                    <div class="user-detailss">
                                        <?php echo e(Auth::User()->fname); ?>

                                        <br>
                                        <?php echo e(Auth::User()->email); ?>

                                    </div>
                                    
                                </div>

                                <div class="scroll-down">
                                <?php if(Auth::User()->role == "admin" ): ?>                               
                                <a target="_blank" href="<?php echo e(url('/admins')); ?>" title="<?php echo e(__('Admin Dashboard')); ?>"><li><i data-feather="pie-chart"></i><?php echo e(__('Admin Dashboard')); ?></li></a>
                                <?php endif; ?>
                                <?php if(Auth::User()->role == "instructor"): ?>
                                <a target="_blank" href="<?php echo e(url('/instructor')); ?>" title="<?php echo e(__('Instructor Dashboard')); ?>"><li><i data-feather="pie-chart"></i><?php echo e(__('Instructor Dashboard')); ?></li></a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('mycourse.show')); ?>" title="<?php echo e(__('My Courses')); ?>"><li><i data-feather="book-open"></i><?php echo e(__('My Courses')); ?></li></a>
                                <a href="<?php echo e(route('wishlist.show')); ?>" title="<?php echo e(__('Wishlist')); ?>"><li><i data-feather="heart"></i><?php echo e(__('Wishlist')); ?></li></a>
                                <a href="<?php echo e(route('purchase.show')); ?>" title="<?php echo e(__('Purchased History')); ?>"><li><i data-feather="shopping-cart"></i><?php echo e(__('Purchased History')); ?></li></a>
                                <a href="<?php echo e(route('profile.show',Auth::User()->id)); ?>" title="<?php echo e(__('Profile')); ?>"><li><i data-feather="user"></i><?php echo e(__('Profile')); ?></li></a>
                                <?php if(Auth::User()->role == "user"): ?>
                                <?php if($gsetting->instructor_enable == 1): ?>
                                <a href="#" data-toggle="modal" data-target="#myModalinstructor" title="<?php echo e(__('Become An Instructor')); ?>"><li><i data-feather="shield"></i><?php echo e(__('Become An Instructor')); ?></li></a>
                                <?php endif; ?>                        
                                <?php endif; ?>
                                <a href="<?php echo e(route('flash.deals')); ?>" title="<?php echo e(__('Flash Deals')); ?>"><li><i data-feather="battery-charging"></i><?php echo e(__('Flash Deals')); ?></li></a>
                                <?php if(env('ENABLE_INSTRUCTOR_SUBS_SYSTEM') == 1): ?>
                                <?php if(Auth::User()->role == "instructor"): ?>
                                <a href="<?php echo e(route('plan.page')); ?>" title="<?php echo e(__('Instructor Plan')); ?>"><li><i data-feather="tag"></i><?php echo e(__('Instructor Plan')); ?></li></a>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if(Auth::User()->role == "user" || Auth::User()->role == "instructor"): ?>
                                <?php if($gsetting->device_control == 1): ?>
                                <a href="<?php echo e(route('active.courses')); ?>" title="<?php echo e(__('Watchlist')); ?>"><li><i data-feather="framer"></i><?php echo e(__('Watchlist')); ?></li></a>
                                <?php endif; ?>
                                <?php endif; ?>
                                <?php if($gsetting->donation_enable == 1): ?>
                                <a target="__blank" href="<?php echo e($gsetting->donation_link); ?>" title="<?php echo e(__('Donation')); ?>"><li><i data-feather="framer"></i><?php echo e(__('Donation')); ?></li></a>
                                <?php endif; ?>
                                <?php if(Schema::hasTable('affiliate') && Schema::hasTable('wallet_settings')): ?>
                                <?php if(isset($wallet_settings) && $wallet_settings->status == 1): ?>
                                <a href="<?php echo e(url('/wallet')); ?>" title="<?php echo e(__('Wallet')); ?>"><li><i class="icon-wallet icons"></i><?php echo e(__('Wallet')); ?></li></a>
                                <?php endif; ?>
                                <?php if(isset($affiliate) && $affiliate->status == 1): ?>
                                <a href="<?php echo e(route('get.affiliate')); ?>" title="<?php echo e(__('Affiliate')); ?>"><li><i data-feather="users"></i><?php echo e(__('Affiliate')); ?></li></a>
                                <?php endif; ?>
                                <?php endif; ?>
                                <a href="<?php echo e(route('compare.index')); ?>" title="<?php echo e(__('Compare')); ?>"><li><i data-feather="bar-chart"></i><?php echo e(__("Compare")); ?></li></a>
                                <?php if(Module::has('Resume') && Module::find('Resume')->isEnabled()): ?>
                                    <?php echo $__env->make('resume::front.searchresume', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <?php endif; ?>
                                <?php if(Module::has('Resume') && Module::find('Resume')->isEnabled()): ?>
                                    <?php echo $__env->make('resume::front.job.icon', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <?php endif; ?>                               
                                <?php if(Module::find('Forum') && Module::find('Forum')->isEnabled()): ?>
                                    <?php if($gsetting->forum_enable == 1): ?>
                                        <?php echo $__env->make('forum::layouts.sidebar_menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <a href="<?php echo e(route('my.leaderboard')); ?>" title="<?php echo e(__('Leader Board')); ?>"><li><i class="icon-chart icons"></i><?php echo e(__('Leader Board')); ?></li></a>
                                <?php if(Auth::User()->role == "user"): ?>
                                <a href="<?php echo e(route('studentprofile')); ?>" title="<?php echo e(__('Share Profile')); ?>"><li><i data-feather="share"></i><?php echo e(__('Share Profile')); ?></li></a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('affilate.report')); ?>" title="<?php echo e(__('Affiliate Dashboard')); ?>"><li><i data-feather="users"></i><?php echo e(__('Affiliate Dashboard')); ?></li></a>
                                <a href="<?php echo e(route('batch.front')); ?>" title="<?php echo e(__('Batch')); ?>"><li><i data-feather="book-open"></i><?php echo e(__('Batch')); ?></li></a>
                                <?php if(Module::has('Ebook') && Module::find('Ebook')->isEnabled()): ?>
                                <a href="<?php echo e(route('web.ebook.confirm-order')); ?>" title="<?php echo e(__('My eBook')); ?>"><li><i data-feather="book-open"></i><?php echo e(__('My eBook')); ?></li></a>
                                <?php endif; ?> 
                                </div>
                                <a href="<?php echo e(route('logout')); ?>" title="<?php echo e(__('Logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <div id="notificationFooter">
                                        <?php echo e(__('Logout')); ?>                                        
                                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" class="display-none">
                                            <?php echo csrf_field(); ?>
                                        </form>
                                    </div>
                                </a>
                            </ul>
                          </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>        
    </div>
</section>
<!-- start search -->
<div id="find" class="small-screen-navigation">
    <button type="button" class="close" title="<?php echo e(__('Close')); ?>">×</button>
     <form action="<?php echo e(route('search')); ?>" class="form-inline search-form" method="GET">
         <input type="find" name="searchTerm" class="form-control" id="search"  placeholder="<?php echo e(__('Searchforcourses')); ?>" value="<?php echo e(isset($searchTerm) ? $searchTerm : ''); ?>">
         <button type="submit" class="btn btn-outline-info btn_sm" title="<?php echo e(__('Search')); ?>"><?php echo e(__('Search')); ?></button> 
     </form>
</div>
<!-- start end -->
<!-- side navigation  -->
<script>
function openNav() {
  document.getElementById("mySidenav").style.width = "250px";
}
function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}
</script>
<?php echo $__env->make('instructormodel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/theme/nav.blade.php ENDPATH**/ ?>