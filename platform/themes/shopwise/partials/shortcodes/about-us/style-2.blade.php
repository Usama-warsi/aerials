<div class="section py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-md-12 d-flex align-items-top">
                <div class="w-100">
                    <iframe width="@if(!empty(BaseHelper::clean($shortcode->vwidth))){!! BaseHelper::clean($shortcode->vwidth) !!} @else {{'100%'}} @endif" height="@if(!empty(BaseHelper::clean($shortcode->vheight))){!! BaseHelper::clean($shortcode->vheight) !!} @else {{'100%'}} @endif" poster="{{url('storage/blank.webp')}}" class="lazy" data-src="{!! BaseHelper::clean($shortcode->video) !!}" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 d-flex align-items-center text-justify">
                <div class="paragraph-indent">
                    {!! BaseHelper::clean($shortcode->description) !!}
                </div>
            </div>
        </div>
    </div>
</div>