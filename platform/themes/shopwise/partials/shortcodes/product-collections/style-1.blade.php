<div class="tabby-container">
    
    <div class="row">
        <div class="w-100">
            <div class="heading_s3 text-center">
                <h2 class="tabby-title">{!! BaseHelper::clean($shortcode->title) !!}</h2>
            </div>
            
            <div class="text-center">
                <div class="tab-style2 d-none">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#tabmenubar" aria-expanded="false"> 
                        <span class="ion-android-menu"></span>
                    </button>
                    <ul class="tabmenubar nav nav-tabs justify-content-center {{ Arr::get($attributes ?? [], 'class') }}" id="tabmenubar" role="tablist">
                        @foreach ($collections as $collection)
                        @if($collection->id == BaseHelper::clean($shortcode->collection))
                        <li class="nav-item">
                            <a @class([
                                'nav-link tabby-nav-links active', 'nada' => $collection->slug == $collectionId,
                            ]) id="{{ $collection->slug }}-tab" data-toggle="tab" href="#{{ $collection->slug }}"
                            role="tab" aria-controls="{{ $collection->slug }}" aria-selected="true"
                            @if ($collection->id == $collectionId) data-loaded @endif data-ref="{{ $collection->slug }}"
                            data-url="{{ route('public.ajax.products', ['collection_id' => $collection->id, 'limit' => $limit]) }}">{{ $collection->name }}</a>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-mb-20">
        <div class="container col-12">
            <div class="tab_slider">
                @foreach ($collections as $collection)
                 @if($collection->id == BaseHelper::clean($shortcode->collection))
                    <div @class(['tab-pane fade show active','nada' => $collection->id == $shortcode->collection]) id="{{ $collection->slug }}" role="tabpanel" aria-labelledby="{{ $collection->name }}">
                        @if ($collection->id == $shortcode->collection)
                            <div class="product_slider carousel_slider owl-carousel owl-theme nav_style1" data-loop="true" data-dots="false" data-nav="true" data-margin="5" data-responsive='{"0":{"items": "3"}, "481":{"items": "3"}, "768":{"items": "3"}, "1199":{"items": "6"}}'>
                                @foreach($products as $product)
                                    <div class="item">
                                        {!! Theme::partial('product-item', compact('product')) !!}
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="half-circle-spinner">
                                <div class="circle circle-1"></div>
                                <div class="circle circle-2"></div>
                            </div>
                        @endif
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    
</div>
@push('footer')


<script type="text/x-custom-template" class="product-collection-items">
    <div class="product_slider carousel_slider owl-carousel owl-theme nav_style1" data-loop="true" data-dots="false" data-nav="true" data-margin="5" data-responsive='{"0":{"items": "3"}, "481":{"items": "3"}, "768":{"items": "3"}, "1199":{"items": "6"}}'>__data__</div>
</script>

<script>
$(document).ready(function() {
    $('ul.tabmenubar a.tabby-nav-links').each(function() {
        $(this).click();
    });
    
 
});

</script>
@endpush