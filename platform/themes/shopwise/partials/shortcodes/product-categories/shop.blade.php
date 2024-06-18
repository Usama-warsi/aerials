<div class="section">
    <form action="{{ URL::current() }}" method="GET">
        @if (request()->has('q'))
            <input type="hidden" name="q" value="{{ BaseHelper::stringify(request()->query('q')) }}">
        @endif

        @if (request()->has('categories'))
            <input type="hidden" name="categories[]" value="{{ Arr::first(request()->query('categories', [])) }}">
        @endif

        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-12">
                            <div class="heading_s2 text-center">
                                <h2>{!! BaseHelper::clean($shortcode->title) !!}</h2>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row shop-cat-grid">
                        @foreach ($categories as $category)
                            <div class="col-md-4 container_foto shoppy-container @if ($category->parent_id === 0) parentcat parent-{{ $category->id }} @else childcat parent-{{ $category->parent_id }} hidden @endif">
                                <a href="{{ $category->url }}" class="category-link">
                                    <article class="text-left">
                                        <h2>{{ $category->name }}</h2>
                                    </article>
                                    <img src="{{ RvMedia::getImageUrl($category->image, null, false, RvMedia::getDefaultImage()) }}" alt="{{ $category->name }}" loading="lazy">
                                </a>
                            </div>
                        @endforeach
                    </div>
                    
                    <script>
                        $(document).ready(function() {
                            $('.parentcat').click(function(event) {
                                var parentClass = $(this).attr('class').split(' ').find(cls => cls.startsWith('parent-'));
                                var childCategories = $('.childcat.parent-' + parentClass.substr(7));
                    
                                if (childCategories.length > 0) {
                                    event.preventDefault();
                                    
                                    if (!childCategories.hasClass('hidden')) {
                                        $('.childcat').addClass('hidden');
                                        $('.parentcat').removeClass('hidden');
                                    } else {
                                        $('.childcat').addClass('hidden');
                                        childCategories.removeClass('hidden');
                                        $('.parentcat').not(this).addClass('hidden');
                                        $(this).addClass('hidden');
                                    }
                                } else {
                                    console.log("Undefined");
                                }
                            });
                        });
                    </script>
                    
                </div>
                <div class="col-lg-3 order-lg-first mt-4 pt-2 mt-lg-0 pt-lg-0">
                    <div class="sidebar">
                        @include(Theme::getThemeNamespace() . '::views/ecommerce/includes/filters')
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<!-- END SECTION SHOP -->