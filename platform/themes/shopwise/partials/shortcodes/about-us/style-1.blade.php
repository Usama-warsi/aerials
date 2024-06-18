<div class="section p-0 m-0 overflow-hidden">
    <div class="w-100">
        <div class="row">
            <div class="col-12">
                <div class="heading_s3 text-center">
                    <h2>{!! BaseHelper::clean($shortcode->title) !!}</h2>
                </div>
            </div>
        </div>
        <div class="container mt-mb-20">
            <div class="row align-items-center">
                <div class="col-lg-6 col-md-12">
                    <div class="w-100">
                        <iframe width="@if(!empty(BaseHelper::clean($shortcode->vwidth))){!! BaseHelper::clean($shortcode->vwidth) !!} @else {{'100%'}} @endif" height="@if(!empty(BaseHelper::clean($shortcode->vheight))){!! BaseHelper::clean($shortcode->vheight) !!} @else {{'100%'}} @endif" src="{!! BaseHelper::clean($shortcode->video) !!}" frameborder="0" allowfullscreen></iframe>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 text-justify">
                    <div class="paragraph-indent">
                        {!! BaseHelper::clean($shortcode->description) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>