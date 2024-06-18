<div class="w-100 overflow-hidden">
    <div class="row m-0">
        <div class="col-12 p-0">
            <div class="heading_s3 text-center">
                <h2>{!! BaseHelper::clean($shortcode->title) !!}</h2>
            </div>
        </div>
    </div>
    
    <div class="full-cat row m-0">
        @foreach ($parentcategories as $category)
            <div class="col-md-4 container_foto" style="padding: 0px;">
                <a href="{{ $category->url }}">
                    <article class="text-left">
                        <h2>{{ $category->name }}</h2>
                    </article>
                    <img class=""width="474" height="250" src="{{ RvMedia::getImageUrl($category->image, null, false, RvMedia::getDefaultImage()) }}" alt="{{ $category->name }}" loading="lazy">
                </a>
            </div>
        @endforeach
    </div>
</div>