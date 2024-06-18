<div class="section py-5">
    <div class="container">
        <div class="row">
         


            <div class="col-md-6 d-flex align-items-center text-justify">
                
               <div class="w-100">
                   @if(BaseHelper::clean($shortcode->title))
                    <div class="heading_tab_header">
                    <div class="heading_s2">
                         <h4>{!! BaseHelper::clean($shortcode->title) !!}</h4>
                    </div>
                </div>
                    @endif
      
                {!! BaseHelper::clean($shortcode->description) !!}
               </div>

            </div>
            
               <div class="col-md-6 d-flex align-items-top">

              
                 
                    
        
                    {!! BaseHelper::clean($shortcode->description2) !!}
            </div>
        </div>

    </div>
</div>