<div class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="heading_tab_header">
                    <div class="heading_s2">
                        <h4>{!! BaseHelper::clean($shortcode->title) !!}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div
                     class="product_slider carousel_slider owl-carousel owl-theme nav_style3" data-loop="false"
                     data-dots="false" data-nav="true" data-margin="30"
                     data-responsive='{"0":{"items": "2"}, "650":{"items": "2"}, "1199":{"items": "2"}}'>
                    @foreach ($flashSales as $flashSale)
                        @foreach ($flashSale->products as $product)
                            @continue (! EcommerceHelper::showOutOfStockProducts() && $product->isOutOfStock())

                            <div class="item">
                                {!! Theme::partial('flash-sale-product', compact('product', 'flashSale')) !!}
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>