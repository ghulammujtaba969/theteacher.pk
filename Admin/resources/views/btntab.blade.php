@if($cats->courses_count >= 4)
<div class="view-button txt-rgt">
    <a href="{{ route('category.page',['id' => $cats->id, 'category' => $cats->title]) }}" class="btn btn-secondary" title="{{ __('View More')}}">{{ __('View More') }}<i data-feather="chevrons-right"></i>
    </a>
</div>
@endif