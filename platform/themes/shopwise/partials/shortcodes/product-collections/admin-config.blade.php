<div class="mb-3">
    <label class="form-label">{{ __('Title') }}</label>
    <input type="text" name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control" placeholder="{{ __('Title') }}">
</div>


@if ($ads->isNotEmpty())
    <div class="mb-3">
        <label class="form-label">{{ __('Ads') }}</label>
        {!! Form::customSelect('ads_key', ['' => __('-- None --')] + $ads->pluck('name', 'key')->toArray(), Arr::get($attributes, 'ads_key')) !!}
    </div>
@endif

<div class="mb-3">
    <label class="form-label">Style</label>
    <select class=" form-select" id="" name="collection">
 
   @foreach($collections as $coll)
 
        <option value="{{$coll->id}}" @if(Arr::get($attributes, 'collection') == $coll->id) {{'selected'}} @endif >{{$coll->name}}</option>
    
 
@endforeach
    
    </select>

</div>

<div class="mb-3">
    <label class="form-label">{{ __('Style') }}</label>
    {!! Form::customSelect('style', [
        'style-1' => __('Style 1'),
        'style-2' => __('Style 2'),
        'style-3' => __('Style 3'),
        'style-4' => __('Style 4'),
        'style-5' => __('Style 5'),
        'style-6' => __('Style 6'),
    ], Arr::get($attributes, 'style')) !!}
</div>