<!-- START SECTION CATEGORIES -->
<style>
ul.ps-list--categories li {
    list-style-type: none;
}
</style>

<div class="section pt-0 small_pb">
	<div class="container">
	    <div class="row">
    	    <div class="col-lg-9">
    	        <div class="row">
                    @foreach ($categories as $category)
                    <div class="col-md-4 container_foto">
                        <a href="{{ $category->url }}" class="">
                            <article class="text-left">
                                <h2>{{ $category->name }}</h2>
                            </article>
                            <img src="{{ RvMedia::getImageUrl($category->image, null, false, RvMedia::getDefaultImage()) }}"
                                alt="{{ $category->name }}" loading="lazy">
                        </a>
                    </div>
                    @endforeach
                </div>
    	    </div>
    	    <div class="col-lg-3 order-lg-first mt-4 pt-2 mt-lg-0 pt-lg-0">
                <div class="sidebar">
                    @include(Theme::getThemeNamespace() . '::views/ecommerce/includes/filters')
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END SECTION CATEGORIES -->
