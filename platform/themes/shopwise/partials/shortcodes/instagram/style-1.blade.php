<!-- START SECTION INSTAGRAM IMAGE -->

<style>
    .insta-inner{
        position:absolute;
        top:0;
        left:0;
        background:#0009;
        height:100%;
        width:100%;
        text-align:center;
        padding:10%;
        color:#fff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    opacity:0;
    transition:0.5s ease;
    }
     .insta-inner p{
         margin-top:12px;
         line-height:16px;
         font-size:16px;
     }
      .insta-inner:hover{
           opacity:1;
      }
</style>
<div class="">
    <div class="p-0">
        <div class="row">
            <div class="col-12 text-center">
                <div class="heading_s3 text-center">
                    <h2>{!! BaseHelper::clean($shortcode->title) !!}</h2>
                    <a href="https://www.instagram.com/{{ BaseHelper::clean($shortcode->username) }}" target="_blank"><h5 class="text-white m-0">{!! '@'.BaseHelper::clean($shortcode->username) !!}</h5></a>
                </div>
            </div>
        </div>
        <div class=" row no-gutters">
            <div class="w-100 text-center">
               
             <div class="instaslider carousel_slider owl-carousel owl-theme nav_style1 text-dark" data-loop="true" data-dots="false" data-nav="true" data-margin="0" data-responsive='{"0":{"items": "2"}, "481":{"items": "2"}, "768":{"items": "2"}, "1024":{"items": "3"}, "1199":{"items": "4"}}'>
                                @foreach($contents["data"] as $post)
         
       @php
        $username = isset($post["username"]) ? $post["username"] : "";
        $caption = isset($post["caption"]) ? $post["caption"] : "";
        $media_url = isset($post["media_url"]) ? $post["media_url"] : "";
        $permalink = isset($post["permalink"]) ? $post["permalink"] : "";
        $media_type = isset($post["media_type"]) ? $post["media_type"] : "";
       
       @endphp
        
      <div class="item ">
    <div class="insta-img">
        @if($media_type=="VIDEO")
        <video controls style='width:100%; display: block !important;'>
            <source src='{{$media_url}}' type='video/mp4'>
            Your browser does not support the video tag.
        </video>
        @else
        <img  src="{{$media_url}}" alt="{{'@'.$username}}" />
        @endif
    </div>
    <a href='{{$permalink}}' target="_blank" nofollow class="insta-inner">
       
        <h4 class="text-white">{{'@'.$username}}</h4>
        <p class="text-white">{{ Str::limit($caption, 100) }}</p>
      
           
    </a>

</div>
        
        @endforeach
                                    
                              
                                    
                            </div>
            </div>
        </div>

    </div>
</div>

<!-- END SECTION INSTAGRAM IMAGE -->