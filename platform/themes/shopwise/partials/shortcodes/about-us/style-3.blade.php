<div class="section py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6 d-flex align-items-top">

              
                 
                    
        
                    <img width="@if(!empty(BaseHelper::clean($shortcode->iwidth))){!! BaseHelper::clean($shortcode->iwidth) !!} @else {{'100%'}} @endif" height="@if(!empty(BaseHelper::clean($shortcode->iheight))){!! BaseHelper::clean($shortcode->iheight) !!} @else {{'100%'}} @endif" src="{{ RvMedia::getImageUrl($shortcode->image, null, false, RvMedia::getDefaultImage()) }}" />

              
            </div>


            <div class="col-md-6 d-flex align-items-center text-justify">
                
               <div class="w-100">
                    <div class="heading_tab_header">
                    <div class="heading_s2">
                         <h4>{!! BaseHelper::clean($shortcode->title) !!}</h4>
                    </div>
                </div>
                    
      
                {!! BaseHelper::clean($shortcode->description) !!}
               </div>

            </div>
        </div>

    </div>
</div>