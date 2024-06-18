<div class="container tabby-container">
    <div class="row">
        <div class="col-12">
            <div class="heading_s3 text-center">
                <h2 class="tabby-title">{!! BaseHelper::clean($shortcode->title) !!}</h2>
            </div>
            <div class="text-center">
                <div class="tab-style2">
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#tabmenubar" aria-expanded="false">
                        <span class="ion-android-menu"></span>
                    </button>
                    <ul class="nav nav-tabs justify-content-center {{ Arr::get($attributes ?? [], 'class') }}" role="tablist">
                        @foreach ($collections as $collection)
                        <li class="nav-item">
                            <p>{{ $collection->id }}</p>
                            <p>{{ $collection->name }}</p>
                            <p>{{ $collection->slug }}</p>
                            <p>{{ $collectionId }}</p>
                            <a @class(['nav-link tabby-nav-links active','active' => $collection->slug == $collectionId,]) id="{{ $collection->slug }}-tab" data-toggle="tab" href="#{{ $collection->slug }}"
                            role="tab" aria-controls="{{ $collection->slug }}" aria-selected="true" @if ($collection->id == $collectionId) data-loaded="1" @endif data-ref="{{ $collection->slug }}"
                            data-url="{{ route('public.ajax.products', ['collection_id' => $collection->id, 'limit' => $limit]) }}">{{ $collection->name }}</a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            @foreach ($collections as $collection)
            <div class="tab_slider">
                <div @class(['tab-pane fade', 'show active' => $collection->id == $collectionId]) id="{{ $collection->slug }}" role="tabpanel" aria-labelledby="{{ $collection->slug }}-tab">
                    @if ($collection->id == $collectionId)
                        <div class="product_slider carousel_slider owl-carousel owl-theme nav_style1" data-loop="true" data-dots="false" data-nav="true" data-margin="10" data-responsive='{"0":{"items": "3"}, "481":{"items": "3"}, "768":{"items": "3"}, "1199":{"items": "6"}}'>
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
            </div>
            @endforeach
        </div>
    </div> 
</div>

<script type="text/x-custom-template" class="product-collection-items">
    <div class="product_slider carousel_slider owl-carousel owl-theme nav_style1" data-loop="true" data-dots="false" data-nav="true" data-margin="10" data-responsive='{"0":{"items": "3"}, "481":{"items": "3"}, "768":{"items": "3"}, "1199":{"items": "4"}}'>__data__</div>
</script>