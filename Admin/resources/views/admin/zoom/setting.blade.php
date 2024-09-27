@extends('admin.layouts.master')
@section('title', 'Zoom Setting')
@section('maincontent')
<?php
$data['heading'] = 'Zoom Settings';
$data['title'] = 'Meetings';
$data['title1'] = 'Zoom Meetings';
$data['title2'] = 'Zoom Settings';
?>
@include('admin.layouts.topbar',$data)
<div class="contentbar">
  @if ($errors->any())  
  <div class="alert alert-danger" role="alert">
  @foreach($errors->all() as $error)     
  <p>{{ $error}}<button type="button" class="close" data-dismiss="alert" aria-label="Close" title="{{ __('Close') }}">
  <span aria-hidden="true" style="color:red;">&times;</span></button></p>
  @endforeach  
  </div>
  @endif
  <div class="row">
    <div class="col-lg-12">
      <div class="card dashboard-card m-b-30">
        <div class="card-header">
          <h5 class="card-title">{{ __('Zoom Meetings Settings') }}</h5>
        </div>
        <div class="card-body">

          <form action="{{ route('updateToken') }}" method="POST">
            @csrf

          <div class="row">
            <div class="col-md-6">

              <div class="form-group">
                <label>{{ __('Zoom Email') }}:</label>
                  <div class="input-group mb-3">
                    <input id="password-field" value="{{ Auth::user()->zoom_email }}" type="password"  name="zoom_email" class="form-control" placeholder="user@example.com">
                    <div class="input-group-prepend text-center">
                    <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span></i></span>
                    </div>
                  </div>
              </div>
                <div class="form-group">
                  <label>{{ __('Zoom JWT Token') }}:</label>
                  <textarea name="jwt_token" class="form-control" rows="5" cols="30" placeholder="Enter your JWT Token here">{{ Auth::user()->jwt_token }}</textarea>
                </div>
            </div>

            <div class="col-md-6">
              <h4 style="color: black"><i class="fa fa-question-circle"></i> {{ __('How to get JWT Token and Email') }} : </h4>
              <hr>
             <div class="panel panel-default">
              <div class="panel-body">
              <ul>
                <li>{{ __('First Sign up or Sign in here') }} : <a href="https://marketplace.zoom.us/" target="_blank">{{ __('Zoom Market Place Portal') }}</a></li>
                 <li>{{ __('Click on Top right side menu and click on build app') }} : <a href="https://marketplace.zoom.us/develop/create" target="_blank">{{ __('Create APP') }}</a></li>
                 <li>{{ __('Choose JWT APP and Continue...') }}</li>
                 <li>{{ __('After filling details click on credential tab and bottom you will see') }} <b>{{ __('JWT Token') }}</b> {{ __('change token expiry according to your setting.') }}</li>
                 <li>{{ __('Paste your zoom email account id and JWT token here and create,edit meetings here.') }}</li>
              </ul>
            </div>
            </div>
            </div>
            </div>
            
            <div class="form-group">
							<button type="reset" class="btn btn-danger-rgba mr-1" title="{{ __('Reset') }}"><i class="fa fa-ban"></i> {{ __("Reset")}}</button>
							<button type="submit" class="btn btn-primary-rgba" title="{{ __('Update') }}"><i class="fa fa-check-circle"></i>
							{{ __("Update")}}</button>
						</div>

          </form>
                
                    
    
                  
              
              

              
           
        </div>
      </div>
    </div>
  </div>
</div>



@endsection

