@extends('theme.master')
@section('title', 'Batch')
@section('content')
@include('admin.message')
@php
$gets = App\Breadcum::first();
@endphp
@if(isset($gets))
<section id="business-home" class="business-home-main-block">
    <div class="business-img">
        @if($gets['img'] !== NULL && $gets['img'] !== '')
        <img src="{{ url('/images/breadcum/'.$gets->img) }}" class="img-fluid" alt="" />
        @else
        <img src="{{ Avatar::create($gets->text)->toBase64() }}" alt="course" class="img-fluid">
        @endif
    </div>
    <div class="overlay-bg"></div>
    <div class="container-fluid">
        <div class="business-dtl">
            <div class="row">
                <div class="col-lg-6">
                    <div class="bredcrumb-dtl">
                        <h1 class="wishlist-home-heading">{{ __('Batch') }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
<section id="batch" class="batch-main-block">
    <div class="container-xl">
        <div class="row">
            @foreach($data as $datas)
            <div class="col-lg-3">
                <div class="student-view-block student-view-block-1">
                    <div class="genre-slide-image">
                        <div class="view-block">
                            <div class="view-img">
                                <a href=""><img src="{{ asset('images/batch/'.$datas->preview_image) }}" class="img-fluid" alt=""></a>
                            </div>
                            <div class="view-dtl">
                                <div class="view-heading"><a href="">{{ $datas->title }}</a>
                                </div>
                                <div class="batch-dtl mb-3">
                                    <p>{{substr(preg_replace("/\r\n|\r|\n/",'',strip_tags(html_entity_decode($datas->detail))), 0, 120)}}...</p>
                                </div>
                                <div class="batch-btn mb-2">
                                    <a href="{{ route('batch.frontdetail',$datas->id) }}" type="button" class="btn btn-primary">{{__('view more')}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endsection