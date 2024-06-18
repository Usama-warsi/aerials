<!-- START SECTION SHOP -->
<div class="section overflow-hidden">
    @foreach ($data as $key => $products)
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="heading_s3 text-center">
                        <h2>{!! BaseHelper::clean($shortcode->{$key . '_title'}) !!}</h2>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="product_slider carousel_slider owl-carousel owl-theme nav_style5" data-loop="true" data-dots="false" data-nav="true" data-margin="30" data-responsive='{"0":{"items": "1"}, "481":{"items": "2"}, "768":{"items": "3"}, "1199":{"items": "6"}}'>
                        @foreach($products as $product)
                            <div class="item">
                                {!! Theme::partial('product-item', ['product' => $product]) !!}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div> 
        </div>
    @endforeach
</div>
<!-- END SECTION SHOP -->