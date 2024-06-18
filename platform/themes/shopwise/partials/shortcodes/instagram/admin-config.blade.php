<div class="mb-3">
    <label class="form-label">{{ __('Title') }}</label>
    <input type="text" name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control" placeholder="{{ __('Title') }}">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Username') }}</label>
    <input type="text" name="username" value="{{ Arr::get($attributes, 'username') }}" class="form-control" placeholder="{{ __('Username') }}">
</div>



<div class="mb-3">
    <label class="form-label">{{ __('Images') }}</label>
    {!! Form::mediaImages('images', explode(',', Arr::get($attributes, 'images'))) !!}
</div>
<div class="mb-3">
    <label class="form-label">{{ __('Style') }}</label>
    {!! Form::customSelect('style', [
        'style-1' => __('Style 1'),
        'style-2' => __('Style 2 (slider third party)'),
    ], Arr::get($attributes, 'style')) !!}
</div>