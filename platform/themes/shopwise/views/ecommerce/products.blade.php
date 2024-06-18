@php Theme::set('pageName', __('Products')) @endphp
<div class="section">
    <form action="{{ URL::current() }}" method="GET">
        @if (request()->has('q'))
            <input type="hidden" name="q" value="{{ BaseHelper::stringify(request()->query('q')) }}">
        @endif
        @if (request()->has('categories'))
            <input type="hidden" name="categories[]" value="{{ Arr::first(request()->query('categories', [])) }}">
        @else
        <div class="container">
            <div class="row">
                <div class="col-lg-9">
                    <div class="row align-items-center mb-4 pb-1">
                        <div class="w-100 C-131 d-flex">
                            @include(Theme::getThemeNamespace() . '::views/ecommerce/includes/shop')
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
        @endif
    </form>
</div>
<!-- END SECTION SHOP -->