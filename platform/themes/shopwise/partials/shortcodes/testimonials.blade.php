<div class="bg_redon overflow-hidden">
    <div class="row justify-content-center">
        <div class="w-100">
            <div class="heading_s3 text-center">
                <h2>{!! BaseHelper::clean($title) !!}</h2>
            </div>
        </div>
    </div>
    <div class="container pt-pb-20">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="testimonial_wrap testimonial_style1 carousel_slider owl-carousel owl-theme nav_style2" data-nav="true" data-dots="false" data-center="true" data-loop="true" data-autoplay="true" data-items='1'>
                    @foreach($testimonials as $testimonial)
                    <div class="testimonial_box">
                        <div class="testimonial_desc">
                            <p>{!! BaseHelper::clean($testimonial->content) !!}</p>
                        </div>
                        <div class="author_wrap">
                            <div class="author_img">
                                <img class="lazy" width="60" height="60" src="{{ RvMedia::getImageUrl($testimonial->image, null, false, RvMedia::getDefaultImage()) }}" alt="{{ $testimonial->name }}" loading="lazy" />
                            </div>
                            <div class="author_name">
                                <h6>{{ $testimonial->name }}</h6>
                                <span>{{ $testimonial->company }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>