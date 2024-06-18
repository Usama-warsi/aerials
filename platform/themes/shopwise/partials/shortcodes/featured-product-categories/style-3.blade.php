<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="heading_s3 text-center">
                <h2>{!! BaseHelper::clean($shortcode->title) !!}</h2>
            </div>
        </div>
    </div>
    
    <div class="row">
        @foreach ($categories as $category)
            @if($category->parent_id == 0)
            <div class="col-md-4 container_foto">
                <a href="{{ $category->url }}">
                    <article class="text-left">
                        <h2>{{ $category->name }}</h2>
                    </article>
                    <img src="{{ RvMedia::getImageUrl($category->image, null, false, RvMedia::getDefaultImage()) }}" alt="{{ $category->name }}" loading="lazy">
                </a>
            </div>
            @endif
        @endforeach
    </div>
</div>