@php
    [$categories, $brands, $tags, $rand, $categoriesRequest, $urlCurrent, $categoryId, $maxFilterPrice] = EcommerceHelper::dataForFilter($category ?? null);
    $categories = ProductCategoryHelper::getActiveTreeCategories();
    Theme::asset()->usePath()->add('jquery-ui-css', 'css/jquery-ui.css');
    Theme::asset()->container('footer')->usePath()->add('jquery-ui-js', 'js/jquery-ui.js', ['jquery']);
    Theme::asset()->container('footer')->usePath()->add('touch-punch', 'js/jquery.ui.touch-punch.min.js', ['jquery-ui-js']);
    Theme::asset()->usePath()->add('custom-scrollbar-css', 'plugins/mcustom-scrollbar/jquery.mCustomScrollbar.css');
    Theme::asset()->container('footer')->usePath()->add('custom-scrollbar-js', 'plugins/mcustom-scrollbar/jquery.mCustomScrollbar.js', ['jquery']);
@endphp

@foreach($categories as $category)
<div class=" col-md-4 container_foto shoppy-container" style="padding: 5px;">
    <a href="{{ $category->url }}" class="category-link">
        <article class="text-left">
            <h2>{{ $category->name }}</h2>
        </article>
        <img src="{{ RvMedia::getImageUrl($category->image, null, false, RvMedia::getDefaultImage()) }}" alt="{{ $category->name }}" loading="lazy">
    </a>
</div>
@endforeach

<aside class="widget" style="border: none">
    {!! render_product_swatches_filter([
        'view' => Theme::getThemeNamespace() . '::views.ecommerce.attributes.attributes-filter-renderer'
    ]) !!}
</aside>