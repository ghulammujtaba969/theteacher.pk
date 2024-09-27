@extends('theme2.master')
@section('title', 'Login')
@section('content')
@include('admin.message')
    <!-- Start Login -->
    <section id="login-page" class="login-main-block">
        <div class="login-block">
            <div class="row">
                <div class="col-lg-6 col-md-7">
                    <div class="logo">
                        <a href="{{route('home')}}" title=""><img src="images/logo/logo qued.png" class="img-fluid" alt=""></a>
                    </div>
                    <h4 class="container-heading">{{ __('Welcome Back') }}</h4>
                    <h6 class="login-heading">{{ __('Sign in to continue') }}</h6>
                    <form action="{{ url('login') }}" method="post">
                        @csrf
                        <div class="form-group mb-3">
                            <input class="form-control form-control-lg email" type="email" name="email" placeholder="Email" aria-label="">
                        </div>
                        <div class="form-group mb-3">
                            <input id="password" class="form-control form-control-lg" name="password" type="Password" placeholder="Password" aria-label="">
                            <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                        </div>
                        <div class="login-options">
                            <ul>
                                <li><button type="submit" class="btn btn-primary create-btn" title="Sign Up">Sign Up</button></li>
                                @if($gsetting->google_login_enable == 1)
                                <li class="google"><a href="{{ url('/auth/google') }}" target="_blank" title="google"><img src="images/login/google.png" alt="" class="img-fluid"></a></li>
                                @endif
                                @if($gsetting->fb_login_enable == 1)
                                <li class="facebook"><a href="{{ url('/auth/facebook') }}" target="_blank" title="facebook"><img src="images/login/facebook.png" alt="" class="img-fluid"></a></li>
                                @endif     
                                @if($gsetting->amazon_enable == 1)
                                <li class="facebook"><a href="{{ url('/auth/amazon') }}" target="_blank" title="amazon" class="social-icon amazon-icon" title="Amazon"><img src="images/login/amazon.png" alt="" class="img-fluid"></a></li>
                                @endif
                                @if($gsetting->linkedin_enable == 1)
                                <li class="facebook"><a href="{{ url('/auth/linkedin') }}" target="_blank" title="linkedin" class="social-icon linkedin-icon" title="Linkedin"><img src="images/login/linkedin.png" alt="" class="img-fluid"></a></li>
                                @endif
                                @if($gsetting->twitter_enable == 1)
                                <li class="facebook"><a href="{{ url('/auth/twitter') }}" target="_blank" title="twitter" class="social-icon twitter-icon" title="Twitter"><img src="images/login/twitter.png" alt="" class="img-fluid"></a></li>
                                @endif
                                @if($gsetting->gitlab_login_enable == 1)
                                <li class="facebook"><a href="{{ url('/auth/gitlab') }}" target="_blank" title="gitlab" class="social-icon gitlab-icon" title="Gitlab"><img src="images/login/github.png" alt="" class="img-fluid"></a></li>
                                @endif
                            </ul>
                        </div>
                    </form>
                    <br>
                    <div class="forgot-pass"><a href="{{ 'password/reset' }}" title="">{{ __('Forgot Password') }}?</a></div>
                   
                    <div class="sign-up">{{ __('Do not have an account') }}?  <a href="{{ route('register') }}" title="" target="_blank">{{ __('Sign up') }}</a></div>
                </div>
                <div class="col-lg-6 col-md-5">
                    <div class="login-img">
                        <img src="images/login/login-02.png" class="img-fluid" alt="img">    
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- End Login -->
    @endsection