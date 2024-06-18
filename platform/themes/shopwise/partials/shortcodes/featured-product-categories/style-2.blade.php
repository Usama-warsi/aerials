<div class="container-fluid w-100  px-5">
    <div class="row">
        <div class="col-12">
            <div class="heading_tab_header">
                <div class="heading_s2">
                    <h2>{!! BaseHelper::clean($shortcode->title) !!}</h2>
                </div>
               
            </div>
        </div>
    </div>
    <div class="row">
         @foreach ($categories as $category)
                  
                        <div class="col-md-3 container_foto">
    <a href="{{ $category->url }}" class="">
                    <article class="text-left">

  
        <h2>{{ $category->name }}</h2>
        <!-- <h4>Descripción corta de la imagen en cuestión</h4> -->
            </article>
            <img src="{{ RvMedia::getImageUrl($category->image, null, false, RvMedia::getDefaultImage()) }}"
            alt="{{ $category->name }}" loading="lazy" > 
    </a>
</div>
       
                    @endforeach

    </div> 
</div>






