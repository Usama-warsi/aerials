<div class="mb-3">
    <label class="form-label">{{ __('Title') }}</label>
    <input type="text" name="title" value="{{ Arr::get($attributes, 'title') }}" class="form-control" placeholder="{{ __('Title') }}">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Video Link') }}</label>
    <input type="text" name="video" value="{{ Arr::get($attributes, 'video') }}" class="form-control" placeholder="{{ __('Video Link') }}">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Image Width (px/%/rem/em)') }}</label>
    <input type="text" name="iwidth" value="{{ Arr::get($attributes, 'iwidth') }}" class="form-control" placeholder="{{ __('Image width') }}">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Image Height (px/%/rem/em)') }}</label>
    <input type="text" name="iheight" value="{{ Arr::get($attributes, 'iheight') }}" class="form-control" placeholder="{{ __('Image height') }}">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Video Width (px/%/rem/em)') }}</label>
    <input type="text" name="vwidth" value="{{ Arr::get($attributes, 'vwidth') }}" class="form-control" placeholder="{{ __('Video width') }}">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Video Height (px/%/rem/em)') }}</label>
    <input type="text" name="vheight" value="{{ Arr::get($attributes, 'vheight') }}" class="form-control" placeholder="{{ __('Video height') }}">
</div>

<div class="mb-3">
   <div class="variation-images">
        <label class="form-label">{{ __('Choose Height') }}</label>
            @include('core/base::forms.partials.image', [
                'name' => 'image',
                'value' => Arr::get($attributes, 'image'),
            ])
        </div></div>
        
<div class="mb-3">
    <label class="form-label">{{ __('About Description') }}</label>
    <input type="textarea" name="description" value="{{ Arr::get($attributes, 'description') }}" class="form-control" placeholder="{{ __('description') }}">
</div>
<div class="mb-3">
    <label class="form-label">{{ __('About 2nd Column Description') }}</label>
    <input type="textarea" name="description2" value="{{ Arr::get($attributes, 'description2') }}" class="form-control" placeholder="{{ __('description2') }}">
</div>

<div class="mb-3">
    <label class="form-label">{{ __('Style') }}</label>
    {!! Form::customSelect('style', [
        'style-1' => __('Style 1 (video with heading)'),
        'style-2' => __('Style 2 (video without heading)'),
        'style-3' => __('Style 3 (with image left)'),
        'style-4' => __('Style 4 (with image right)'),
        'style-5' => __('Style 5 (content)'),
      
    ], Arr::get($attributes, 'style')) !!}
</div>
