<footer id="footer" class="footer-main-block">
    <!-- Newsletter -->
    @if($hsetting->newsletter_enable == 1)
    <section id="newsletter" class="newsletter-main-block">
        <div class="container-xl">
            <div class="newsletter-block">
                <div class="row">
                    <div class="col-lg-6 col-md-5">
                        <h1 class="newsletter-heading">{{ __('Join our mailing list') }}</h1>
                    </div>
                    <div class="col-lg-6 col-md-7">
                        <form method="post" action="{{url('store-newsletter')}}">
                            @csrf
                            <input type="email" required placeholder="Enter your email address" name=subscribed_email>
                        <button type="submit" class="btn btn-primary" alt="{{ __('Subscribe') }}">{{ __('Subscribe') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif
    <div class="container-xl">
        <div class="footer-block">
            <div class="row">
                @php
                    $widgets = App\WidgetSetting::first();
                @endphp
                <div class="col-lg-4 col-md-4 col-12">                   
                    <div class="footer-logo"> <!-- Foote Logo -->
                        @if($gsetting->logo_type == 'L')
                            @if($gsetting->footer_logo != NULL)
                            <a href="{{ url('/') }}" title="{{ $gsetting->project_title }}"><img src="{{ asset('images/logo/'.$gsetting->footer_logo) }}" alt="{{ $gsetting->project_title }}" class="img-fluid" ></a>
                            @endif;
                        @else()
                            <a href="{{ url('/') }}"><b>{{ $gsetting->project_title }}</b></a>
                        @endif
                    </div>
                    <!-- App Download -->
                    <div class="mobile-btn">
                        @if($gsetting->play_download == '1')
                            <a href="{{ $gsetting->play_link }}" title="{{ __('Google Play')}}" target="_blank"><img src="{{ url('images/icons/download-google-play.png') }}" alt="{{ __('Google Play')}}"></a>
                        @endif
                        @if($gsetting->app_download == '1')
                            <a href="{{ $gsetting->app_link }}" title="{{ __('iOS')}}" target="_blank"><img src="{{ url('images/icons/app-download-ios.png') }}" alt="{{ __('iOS')}}"></a>
                        @endif
                    </div>
                </div>
                @if(isset($widgets) && $widgets->widget_enable == 1)
                <div class="col-lg-2 col-md-2 col-4">          
                     <!-- Widget One -->         
                    <div class="widget"><b>{{ $widgets->widget_one }}</b></div>
                    <div class="footer-link">                        <ul>
                            @if($gsetting->instructor_enable == 1)
                                @if(Auth::check())
                                    @if(Auth::User()->role == "user")
                                    <li><a href="#" data-toggle="modal" data-target="#myModalinstructor" title="{{ __('Become An Instructor')}}">{{ __('Become An Instructor') }}</a></li>
                                    @endif
                                @else
                                    <li><a href="{{ route('login') }}" title="{{ __('Become An Instructor') }}">{{ __('Become An Instructor') }}</a></li>
                                @endif
                            @endif
                            @if(isset($widgets) && $widgets->about_enable == 1)
                            <li><a href="{{ route('about.show') }}" title="{{ __('About Us') }}">{{ __('About Us') }}</a></li>
                            @endif
                            
                            @if(isset($widgets) && $widgets->contact_enable == 1)
                            <li><a href="{{url('user_contact')}}" title="{{ __('Contact Us') }}">{{ __('Contact Us') }}</a></li>
                            @endif
                            <li><a href="{{ route('front.service') }}" title="{{ __('Our Services') }}" target="_blank">{{ __('Our Services') }}</a></li>
                            <li><a href="{{ route('front.feature') }}" title="{{ __('Our Feature') }}" target="_blank">{{ __('Our Feature') }}</a></li>
                            <li><a href="{{ route('footer.alumini') }}" title="{{ __('Our Alumni') }}" target="_blank">{{ __('Alumni') }}</a></li>
                            @php
                            $menus = App\Menu::get();
                            $pages = App\Page::get();
                            @endphp
                            <li>
                                <ul>
                                    @foreach($menus as $menu)
                                    @if($menu->footer == 'widget2')
                                    @if($menu->link_by == 'url')
                                    <li><a href="{{ $menu->url }}" title="{{ $menu->title }}">{{ $menu->title }}</a></li>
                                    @endif
                                    @if($menu->link_by == 'page')
                                    <li><a href="{{ route('page.show', $menu->page->slug) }}" title="{{ $menu->title }}">{{ $menu->title }}</a></li>
                                    @endif
                                    @endif
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-4"> <!-- Widget Two -->
                    <div class="widget"><b>{{ $widgets->widget_two }}</b></div>
                    <div class="footer-link">
                        <ul>
                            @if(isset($widgets) && $widgets->career_enable == 1)
                            <li><a href="{{ route('careers.show') }}" title="{{ __('Careers') }}">{{ __('Careers') }}</a></li>
                            @endif
                            @if(isset($widgets) && $widgets->blog_enable == 1)
                            <li><a href="{{ route('blog.all') }}" title="{{ __('Blog') }}">{{ __('Blog') }}</a></li>
                            @endif
                            @if(isset($widgets) && $widgets->help_enable == 1)
                            <li><a href="{{ route('help.show') }}" title="{{ __('Help & Support') }}">{{ __('Help & Support') }}</a></li>
                            @endif
                            @php
                            $menus = App\Menu::get();
                            $pages = App\Page::get();
                            @endphp
                            <li>
                                <ul>
                                    @foreach($menus as $menu)
                                    @if($menu->footer == 'widget3')
                                    @if($menu->link_by == 'url')
                                    <li><a href="{{ $menu->url }}" title="{{ $menu->title }}">{{ $menu->title }}</a></li>
                                    @endif
                                    @if($menu->link_by == 'page')
                                    <li><a href="{{ route('page.show', $menu->page->slug) }}" title="{{ $menu->title }}">{{ $menu->title }}</a></li>
                                    @endif
                                    @endif
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-4"> <!-- Widget Three -->
                    <div class="widget"><b>{{ $widgets->widget_three }}</b></div>
                    <div class="footer-link">
                        <ul>                            
                            @php
                                $pages = App\Page::get();
                            @endphp
                            
                            @if(isset($pages))
                            @foreach($pages as $page)
                                @if($page->status == 1)
                                <li><a href="{{ route('page.show', $page->slug) }}" title="{{ $page->title }}">{{ $page->title }}</a></li>
                                @endif
                            @endforeach
                            @endif
                            @php
                            $menus = App\Menu::get();
                            $pages = App\Page::get();
                            @endphp
                            <li>
                                <ul>
                                    @foreach($menus as $menu)
                                    @if($menu->footer == 'widget4')
                                    @if($menu->link_by == 'url')
                                    <li><a href="{{ $menu->url }}">{{ $menu->title }}</a></li>
                                    @endif
                                    @if($menu->link_by == 'page')
                                    <li><a href="{{ route('page.show', $menu->page->slug) }}">{{ $menu->title }}</a></li>
                                    @endif
                                    @endif
                                    @endforeach
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
                @endif
                <div class="col-lg-2 col-md-2"> <!-- Language -->
                    @php
                        $languages = App\Language::get(); 
                    @endphp
                    @if(isset($languages) && count($languages) > 0)
                    <div class="footer-dropdown">
                        <a href="#" class="a" data-toggle="dropdown" title="{{ __('Language')}}"><i data-feather="globe"></i>{{Session::has('changed_language') ? ucfirst(Session::get('changed_language')) : ''}}<i class="fa fa-angle-up lft-10"></i></a>   
                        <ul class="dropdown-menu">                          
                            @foreach($languages as $language)
                            <a href="{{ route('languageSwitch', $language->local) }}" title="{{$language->name}}"><li>{{$language->name}}</li></a>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    @php
                        $currencies = DB::table('currencies')->get(); 
                    @endphp
                    @if(isset($currencies) && count($currencies) > 0)
                    <div class="footer-dropdown footer-dropdown-two">  <!-- Currency -->
                        <a href="#" class="a" data-toggle="dropdown" title="{{ __('Currency')}}"><i class="icon-wallet icons mr-2"></i> {{Session::has('changed_currency') ? ucfirst(Session::get('changed_currency')) : $currency->code}}<i class="fa fa-angle-up lft-10"></i></a>  
                        <ul class="dropdown-menu">                          
                            @foreach($currencies as $currency)
                            <a href="{{ route('CurrencySwitch', $currency->code) }}" title="{{$currency->code}}"><li>{{$currency->code}}</li></a>
                            @endforeach
                        </ul>
                    </div>
                    @endif
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
                            <li>{{ $gsetting->cpy_txt }}</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="copyright-social">
                        <ul>                           
                            <li>
                                @if(isset($terms->terms) && $terms->terms != NULL && $terms->terms != '')
                                <a href="{{url('terms_condition')}}" title="{{ __('Terms & Condition') }}">{{ __('Terms & Condition') }}</a>
                                @endif
                            </li> 
                            <li>
                                @if(isset($terms->policy) && $terms->policy != NULL && $terms->policy != '')
                                <a href="{{url('privacy_policy')}}" title="{{ __('Privacy Policy') }}">{{ __('Privacy Policy') }}</a>
                                @endif
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
@include('instructormodel')