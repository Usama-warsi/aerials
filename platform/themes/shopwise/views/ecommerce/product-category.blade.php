@php Theme::set('pageName', $category->name); @endphp
<div class="section">
    <form action="{{ $category->url }}" method="GET">
        @if (request()->has('q'))
            <input type="hidden" name="q" value="{{ BaseHelper::stringify(request()->query('q')) }}">
        @endif
        @if (request()->has('categories'))
            <input type="hidden" name="categories[]" value="{{ Arr::first(request()->query('categories', [])) }}">
        @endif
        @if (request()->has('categories'))
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <div id="category-sector" class="row align-items-center mb-4 pb-1">
                        <div class="w-100 C-131 d-flex">
                            @php
                                [$categories, $brands, $tags, $rand, $categoriesRequest, $urlCurrent, $categoryId, $maxFilterPrice] = EcommerceHelper::dataForFilter($category ?? null);
                                $categories = ProductCategoryHelper::getActiveTreeCategories();                                
                                Theme::asset()->usePath()->add('jquery-ui-css', 'css/jquery-ui.css');
                                Theme::asset()->container('footer')->usePath()->add('jquery-ui-js', 'js/jquery-ui.js', ['jquery']);
                                Theme::asset()->container('footer')->usePath()->add('touch-punch', 'js/jquery.ui.touch-punch.min.js', ['jquery-ui-js']);
                                Theme::asset()->usePath()->add('custom-scrollbar-css', 'plugins/mcustom-scrollbar/jquery.mCustomScrollbar.css');
                                Theme::asset()->container('footer')->usePath()->add('custom-scrollbar-js', 'plugins/mcustom-scrollbar/jquery.mCustomScrollbar.js', ['jquery']);
                            @endphp
                            
                            @foreach($categories as $subcategory)
                                <div class="col-md-4 container_foto shoppy-container @if ($subcategory->activeChildren->count()) parent-category @endif" style="padding: 5px;">
                                    <a href="{{ $subcategory->url }}" class="category-link">
                                        <article class="text-left">
                                            <h2>{{ $subcategory->name }}</h2>
                                        </article>
                                        <img src="{{ RvMedia::getImageUrl($subcategory->image, null, false, RvMedia::getDefaultImage()) }}" alt="{{ $subcategory->name }}" loading="lazy">
                                    </a>
                                </div>
                                @if ($subcategory->activeChildren->count())
                                    @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.sub-childs', ['children' => $subcategory->activeChildren])
                                @endif
                            @endforeach
                            
                            <div id="products-section" class="row shop_container grid d-none">
                                @if ($products->count() > 0)
                                    @foreach($products as $product)
                                        <div class="col-md-4 col-6">
                                            {!! Theme::partial('product-item-grid', compact('product')) !!}
                                        </div>
                                    @endforeach
                                    <div class="mt-3 justify-content-center pagination_style1">
                                        {!! $products->appends(request()->query())->onEachSide(1)->links() !!}
                                    </div>
                                @else
                                    <br>
                                    <div class="col-12 text-center">{{ __('No products!') }}</div>
                                @endif
                            </div>
                            
                            <aside class="widget" style="border: none">
                                {!! render_product_swatches_filter(['view' => Theme::getThemeNamespace() . '::views.ecommerce.attributes.attributes-filter-renderer']) !!}
                            </aside>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 order-lg-first mt-4 pt-2 mt-lg-0 pt-lg-0">
                    <div class="sidebar">
                        @include(Theme::getThemeNamespace() . '::views/ecommerce/includes/filters')
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <div class="row shop_container grid">
                        @if ($products->count() > 0)
                            @foreach($products as $product)
                                <div class="col-md-4 col-6">
                                    {!! Theme::partial('product-item-grid', compact('product')) !!}
                                </div>
                            @endforeach
                            <div class="mt-3 justify-content-center pagination_style1">
                                {!! $products->appends(request()->query())->onEachSide(1)->links() !!}
                            </div>
                        @else
                            <br>
                            <div class="col-12 text-center">{{ __('No products!') }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-lg-3 order-lg-first mt-4 pt-2 mt-lg-0 pt-lg-0">
                    <div class="sidebar">
                        @include(Theme::getThemeNamespace() . '::views/ecommerce/includes/filters')
                    </div>
                </div>
            </div>
        </div>
        @endif
    </form>
</div>
<!-- END SECTION SHOP -->

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var breadcrumbItems = document.querySelectorAll('ol.breadcrumb li.breadcrumb-item');
        if (breadcrumbItems.length > 3) {
            var allContainers = document.querySelectorAll('.shoppy-container');
            allContainers.forEach(function(container) {
                container.remove();
            });
            
            var productsSection = document.getElementById('products-section');
            productsSection.classList.remove('d-none');
        } else {
            var unwantedContainers = document.querySelectorAll('.shoppy-container:not(.child-category-{{ Arr::first(request()->query('categories', [])) }})');
            unwantedContainers.forEach(function(container) {
                container.remove();
            });
            
            var remainingContainers = document.querySelectorAll('.C-131 .shoppy-container');
            if (remainingContainers.length === 0) {
                var productsSection = document.getElementById('products-section');
                productsSection.classList.remove('d-none');
            }
        }
    });
</script>