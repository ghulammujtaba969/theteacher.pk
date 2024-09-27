<footer id="footer" class="footer-main-block">
    <!-- Newsletter -->
    <?php if($hsetting->newsletter_enable == 1): ?>
    <section id="newsletter" class="newsletter-main-block">
        <div class="container-xl">
            <div class="newsletter-block">
                <div class="row">
                    <div class="col-lg-6 col-md-5">
                        <h1 class="newsletter-heading"><?php echo e(__('Join our mailing list')); ?></h1>
                    </div>
                    <div class="col-lg-6 col-md-7">
                        <form method="post" action="<?php echo e(url('store-newsletter')); ?>">
                            <?php echo csrf_field(); ?>
                            <input type="email" required placeholder="Enter your email address" name=subscribed_email>
                        <button type="submit" class="btn btn-primary" alt="<?php echo e(__('Subscribe')); ?>"><?php echo e(__('Subscribe')); ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <div class="container-xl">
        <div class="footer-block">
            <div class="row">
                <?php
                    $widgets = App\WidgetSetting::first();
                ?>
                <div class="col-lg-4 col-md-4 col-12">                   
                    <div class="footer-logo"> <!-- Foote Logo -->
                        <?php if($gsetting->logo_type == 'L'): ?>
                            <?php if($gsetting->footer_logo != NULL): ?>
                            <a href="<?php echo e(url('/')); ?>" title="<?php echo e($gsetting->project_title); ?>"><img src="<?php echo e(asset('images/logo/'.$gsetting->footer_logo)); ?>" alt="<?php echo e($gsetting->project_title); ?>" class="img-fluid" ></a>
                            <?php endif; ?>;
                        <?php else: ?>
                            <a href="<?php echo e(url('/')); ?>"><b><?php echo e($gsetting->project_title); ?></b></a>
                        <?php endif; ?>
                    </div>
                    <!-- App Download -->
                    <div class="mobile-btn">
                        <?php if($gsetting->play_download == '1'): ?>
                            <a href="<?php echo e($gsetting->play_link); ?>" title="<?php echo e(__('Google Play')); ?>" target="_blank"><img src="<?php echo e(url('images/icons/download-google-play.png')); ?>" alt="<?php echo e(__('Google Play')); ?>"></a>
                        <?php endif; ?>
                        <?php if($gsetting->app_download == '1'): ?>
                            <a href="<?php echo e($gsetting->app_link); ?>" title="<?php echo e(__('iOS')); ?>" target="_blank"><img src="<?php echo e(url('images/icons/app-download-ios.png')); ?>" alt="<?php echo e(__('iOS')); ?>"></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if(isset($widgets) && $widgets->widget_enable == 1): ?>
                <div class="col-lg-2 col-md-2 col-4">          
                     <!-- Widget One -->         
                    <div class="widget"><b><?php echo e($widgets->widget_one); ?></b></div>
                    <div class="footer-link">                        <ul>
                            <?php if($gsetting->instructor_enable == 1): ?>
                                <?php if(Auth::check()): ?>
                                    <?php if(Auth::User()->role == "user"): ?>
                                    <li><a href="#" data-toggle="modal" data-target="#myModalinstructor" title="<?php echo e(__('Become An Instructor')); ?>"><?php echo e(__('Become An Instructor')); ?></a></li>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <li><a href="<?php echo e(route('login')); ?>" title="<?php echo e(__('Become An Instructor')); ?>"><?php echo e(__('Become An Instructor')); ?></a></li>
                                <?php endif; ?>
                            <?php endif; ?>
                            <?php if(isset($widgets) && $widgets->about_enable == 1): ?>
                            <li><a href="<?php echo e(route('about.show')); ?>" title="<?php echo e(__('About Us')); ?>"><?php echo e(__('About Us')); ?></a></li>
                            <?php endif; ?>
                            
                            <?php if(isset($widgets) && $widgets->contact_enable == 1): ?>
                            <li><a href="<?php echo e(url('user_contact')); ?>" title="<?php echo e(__('Contact Us')); ?>"><?php echo e(__('Contact Us')); ?></a></li>
                            <?php endif; ?>
                            <li><a href="<?php echo e(route('front.service')); ?>" title="<?php echo e(__('Our Services')); ?>" target="_blank"><?php echo e(__('Our Services')); ?></a></li>
                            <li><a href="<?php echo e(route('front.feature')); ?>" title="<?php echo e(__('Our Feature')); ?>" target="_blank"><?php echo e(__('Our Feature')); ?></a></li>
                            <li><a href="<?php echo e(route('footer.alumini')); ?>" title="<?php echo e(__('Our Alumni')); ?>" target="_blank"><?php echo e(__('Alumni')); ?></a></li>
                            <?php
                            $menus = App\Menu::get();
                            $pages = App\Page::get();
                            ?>
                            <li>
                                <ul>
                                    <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($menu->footer == 'widget2'): ?>
                                    <?php if($menu->link_by == 'url'): ?>
                                    <li><a href="<?php echo e($menu->url); ?>" title="<?php echo e($menu->title); ?>"><?php echo e($menu->title); ?></a></li>
                                    <?php endif; ?>
                                    <?php if($menu->link_by == 'page'): ?>
                                    <li><a href="<?php echo e(route('page.show', $menu->page->slug)); ?>" title="<?php echo e($menu->title); ?>"><?php echo e($menu->title); ?></a></li>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-4"> <!-- Widget Two -->
                    <div class="widget"><b><?php echo e($widgets->widget_two); ?></b></div>
                    <div class="footer-link">
                        <ul>
                            <?php if(isset($widgets) && $widgets->career_enable == 1): ?>
                            <li><a href="<?php echo e(route('careers.show')); ?>" title="<?php echo e(__('Careers')); ?>"><?php echo e(__('Careers')); ?></a></li>
                            <?php endif; ?>
                            <?php if(isset($widgets) && $widgets->blog_enable == 1): ?>
                            <li><a href="<?php echo e(route('blog.all')); ?>" title="<?php echo e(__('Blog')); ?>"><?php echo e(__('Blog')); ?></a></li>
                            <?php endif; ?>
                            <?php if(isset($widgets) && $widgets->help_enable == 1): ?>
                            <li><a href="<?php echo e(route('help.show')); ?>" title="<?php echo e(__('Help & Support')); ?>"><?php echo e(__('Help & Support')); ?></a></li>
                            <?php endif; ?>
                            <?php
                            $menus = App\Menu::get();
                            $pages = App\Page::get();
                            ?>
                            <li>
                                <ul>
                                    <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($menu->footer == 'widget3'): ?>
                                    <?php if($menu->link_by == 'url'): ?>
                                    <li><a href="<?php echo e($menu->url); ?>" title="<?php echo e($menu->title); ?>"><?php echo e($menu->title); ?></a></li>
                                    <?php endif; ?>
                                    <?php if($menu->link_by == 'page'): ?>
                                    <li><a href="<?php echo e(route('page.show', $menu->page->slug)); ?>" title="<?php echo e($menu->title); ?>"><?php echo e($menu->title); ?></a></li>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-4"> <!-- Widget Three -->
                    <div class="widget"><b><?php echo e($widgets->widget_three); ?></b></div>
                    <div class="footer-link">
                        <ul>                            
                            <?php
                                $pages = App\Page::get();
                            ?>
                            
                            <?php if(isset($pages)): ?>
                            <?php $__currentLoopData = $pages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if($page->status == 1): ?>
                                <li><a href="<?php echo e(route('page.show', $page->slug)); ?>" title="<?php echo e($page->title); ?>"><?php echo e($page->title); ?></a></li>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            <?php
                            $menus = App\Menu::get();
                            $pages = App\Page::get();
                            ?>
                            <li>
                                <ul>
                                    <?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($menu->footer == 'widget4'): ?>
                                    <?php if($menu->link_by == 'url'): ?>
                                    <li><a href="<?php echo e($menu->url); ?>"><?php echo e($menu->title); ?></a></li>
                                    <?php endif; ?>
                                    <?php if($menu->link_by == 'page'): ?>
                                    <li><a href="<?php echo e(route('page.show', $menu->page->slug)); ?>"><?php echo e($menu->title); ?></a></li>
                                    <?php endif; ?>
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
                <div class="col-lg-2 col-md-2"> <!-- Language -->
                    <?php
                        $languages = App\Language::get(); 
                    ?>
                    <?php if(isset($languages) && count($languages) > 0): ?>
                    <div class="footer-dropdown">
                        <a href="#" class="a" data-toggle="dropdown" title="<?php echo e(__('Language')); ?>"><i data-feather="globe"></i><?php echo e(Session::has('changed_language') ? ucfirst(Session::get('changed_language')) : ''); ?><i class="fa fa-angle-up lft-10"></i></a>   
                        <ul class="dropdown-menu">                          
                            <?php $__currentLoopData = $languages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $language): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('languageSwitch', $language->local)); ?>" title="<?php echo e($language->name); ?>"><li><?php echo e($language->name); ?></li></a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                    <?php
                        $currencies = DB::table('currencies')->get(); 
                    ?>
                    <?php if(isset($currencies) && count($currencies) > 0): ?>
                    <div class="footer-dropdown footer-dropdown-two">  <!-- Currency -->
                        <a href="#" class="a" data-toggle="dropdown" title="<?php echo e(__('Currency')); ?>"><i class="icon-wallet icons mr-2"></i> <?php echo e(Session::has('changed_currency') ? ucfirst(Session::get('changed_currency')) : $currency->code); ?><i class="fa fa-angle-up lft-10"></i></a>  
                        <ul class="dropdown-menu">                          
                            <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('CurrencySwitch', $currency->code)); ?>" title="<?php echo e($currency->code); ?>"><li><?php echo e($currency->code); ?></li></a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>    
            </div>
        </div>
    </div>
    <hr>
    <div class="tiny-footer"> <!-- Tiny Footer -->
        <div class="container-xl">
            <div class="row">
                <div class="col-md-6">
                    <div class="logo-footer">
                        <ul>
                            <li><?php echo e($gsetting->cpy_txt); ?></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="copyright-social">
                        <ul>                           
                            <li>
                                <?php if(isset($terms->terms) && $terms->terms != NULL && $terms->terms != ''): ?>
                                <a href="<?php echo e(url('terms_condition')); ?>" title="<?php echo e(__('Terms & Condition')); ?>"><?php echo e(__('Terms & Condition')); ?></a>
                                <?php endif; ?>
                            </li> 
                            <li>
                                <?php if(isset($terms->policy) && $terms->policy != NULL && $terms->policy != ''): ?>
                                <a href="<?php echo e(url('privacy_policy')); ?>" title="<?php echo e(__('Privacy Policy')); ?>"><?php echo e(__('Privacy Policy')); ?></a>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<?php echo $__env->make('instructormodel', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\eclass_purchasecopy\resources\views/theme/footer.blade.php ENDPATH**/ ?>