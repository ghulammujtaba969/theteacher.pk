@if($gsetting->promo_enable == 1)
<div id="promo-outer">
    <div id="promo-inner">
        <a href="{{ $gsetting['promo_link'] }}" title="{{ $gsetting['promo_text'] }}">{{ $gsetting['promo_text'] }}</a>
        <span id="close">x</span>
    </div>
</div>
<div id="promo-tab" class="display-none">{{__('SHOW')}}</div>
@endif

<section id="nav-bar" class="nav-bar-main-block" data-toggle="sticky-onscroll">
    <div class="container-xl">
        <!-- start navigation -->
        <div class="navigation fullscreen-search-block">
            <span style="font-size:30px;cursor:pointer" onclick="openNav()" class="hamburger">&#9776; </span>
            <div class="logo">
                  @if($gsetting->logo_type == 'L')
                    <a href="{{ url('/') }}" title="{{ $gsetting->project_title }}"><img src="{{ asset('images/logo/'.$gsetting->logo) }}" class="img-fluid" alt="{{ $gsetting->project_title }}"></a>
                @else()
                    <a href="{{ url('/') }}" title="{{ $gsetting->project_title }}"><b><div class="logotext">{{ $gsetting->project_title }}</div></b></a>
                @endif
            </div>
            <div class="nav-search nav-wishlist">                
                <a href="#find" title="{{__('Search')}}"><i data-feather="search"></i></a>
            </div>
            @auth
            <div class="shopping-cart">
                <a href="{{ route('cart.show') }}" title="{{__('Cart')}}"><i data-feather="shopping-cart"></i></a>
                <span class="red-menu-badge red-bg-success">
                    @php
                        $item = App\Cart::where('user_id', Auth::User()->id)->count();
                        if($item>0){
                            echo $item;
                        }
                        else{
                            echo "0";
                        }
                    @endphp
                </span>
            </div>
            <div class="nav-wishlist">
                <div id="notification_li">
                    <a href="{{ url('send') }}" id="notificationLinkk" title="{{__('Notifications')}}"><i data-feather="bell"></i></a>
                    <span class="red-menu-badge red-bg-success">
                        {{ Auth()->user()->unreadNotifications->where('type', 'App\Notifications\UserEnroll')->count() }}
                    </span>
                    <div id="notificationContainerr">
                    <div id="notificationTitle">{{ __('Notifications') }}</div>
                    <div id="notificationsBody" class="notifications">
                        <ul>
                            @foreach(Auth()->user()->unreadNotifications->where('type', 'App\Notifications\UserEnroll') as $notification)
                                <li class="unread-notification">
                                    <a href="{{url('notifications/'.$notification->id)}}">          
                                    <div class="notification-image">
                                        @if($notification->data['image'] !== NULL )
                                            <img src="{{ asset('images/course/'.$notification->data['image']) }}" alt="{{ __('Notifications') }}" class="img-fluid" >
                                        @else
                                            <img src="{{ Avatar::create($notification->data['id'])->toBase64() }}" alt="{{ __('Notifications') }}" class="img-fluid">
                                        @endif
                                    </div>
                                    <div class="notification-data">
                                        In {{ str_limit($notification->data['id'], $limit = 20, $end = '...') }}
                                        <br>
                                        {{ str_limit($notification->data['data'], $limit = 20, $end = '...') }}
                                    </div>
                                    </a>
                                </li>
                            @endforeach

                            @foreach(Auth()->user()->readNotifications->where('type', 'App\Notifications\UserEnroll') as $notification)
                                <li>
                                    <a href="{{ route('mycourse.show') }}" title="{{ __('Notifications') }}">
                                    <div class="notification-image">
                                        @if($notification->data['image'] !== NULL )
                                            <img src="{{ asset('images/course/'.$notification->data['image']) }}" alt="{{ __('Notifications') }}" class="img-fluid" >
                                        @else
                                           <img src="{{ Avatar::create($notification->data['id'])->toBase64() }}" alt="{{ __('Notifications') }}" class="img-fluid">
                                        @endif
                                    </div>
                                    <div class="notification-data">
                                        In {{  str_limit($notification->data['id'], $limit = 20, $end = '...') }}
                                        <br>
                                        {{ str_limit($notification->data['data'], $limit = 20, $end = '...') }}
                                    </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div id="notificationFooter"><a href="{{route('deleteNotification')}}" title="{{ __('Clear All') }}">{{ __('Clear All') }}</a></div>
                    </div>
                </div>
            </div>            
            @endauth   
            <div id="mySidenav" class="sidenav">
              <a href="javascript:void(0)" class="closebtn" onclick="closeNav()" title="{{ __('Close') }}">&times;</a>
                @guest
                <div class="login-block">
                    <a href="{{ route('register') }}" class="btn btn-primary" title="{{ __('Signup') }}">{{ __('Signup') }}</a>
                    <a href="{{ route('login') }}" class="btn btn-secondary" title="{{ __('Login') }}">{{ __('Login') }}</a>
                </div>
                @endguest
                @auth

                <div id="notificationTitle">
                     @if(Auth::User()['user_img'] != null && Auth::User()['user_img'] !='' && @file_get_contents('images/user_img/'.Auth::user()['user_img']))
                      <img src="{{ url('/images/user_img/'.Auth::User()->user_img) }}" class="dropdown-user-circle" alt="{{ Auth::User()->fname }}">
                    @else
                        <img src="{{ asset('images/default/user.jpg')}}" class="dropdown-user-circle" alt="{{ Auth::User()->fname }}">
                    @endif
                    <div class="user-detailss">
                        Hi, {{ Auth::User()->fname }}                        
                    </div>                    
                </div>
                <div class="login-block">
                    <a href="{{ route('logout') }}" title="{{ __('Logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <div id="notificationFooter">
                            {{ __('Logout') }}
                            
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="display-none">
                                @csrf
                            </form>
                        </div>
                    </a>
                </div>
                @endauth
                @php
                    $categories = App\Categories::orderBy('position','ASC')->with(['subcategory','subcategory.childcategory'])->get();
                @endphp                
                <div class="wrapper center-block">                    
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    @foreach($categories->where('status', '1') as $cate)
                      <div class="panel panel-default">
                        <div class="panel-heading active" role="tab" id="headingOne">
                            <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne{{ $cate->id }}" aria-expanded="true" aria-controls="collapseOne">
                                <i class="fa {{ $cate->icon }} rgt-10"></i> <label class="prime-cat" data-url="{{ route('category.page',['id' => $cate->id, 'category' => str_slug(str_replace('-','&',$cate->slug))]) }}">{{ str_limit($cate->title, $limit = 20, $end = '..') }}</label> 
                            </a>
                            </h4>
                        </div>                        
                        <div id="collapseOne{{ $cate->id }}" class="subcate-collapse panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                        @foreach($cate->subcategory as $sub)
                          @if($sub->status ==1)
                          <div class="panel-body">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingeleven">
                                  <h4 class="panel-title">
                                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseeleven{{ $sub->id }}" aria-expanded="false" aria-controls="collapseeleven">
                                      <i class="fa {{ $sub->icon }} rgt-10"></i> <label class="sub-cate" data-url="{{ route('subcategory.page',['id' => $sub->id, 'category' => str_slug(str_replace('-','&',$sub->slug))]) }}">{{ str_limit($sub->title, $limit = 15, $end = '..') }}</label>

                                    </a>
                                  </h4>
                                </div>
                                <div id="collapseeleven{{ $sub->id }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingeleven">
                                  @foreach($sub->childcategory as $child)
                                  @if($child->status ==1)
                                  <div class="panel-body sub-cat">
                                    <i class="fa {{ $child->icon }} rgt-10"></i> <label class="child-cate" data-url="{{ route('childcategory.page',['id' => $child->id, 'category' => str_slug(str_replace('-','&',$child->slug))]) }}">{{ $child->title }} </label>
                                  </div>
                                  @endif
                                  @endforeach
                                </div>                                
                            </div>
                          </div>
                          @endif
                        @endforeach
                        </div>                        
                      </div>
                    @endforeach
                  </div>                      
                </div>
                <div>
                    @php
                        $menus = App\Menu::get();
                        $pages = App\Page::get();
                    @endphp
                                            @if(isset($menus))
                    <ul>
                        <li>
                            <ul>
                                @foreach($menus as $menu)
                                @if($menu->link_by == 'url')
                                <li><a href="{{ $menu->url }}" title="{{ $menu->title }}"><i data-feather="align-justify"></i>{{ $menu->title }}</a></li>
                                @endif
                                @if($menu->link_by == 'page')
                                <li><a href="{{ route('page.show', $menu->page->slug) }}" title="{{ $menu->title }}"><i data-feather="align-justify"></i>{{ $menu->title }}</a></li>
                                @endif
                                @endforeach
                            </ul>
                        </li>
                    </ul>
                    @endif
                </div>
                @auth              
    
                   <div class="sidebar-nav-icon">
                    <ul>
                        @if(Auth::User()->role == "admin" )
                        
                        <a target="_blank" href="{{ url('/admins') }}" title="{{ __('Admin Dashboard') }}"><li><i data-feather="pie-chart"></i>{{ __('Admin Dashboard') }}</li></a>                       
                        @endif
                        @if(Auth::User()->role == "instructor")
                        <a target="_blank" href="{{ url('/instructor') }}" title="{{ __('Instructor Dashboard')}}"><li><i data-feather="pie-chart"></i>{{ __('Instructor Dashboard') }}</li></a>
                        @endif
                        <a href="{{ route('mycourse.show') }}" title="{{ __('My Courses')}}"><li><i data-feather="book-open"></i>{{ __('My Courses') }}</li></a>
                        <a href="{{ route('wishlist.show') }}" title="{{ __('Wishlist')}}"><li><i data-feather="heart"></i>{{ __('Wishlist') }}</li></a>
                        <a href="{{ route('purchase.show') }}" title="{{ __('Purchased History')}}"><li><i data-feather="shopping-cart"></i>{{ __('Purchased History') }}</li></a>
                        <a href="{{route('profile.show',Auth::User()->id)}}" title="{{ __('Profile')}}"><li><i data-feather="user"></i>{{ __('Profile') }}</li></a>
                        @if(Auth::User()->role == "user")
                        @if($gsetting->instructor_enable == 1)
                        <a href="#" data-toggle="modal" data-target="#myModalinstructor" title="{{ __('Become An Instructor')}}"><li><i data-feather="shield"></i>{{ __('Become An Instructor') }}</li></a>
                        @endif
                        @endif
                        <a href="{{ route('flash.deals') }}" title="{{ __('Flash Deals')}}"><li><i data-feather="battery-charging"></i>{{ __('Flash Deals') }}</li></a>
                        @if(env('ENABLE_INSTRUCTOR_SUBS_SYSTEM') == 1)
                        @if(Auth::User()->role == "instructor")
                        <a href="{{ route('plan.page') }}" title="{{ __('Instructor Plan')}}"><li><i data-feather="tag"></i>{{ __('Instructor Plan') }}</li></a>
                        @endif
                        @endif
                        @if(Auth::User()->role == "user" || Auth::User()->role == "instructor")
                        @if($gsetting->device_control == 1)
                        <a href="{{ route('active.courses') }}"  title="{{ __('Watchlist')}}"><li><i data-feather="framer"></i>{{ __('Watchlist') }}</li></a>
                        @endif
                        @endif
                        @if($gsetting->donation_enable == 1)
                        <a target="__blank" href="{{ $gsetting->donation_link }}" title="{{ __('Donation')}}"><li><i data-feather="framer"></i>{{ __('Donation') }}</li></a>
                        @endif
                        @if(Schema::hasTable('affiliate') && Schema::hasTable('wallet_settings'))
                        @php
                            $affiliate = App\Affiliate::first();
                            $wallet_settings = App\WalletSettings::first();
                        @endphp       
                        @if(isset($wallet_settings) && $wallet_settings->status == 1)
                        <a href="{{ url('/wallet') }}" title="{{ __('Wallet')}}"><li><i class="icon-wallet icons"></i>{{ __('Wallet') }}</li></a>
                        @endif
                        @if(isset($affiliate) && $affiliate->status == 1)
                        <a href="{{ route('get.affiliate') }}" title="{{ __('Affiliate')}}"><li><i data-feather="users"></i>{{ __('Affiliate') }}</li></a>
                        @endif
                        @endif
                        <a href="{{ route('compare.index') }}" title="{{ __('Compare')}}"><li><i data-feather="bar-chart"></i>{{ __("Compare") }}</li></a>
                        @if(Module::has('Resume') && Module::find('Resume')->isEnabled())
                            @include('resume::front.searchresume')
                        @endif
                        @if(Module::has('Resume') && Module::find('Resume')->isEnabled())
                            @include('resume::front.job.icon')
                        @endif                       
                        @if(Module::find('Forum') && Module::find('Forum')->isEnabled())
                            @if($gsetting->forum_enable == 1)
                                @include('forum::layouts.sidebar_menu')
                            @endif
                        @endif
                         <a href="{{ route('my.leaderboard') }}" title="{{ __('leader Board')}}"><li><i class="icon-chart icons"></i>{{ __('Leader Board') }}</li></a>
                        @if(Auth::User()->role == "user")
                        <a href="{{ route('studentprofile') }}" title="{{ __('Share Profile')}}"><li><i data-feather="share"></i>{{ __('Share profile') }}</li></a>
                       @endif
                       <a href="{{ route('affilate.report') }}" title="{{ __('Affiliate Dashboard')}}"><li><i data-feather="users"></i>{{__('Affiliate Dashboard')}}</li></a>
                    </ul>
                </div>
                <div>
                    @if(Module::has('Ebook') && Module::find('Ebook')->isEnabled())
                    @include('ebook::sidebar.nav')
                @endif
                </div>
                @endauth
            </div>
        </div>        
        <!-- end navigation -->
        <div class="row smallscreen-search-block">
            <div class="col-lg-5">
                <div class="row">
                    <div class="col-lg-5 col-md-4 col-sm-12">
                        <div class="logo">
                            @if($gsetting->logo_type == 'L')
                                <a href="{{ url('/') }}" title="{{ $gsetting->project_title }}"><img src="{{ asset('images/logo/'.$gsetting->logo) }}" class="img-fluid" alt="{{ $gsetting->project_title }}"></a>
                            @else()
                                <a href="{{ url('/') }}"><b><div class="logotext">{{ $gsetting->project_title }}</div></b></a>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-4 col-sm-12">
                        <div class="navigation">
                            <div id="cssmenu">
                                <ul>
                                    <li class="mr-4"><a href="#" title="{{ __('Categories') }}"><i data-feather="grid"></i>{{ __('Categories') }}</a>                                       
                                        <ul>
                                            @foreach($categories as $cate)
                                            @if($cate->status == 1 )
                                            <li><a href="{{ route('category.page',['id' => $cate->id, 'category' => $cate->title]) }}" title="{{ $cate->title }}"><i class="fa {{ $cate->icon }} rgt-20"></i>{{ str_limit($cate->title, $limit = 25, $end = '..') }} <i data-feather="chevron-right" class="float-right"></i></a>
                                            <ul>   
                                                @foreach($cate->subcategory as $sub)
                                                @if($sub->status ==1)
                                                <li><a href="{{ route('subcategory.page',['id' => $sub->id, 'category' => $sub->title]) }}" title="{{ $sub->title }}"><i class="fa {{ $sub->icon }} rgt-20"></i>{{ str_limit($sub->title, $limit = 25, $end = '..') }}
                                                    <i data-feather="chevron-right" class="float-right"></i></a>
                                                    <ul>
                                                        @foreach($sub->childcategory as $child)
                                                        @if($child->status ==1)
                                                        <li>
                                                            <a href="{{ route('childcategory.page',['id' => $child->id, 'category' => $child->title]) }}" title="{{ $child->title }}"><i class="fa {{ $child->icon }} rgt-20"></i>{{ str_limit($child->title, $limit = 25, $end = '..') }}</a>
                                                        </li>
                                                        @endif
                                                        @endforeach
                                                    </ul>
                                                </li>
                                                @endif
                                               @endforeach
                                            </ul>
                                            </li>
                                            @endif
                                            @endforeach
                                        </ul>
                                    </li>
                                    @if(Module::has('Ebook') && Module::find('Ebook')->isEnabled())
                                    @include('ebook::sidebar.nav')
                                @endif                              
                              </ul>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
            <div class="col-lg-7">
                @guest
                <div class="row">
                    <div class="col-lg-7 col-md-6">
                        <div class="learning-business">                            
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="shopping-cart">
                            <a href="{{ route('cart.show') }}" title="{{ __('Cart') }}"><i data-feather="shopping-cart"></i></a>
                            <span class="red-menu-badge red-bg-success">
                                @php
                                    $item = session()->get('cart.add_to_cart');                                    
                                    if(isset($item) && count($item)>0){
                                        echo count(array_unique($item));
                                    }
                                    else{
                                        echo "0";
                                    }
                                @endphp
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-1">
                        <div class="search search-one" id="search">
                            <form method="GET" id="searchform" action="{{ route('search') }}">
                              <div class="search-input-wrap">
                                <input class="search-input" name="searchTerm" placeholder="{{ __('Search in Site') }}" type="text" id="course_name" autocomplete="off" />
                              </div>
                              <input class="search-submit" type="submit" id="go" value="">
                              <div class="icon"><i data-feather="search"></i></div>
                              <div id="course_data"></div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="Login-btn">
                            <a href="{{ route('login') }}" class="btn btn-secondary" title="{{ __('Login') }}">{{ __('Login') }}</a>
                            <a href="{{ route('register') }}" class="btn btn-primary" title="{{ __('Signup') }}">{{ __('Signup') }}</a>                            
                        </div> 
                    </div>
                @endguest
                @auth
                <div class="row">         
                    <div class="col-lg-5 col-md-3 col-6">
                        <div class="learning-business learning-business-two">                           
                        </div>
                    </div>                  
                    <div class="col-lg-1 col-md-1 col-sm-2 col-2">
                        <div class="nav-wishlist">
                            <ul id="nav">
                                <li id="notification_li">
                                    <a href="{{ url('send') }}" id="notificationLink" title="{{ __('Notifications') }}"><i data-feather="bell"></i></a>
                                    <span class="red-menu-badge red-bg-success">
                                        {{ Auth()->user()->unreadNotifications->where('type', 'App\Notifications\UserEnroll')->count() }}
                                    </span>
                                    <div id="notificationContainer">
                                    <div id="notificationTitle">{{ __('Notifications') }}</div>
                                    <div id="notificationsBody" class="notifications">
                                        <ul>
                                            @foreach(Auth()->user()->unreadNotifications->where('type', 'App\Notifications\UserEnroll') as $notification)
                                                <li class="unread-notification">
                                                    <a href="{{url('notifications/'.$notification->id)}}" title="{{ __('Notifications') }}">          
                                                    <div class="notification-image">
                                                        @if($notification->data['image'] !== NULL )
                                                            <img src="{{ asset('images/course/'.$notification->data['image']) }}" alt="{{ __('Notifications') }}" class="img-fluid" >
                                                        @else
                                                            <img src="{{ Avatar::create($notification->data['id'])->toBase64() }}" alt="{{ __('Notifications') }}" class="img-fluid">
                                                        @endif
                                                    </div>
                                                    <div class="notification-data">
                                                        In {{ str_limit($notification->data['id'], $limit = 20, $end = '...') }}
                                                        <br>
                                                        {{ str_limit($notification->data['data'], $limit = 20, $end = '...') }}
                                                    </div>
                                                    </a>
                                                </li>
                                            @endforeach

                                            @foreach(Auth()->user()->readNotifications->where('type', 'App\Notifications\UserEnroll') as $notification)
                                                <li>
                                                    <a href="{{ route('mycourse.show') }}">
                                                    <div class="notification-image">
                                                        @if($notification->data['image'] !== NULL )
                                                            <img src="{{ asset('images/course/'.$notification->data['image']) }}" alt="{{ __('Courses') }}" class="img-fluid" >
                                                        @else
                                                           <img src="{{ Avatar::create($notification->data['id'])->toBase64() }}" alt="{{ __('Courses') }}" class="img-fluid">
                                                        @endif
                                                    </div>
                                                    <div class="notification-data">
                                                        In {{  str_limit($notification->data['id'], $limit = 20, $end = '...') }}
                                                        <br>
                                                        {{ str_limit($notification->data['data'], $limit = 20, $end = '...') }}
                                                    </div>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div id="notificationFooter"><a href="{{route('deleteNotification')}}" title="{{ __('Clear All') }}">{{ __('Clear All') }}</a></div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-2 col-2">
                        <div class="nav-wishlist">
                            <a href="{{ route('wishlist.show') }}" title="Go to Wishlist"><i data-feather="heart"></i></a>
                            <span class="red-menu-badge red-bg-success">
                                @php
                                    $wishlist = App\Wishlist::where('user_id', Auth::User()->id)->get();
                                    
                                @endphp                             

                                @php
                                    $counter = 0;
                                    foreach ($wishlist as $item) {
                                         if($item->courses->status == '1'){

                                              
                                          $counter++;
       
                                         }
                                    }

                                    echo  $counter; 
                                @endphp
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-2 col-2">
                        <div class="shopping-cart">
                            <a href="{{ route('cart.show') }}" title="{{ __('Cart') }}"><i data-feather="shopping-cart"></i></a>
                            <span class="red-menu-badge red-bg-success">
                                @php
                                    $item = App\Cart::where('user_id', Auth::User()->id)->count();
                                    if($item>0){
                                        echo $item;
                                    }
                                    else{
                                        echo "0";
                                    }
                                @endphp
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-2 col-2">
                        <div class="search search-one" id="search">
                            <form method="GET" id="searchform" action="{{ route('search') }}">
                              <div class="search-input-wrap">
                                <input class="search-input" name="searchTerm" placeholder="{{ __('Search In Site') }}" type="text" id="course_name" autocomplete="off" />
                              </div>
                              <input class="search-submit" type="submit" id="go" value="">
                              <div class="icon"><i data-feather="search"></i></div>
                              <div id="course_data"></div>
                            </form>
                        </div>                       
                    </div>
                            @php
                                $menus = App\Menu::get();
                                $pages = App\Page::get();
                            @endphp
                                @if(isset($menus) && $menus->isNotEmpty() && $pages->isNotEmpty())
                                <div class="col-lg-1">
                            <div class="navigation navigation-one">
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                        <i data-feather="align-justify"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach($menus as $menu)
                                        @if($menu->position_menu == 'top')
                                        @if($menu->link_by == 'url')
                                        <li><a class="" href="{{ $menu->url }}" title="{{ $menu->title }}">{{ $menu->title }}</a></li>
                                        @endif
                                        @if($menu->link_by == 'page')
                                        <li><a class="" href="{{ route('page.show', $menu->page->slug) }}" title="{{ $menu->title }}">{{ $menu->title }}</a></li>
                                        @endif
                                        @endif
                                        @endforeach
                                    </ul>
                                </div>

                            </div>
                    </div>
                    @endif
                    @php
                    $user = \Auth::user(); 
                    $roles = $user->getRoleNames();
                    $test_id = Spatie\Permission\Models\Role::select('id')->where('name',$roles[0])->get();
                    $dropdown = App\Dropdown::where('role_id', $test_id[0]['id'])->get();
                    @endphp
                    @if($roles[0] != "admin" &&  $roles[0] != "instructor" && $roles[0] != "user")
                    @foreach($dropdown as $drop)
                    <div class="col-lg-2 col-md-3 col-sm-6 col-6">
                        <div class="my-container">
                          <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle  my-dropdown" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                @if(Auth::User()['user_img'] != null && Auth::User()['user_img'] !='' && @file_get_contents('images/user_img/'.Auth::user()['user_img']))
                                  <img src="{{ url('/images/user_img/'.Auth::User()->user_img) }}" class="circle" alt="{{ Auth::User()->fname }}">
                                @else
                                    <img src="{{ asset('images/default/user.jpg')}}"  class="circle" alt="{{ Auth::User()->fname }}">
                                @endif
                                <span class="dropdown__item name" id="name">{{ str_limit(Auth::User()->fname, $limit = 10, $end = '..') }}</span>
                                <span class="dropdown__item caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right User-Dropdown U-open" aria-labelledby="dropdownMenu1">
                                <div id="notificationTitle">
                                     @if(Auth::User()['user_img'] != null && Auth::User()['user_img'] !='' && @file_get_contents('images/user_img/'.Auth::user()['user_img']))
                                      <img src="{{ url('/images/user_img/'.Auth::User()->user_img) }}" class="dropdown-user-circle" alt="{{ Auth::User()->fname }}">
                                    @else
                                        <img src="{{ asset('images/default/user.jpg')}}" class="dropdown-user-circle" alt="{{ Auth::User()->fname }}">
                                    @endif
                                    <div class="user-detailss">
                                        {{ Auth::User()->fname }}
                                        <br>
                                        {{ Auth::User()->email }}
                                    </div>                                    
                                </div>
                                <div class="scroll-down">
                                @if(Auth::User()->role == "admin" )                               
                                <a target="_blank" href="{{ url('/admins') }}" title="{{ __('Admin Dashboard') }}"><li><i data-feather="pie-chart"></i>{{ __('Admin Dashboard') }}</li></a>
                                @endif
                                @if(Auth::User()->role == "instructor")
                                <a target="_blank" href="{{ url('/instructor') }}" title="{{ __('Instructor Dashboard') }}"><li><i data-feather="pie-chart"></i>{{ __('Instructor Dashboard') }}</li></a>
                                @endif

                              
                                @if($drop->my_courses == '1')
                                <a href="{{ route('mycourse.show') }}" title="{{ __('My Courses') }}"><li><i data-feather="book-open"></i>{{ __('My Courses') }}</li></a>
                                @endif
                                @if($drop->my_wishlist == '1')
                                <a href="{{ route('wishlist.show') }}" title="{{ __('Wishlist')}}"><li><i data-feather="heart"></i>{{ __('Wishlist') }}</li></a>
                                @endif
                                @if($drop->purchased_history == '1')
                                <a href="{{ route('purchase.show') }}" title="{{ __('Purchased History')}}"><li><i data-feather="shopping-cart"></i>{{ __('Purchased History') }}</li></a>
                                @endif
                                @if($drop->my_profile == '1')
                                <a href="{{route('profile.show',Auth::User()->id)}}" title="{{ __('Profile')}}"><li><i data-feather="user"></i>{{ __('Profile') }}</li></a>
                                @endif
                                @if(Auth::User()->role == "user")
                                @if($gsetting->instructor_enable == 1)
                                <a href="#" data-toggle="modal" data-target="#myModalinstructor"  title="{{ __('Become An Instructor')}}"><li><i data-feather="shield"></i>{{ __('Become An Instructor') }}</li></a>
                                @endif
                        
                                @endif
                                @if($drop->flash_deal == '1')
                                <a href="{{ route('flash.deals') }}" title="{{ __('Flash Deal')}}"><li><i data-feather="battery-charging"></i>{{ __('Flash Deals') }}</li></a>
                                @endif
                                @if(env('ENABLE_INSTRUCTOR_SUBS_SYSTEM') == 1)
                                @if(Auth::User()->role == "instructor")
                                <a href="{{ route('plan.page') }}" title="{{ __('Instructor Plan')}}"><li><i data-feather="tag"></i>{{ __('Instructor Plan') }}</li></a>
                                @endif
                                @endif
                                @if(Auth::User()->role == "user" || Auth::User()->role == "instructor")
                                @if($gsetting->device_control == 1)
                                <a href="{{ route('active.courses') }}" title="{{ __('Watchlist')}}"><li><i data-feather="framer"></i>{{ __('Watchlist') }}</li></a>
                                @endif
                                @endif                                
                                @if($gsetting->donation_enable == 1 && $drop->donation == '1')
                                <a target="__blank" href="{{ $gsetting->donation_link }}" title="{{ __('Donation')}}"><li><i data-feather="framer"></i>{{ __('Donation') }}</li></a>
                                @endif
                                @if($gsetting->affilate == 1 && $drop->my_wallet == '1')
                                @if(Schema::hasTable('affiliate') && Schema::hasTable('wallet_settings'))
                                @endif
                                @if(isset($wallet_settings) && $wallet_settings->status == 1)
                                <a href="{{ url('/wallet') }}" title="{{ __('Wallet')}}"><li><i class="icon-wallet icons"></i>{{ __('Wallet') }}</li></a>
                                @endif
                                @if(isset($affiliate) && $affiliate->status == 1)
                                <a href="{{ route('get.affiliate') }}" title="{{ __('Affiliate')}}"><li><i data-feather="users"></i>{{ __('Affiliate') }}</li></a>
                                @endif
                                @endif
                                @if($drop->compare == '1')
                                <a href="{{ route('compare.index') }}" title="{{ __('Compare')}}"><li><i data-feather="bar-chart"></i>{{ __("Compare") }}</li></a>
                                @endif
                                @if($drop->search_job == '1')
                                @if(Module::has('Resume') && Module::find('Resume')->isEnabled())
                                    @include('resume::front.searchresume')
                                @endif
                                @endif
                                @if($drop->job_portal == '1')
                                @if(Module::has('Resume') && Module::find('Resume')->isEnabled())
                                    @include('resume::front.job.icon')
                                @endif
                                @endif
                                @if($drop->form_enable == '1')
                                @if(Module::find('Forum') && Module::find('Forum')->isEnabled())
                                    @if($gsetting->forum_enable == 1)
                                        @include('forum::layouts.sidebar_menu')
                                    @endif
                                @endif
                                @endif
                                @if($drop->my_leadership == '1')
                                <a href="{{ route('my.leaderboard') }}" title="{{ __('leader Board')}}"><li><i class="icon-chart icons"></i>{{ __('Leader Board') }}</li></a>
                                @endif
                                @if(Auth::User()->role == "user")
                                <a href="{{ route('studentprofile') }}" title="{{ __('Share Profile')}}"><li><i data-feather="share"></i>{{ __('Share Profile') }}</li></a>
                                @endif
                                @if($drop->affilate_dashboard == '1')
                                <a href="{{ route('affilate.report') }}"><li><i data-feather="users"></i>{{__('Affiliate Dashboard')}}</li></a>
                                @endif
                                <a href="{{ route('batch.front') }}" title="{{ __('Batch')}}"><li><i data-feather="book-open"></i>{{__('Batch')}}</li></a>
                                </div>
                                @if(Module::has('Ebook') && Module::find('Ebook')->isEnabled())
                                <a href="{{ route('web.ebook.confirm-order') }}" title="{{ __('My eBook')}}"><li><i data-feather="book-open"></i>{{ __('My eBook') }}</li></a>
                                @endif 
                                <a href="{{ route('logout') }}" title="{{ __('Logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <div id="notificationFooter">
                                        {{ __('Logout') }}                                        
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="display-none">
                                            @csrf
                                        </form>
                                    </div>
                                </a>
                            </ul>
                          </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <div class="col-lg-2 col-md-3 col-sm-6 col-6">
                        <div class="my-container">
                          <div class="dropdown">
                            <button class="btn btn-default dropdown-toggle  my-dropdown" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                 @if(Auth::User()['user_img'] != null && Auth::User()['user_img'] !='' && @file_get_contents('images/user_img/'.Auth::user()['user_img']))
                                  <img src="{{ url('/images/user_img/'.Auth::User()->user_img) }}" class="circle" alt="{{ Auth::User()->fname }}">
                                @else
                                    <img src="{{ asset('images/default/user.jpg')}}"  class="circle" alt="{{ Auth::User()->fname }}">
                                @endif
                                <span class="dropdown__item name" id="name">{{ str_limit(Auth::User()->fname, $limit = 10, $end = '..') }}</span>
                                <span class="dropdown__item caret"></span>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-right User-Dropdown U-open" aria-labelledby="dropdownMenu1">
                                <div id="notificationTitle">
                                     @if(Auth::User()['user_img'] != null && Auth::User()['user_img'] !='' && @file_get_contents('images/user_img/'.Auth::user()['user_img']))
                                      <img src="{{ url('/images/user_img/'.Auth::User()->user_img) }}" class="dropdown-user-circle" alt="{{ Auth::User()->fname }}">
                                    @else
                                        <img src="{{ asset('images/default/user.jpg')}}" class="dropdown-user-circle" alt="{{ Auth::User()->fname }}">
                                    @endif
                                    <div class="user-detailss">
                                        {{ Auth::User()->fname }}
                                        <br>
                                        {{ Auth::User()->email }}
                                    </div>
                                    
                                </div>

                                <div class="scroll-down">
                                @if(Auth::User()->role == "admin" )                               
                                <a target="_blank" href="{{ url('/admins') }}" title="{{ __('Admin Dashboard')}}"><li><i data-feather="pie-chart"></i>{{ __('Admin Dashboard') }}</li></a>
                                @endif
                                @if(Auth::User()->role == "instructor")
                                <a target="_blank" href="{{ url('/instructor') }}" title="{{ __('Instructor Dashboard')}}"><li><i data-feather="pie-chart"></i>{{ __('Instructor Dashboard') }}</li></a>
                                @endif
                                <a href="{{ route('mycourse.show') }}" title="{{ __('My Courses')}}"><li><i data-feather="book-open"></i>{{ __('My Courses') }}</li></a>
                                <a href="{{ route('wishlist.show') }}" title="{{ __('Wishlist')}}"><li><i data-feather="heart"></i>{{ __('Wishlist') }}</li></a>
                                <a href="{{ route('purchase.show') }}" title="{{ __('Purchased History')}}"><li><i data-feather="shopping-cart"></i>{{ __('Purchased History') }}</li></a>
                                <a href="{{route('profile.show',Auth::User()->id)}}" title="{{ __('Profile')}}"><li><i data-feather="user"></i>{{ __('Profile') }}</li></a>
                                @if(Auth::User()->role == "user")
                                @if($gsetting->instructor_enable == 1)
                                <a href="#" data-toggle="modal" data-target="#myModalinstructor" title="{{ __('Become An Instructor')}}"><li><i data-feather="shield"></i>{{ __('Become An Instructor') }}</li></a>
                                @endif                        
                                @endif
                                <a href="{{ route('flash.deals') }}" title="{{ __('Flash Deals')}}"><li><i data-feather="battery-charging"></i>{{ __('Flash Deals') }}</li></a>
                                @if(env('ENABLE_INSTRUCTOR_SUBS_SYSTEM') == 1)
                                @if(Auth::User()->role == "instructor")
                                <a href="{{ route('plan.page') }}" title="{{ __('Instructor Plan')}}"><li><i data-feather="tag"></i>{{ __('Instructor Plan') }}</li></a>
                                @endif
                                @endif
                                @if(Auth::User()->role == "user" || Auth::User()->role == "instructor")
                                @if($gsetting->device_control == 1)
                                <a href="{{ route('active.courses') }}" title="{{ __('Watchlist')}}"><li><i data-feather="framer"></i>{{ __('Watchlist') }}</li></a>
                                @endif
                                @endif
                                @if($gsetting->donation_enable == 1)
                                <a target="__blank" href="{{ $gsetting->donation_link }}" title="{{ __('Donation')}}"><li><i data-feather="framer"></i>{{ __('Donation') }}</li></a>
                                @endif
                                @if(Schema::hasTable('affiliate') && Schema::hasTable('wallet_settings'))
                                @if(isset($wallet_settings) && $wallet_settings->status == 1)
                                <a href="{{ url('/wallet') }}" title="{{ __('Wallet')}}"><li><i class="icon-wallet icons"></i>{{ __('Wallet') }}</li></a>
                                @endif
                                @if(isset($affiliate) && $affiliate->status == 1)
                                <a href="{{ route('get.affiliate') }}" title="{{ __('Affiliate')}}"><li><i data-feather="users"></i>{{ __('Affiliate') }}</li></a>
                                @endif
                                @endif
                                <a href="{{ route('compare.index') }}" title="{{ __('Compare')}}"><li><i data-feather="bar-chart"></i>{{ __("Compare") }}</li></a>
                                @if(Module::has('Resume') && Module::find('Resume')->isEnabled())
                                    @include('resume::front.searchresume')
                                @endif
                                @if(Module::has('Resume') && Module::find('Resume')->isEnabled())
                                    @include('resume::front.job.icon')
                                @endif                               
                                @if(Module::find('Forum') && Module::find('Forum')->isEnabled())
                                    @if($gsetting->forum_enable == 1)
                                        @include('forum::layouts.sidebar_menu')
                                    @endif
                                @endif
                                <a href="{{ route('my.leaderboard') }}" title="{{ __('Leader Board')}}"><li><i class="icon-chart icons"></i>{{ __('Leader Board') }}</li></a>
                                @if(Auth::User()->role == "user")
                                <a href="{{ route('studentprofile') }}" title="{{ __('Share Profile')}}"><li><i data-feather="share"></i>{{ __('Share Profile') }}</li></a>
                                @endif
                                <a href="{{ route('affilate.report') }}" title="{{ __('Affiliate Dashboard')}}"><li><i data-feather="users"></i>{{__('Affiliate Dashboard')}}</li></a>
                                <a href="{{ route('batch.front') }}" title="{{ __('Batch')}}"><li><i data-feather="book-open"></i>{{__('Batch')}}</li></a>
                                @if(Module::has('Ebook') && Module::find('Ebook')->isEnabled())
                                <a href="{{ route('web.ebook.confirm-order') }}" title="{{ __('My eBook')}}"><li><i data-feather="book-open"></i>{{ __('My eBook') }}</li></a>
                                @endif 
                                </div>
                                <a href="{{ route('logout') }}" title="{{ __('Logout')}}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <div id="notificationFooter">
                                        {{ __('Logout') }}                                        
                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="display-none">
                                            @csrf
                                        </form>
                                    </div>
                                </a>
                            </ul>
                          </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endauth
            </div>
        </div>        
    </div>
</section>
<!-- start search -->
<div id="find" class="small-screen-navigation">
    <button type="button" class="close" title="{{ __('Close')}}">×</button>
     <form action="{{ route('search') }}" class="form-inline search-form" method="GET">
         <input type="find" name="searchTerm" class="form-control" id="search"  placeholder="{{ __('Searchforcourses') }}" value="{{ isset($searchTerm) ? $searchTerm : '' }}">
         <button type="submit" class="btn btn-outline-info btn_sm" title="{{ __('Search')}}">{{__('Search')}}</button> 
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
@include('instructormodel')