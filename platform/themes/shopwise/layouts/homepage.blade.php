<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1" name="viewport"/>
        <!--<meta name="viewport" content="width=1024">-->
        <meta name="csrf-token" content="{{ csrf_token() }}">
       
        <style>
            :root {
                --color-1st: {{ theme_option('primary_color', '#FF324D') }};
                --primary-color: {{ theme_option('primary_color', '#FF324D') }};
                --color-2nd: {{ theme_option('secondary_color', '#1D2224') }};
                --secondary-color: {{ theme_option('secondary_color', '#1D2224') }};
                --primary-font: '{{ theme_option('primary_font', 'Poppins') }}', sans-serif;
            }
            body{
                overflow-x:hidden;
            }
            #preload-remove{
             width:100%;
             height:90vh;
             display:flex;
             justify-content:center;
             align-items:center;
             background:#fff;
            }
             #preload-remove p{
                 position:absolute;
                 top:10px;
                 right:10%;
                 font-size:1rem;
                 font-family:arial;
                 background:#a81717;
                 padding:0.7rem 1rem;
                 border-radius:50%;
                 color:#fff;
             }
        </style>
      {!! Theme::header() !!}
       
    </head>
    <body>
   

<div id="append-div" class="m-0 p-0">
   <div id="preload-remove">
        <svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="238.000000pt" height="78.000000pt" viewBox="0 0 238.000000 78.000000" preserveAspectRatio="xMidYMid meet">

<g transform="translate(0.000000,78.000000) scale(0.100000,-0.100000)" fill="#000000" stroke="none">
<path d="M321 742 c-53 -26 -75 -63 -75 -125 -1 -40 4 -59 22 -81 36 -47 29 -84 -27 -149 -47 -54 -66 -92 -73 -147 -6 -50 -94 -35 -100 17 -2 21 -9 29 -27 31 -22 4 -24 1 -18 -34 4 -21 17 -48 29 -60 30 -29 245 -151 281 -160 24 -5 27 -3 27 20 0 18 -5 26 -17 26 -34 0 -84 40 -103 83 -26 57 -25 106 3 148 26 39 57 47 89 25 29 -20 35 -20 40 2 3 12 -2 24 -13 31 -15 9 -19 23 -19 65 0 64 15 81 64 73 26 -4 36 -1 41 10 9 24 -14 34 -68 30 -63 -5 -91 15 -91 67 0 42 41 92 85 101 34 8 95 -19 111 -48 10 -20 11 -20 23 4 12 20 11 27 -3 43 -41 45 -123 58 -181 28z"/>
<path d="M550 687 c0 -3 -33 -86 -74 -186 l-75 -182 46 3 c45 3 47 4 61 46 l14 42 63 0 64 0 12 -41 c14 -46 22 -52 72 -47 l35 3 -67 180 -68 180 -41 3 c-23 2 -42 1 -42 -1z m64 -164 l15 -53 -40 0 c-21 0 -39 3 -39 6 0 24 34 114 40 107 5 -4 15 -32 24 -60z"/>
<path d="M830 505 l0 -185 115 0 115 0 0 30 0 30 -75 0 -75 0 0 50 0 50 65 0 65 0 0 30 0 30 -65 0 -65 0 0 40 0 40 70 0 70 0 0 35 0 35 -110 0 -110 0 0 -185z"/>
<path d="M1130 505 l0 -185 45 0 44 0 3 73 3 72 55 -72 c54 -73 55 -73 103 -73 55 0 55 -4 -11 84 -43 56 -44 60 -27 72 10 8 22 14 27 14 13 0 38 58 38 89 0 83 -46 111 -184 111 l-96 0 0 -185z m193 93 c8 -12 7 -24 -1 -42 -10 -22 -18 -26 -57 -26 l-45 0 0 46 0 45 46 -3 c31 -2 51 -9 57 -20z"/>
<path d="M1500 505 l0 -185 40 0 40 0 0 185 0 185 -40 0 -40 0 0 -185z"/>
<path d="M1727 523 c-36 -93 -69 -176 -73 -186 -6 -16 -2 -18 36 -15 41 3 44 5 58 46 l14 43 66 -3 65 -3 15 -42 c13 -42 15 -43 59 -43 32 0 44 4 41 13 -3 6 -34 90 -69 184 l-64 173 -41 0 -40 0 -67 -167z m138 -34 c6 -17 1 -19 -35 -19 -41 0 -42 1 -35 27 3 15 13 43 22 62 l16 36 13 -44 c7 -24 16 -52 19 -62z"/>
<path d="M2078 505 l-3 -185 118 0 117 0 0 30 0 29 -72 3 -73 3 -3 153 -3 152 -39 0 -39 0 -3 -185z"/>
<path d="M400 155 l0 -125 73 2 72 1 -60 6 -60 6 -3 53 -3 52 58 2 58 1 -55 6 -55 6 -3 48 -3 47 60 0 c34 0 61 4 61 10 0 6 -30 10 -70 10 l-70 0 0 -125z"/>
<path d="M612 264 c-31 -22 -29 -67 4 -93 15 -12 32 -21 39 -21 24 0 65 -41 65 -65 0 -33 -33 -48 -86 -42 -50 5 -60 -8 -13 -18 39 -9 93 1 109 20 6 7 10 31 8 52 -3 35 -7 40 -56 64 -71 34 -78 43 -64 73 12 27 42 36 85 25 20 -6 27 -4 27 7 0 20 -89 19 -118 -2z"/>
<path d="M814 240 l-26 -39 21 -29 c18 -26 19 -31 6 -42 -8 -7 -15 -23 -15 -35 0 -27 45 -85 66 -85 7 0 26 18 41 41 26 39 26 41 10 69 -16 26 -16 30 0 58 17 29 17 31 -10 66 -36 47 -60 46 -93 -4z m81 -10 c8 -16 15 -31 15 -35 0 -3 -22 -5 -50 -5 -27 0 -50 3 -50 6 0 15 43 64 55 64 8 0 21 -13 30 -30z m12 -136 c2 -7 -7 -25 -21 -40 l-26 -29 -20 25 c-11 14 -20 33 -20 43 0 15 7 18 42 15 23 -2 43 -8 45 -14z"/>
<path d="M990 155 l0 -125 73 2 72 1 -60 6 -60 6 -3 53 -3 52 58 2 58 1 -55 6 -55 6 -3 48 -3 47 60 0 c34 0 61 4 61 10 0 6 -30 10 -70 10 l-70 0 0 -125z"/>
<path d="M1190 155 c0 -77 4 -125 10 -125 6 0 10 42 10 107 0 64 4 103 9 98 5 -6 32 -54 61 -108 38 -71 57 -97 71 -97 18 0 19 9 19 125 0 158 -18 168 -22 13 l-3 -113 -60 112 c-83 156 -95 154 -95 -12z"/>
<path d="M1420 271 c0 -6 17 -11 38 -13 l37 -3 3 -113 c4 -149 22 -147 22 3 l0 115 35 0 c19 0 35 5 35 10 0 6 -35 10 -85 10 -47 0 -85 -4 -85 -9z"/>
<path d="M1640 155 c0 -77 4 -125 10 -125 6 0 10 48 10 125 0 77 -4 125 -10 125 -6 0 -10 -48 -10 -125z"/>
<path d="M1775 188 c-21 -51 -45 -108 -52 -125 -11 -28 -11 -33 1 -33 8 0 17 13 21 29 9 42 27 53 81 49 44 -3 48 -5 59 -37 6 -19 17 -37 24 -39 15 -5 13 2 -39 143 -21 54 -42 101 -47 102 -6 2 -27 -38 -48 -89z m68 -1 c9 -30 17 -57 17 -60 0 -4 -21 -7 -46 -7 -40 0 -45 2 -39 18 4 9 14 36 22 60 8 23 18 42 22 42 4 0 15 -24 24 -53z"/>
<path d="M1980 155 l0 -125 68 2 67 2 -57 3 -58 4 0 119 c0 73 -4 120 -10 120 -6 0 -10 -48 -10 -125z"/>
<path d="M2182 268 c-27 -14 -38 -49 -22 -77 6 -12 35 -32 64 -45 59 -26 70 -42 52 -77 -10 -20 -20 -24 -61 -25 -67 -2 -73 -4 -50 -14 41 -18 95 -11 120 15 45 44 22 95 -56 124 -38 15 -44 20 -44 46 0 38 39 60 79 45 24 -9 43 0 29 14 -10 10 -86 6 -111 -6z"/>
</g>
</svg>
        <p>x</p>
   </div>
</div>


  

  
@stack('footer')
    <script>
        window.trans = {
            "No reviews!": "{{ __('No reviews!') }}",
            "Days": "{{ __('Days') }}",
            "Hours": "{{ __('Hours') }}",
            "Minutes": "{{ __('Minutes') }}",
            "Seconds": "{{ __('Seconds') }}",
        };

        window.siteUrl = "{{ route('public.index') }}";
    </script>

 <script>

    var isloading = true;

        
    function removeLoading() {
        if (isloading) {
            var head = document.head;
            var body = document.body;
            var appendDiv = document.getElementById('append-div');
  
            var stylesheets = [
                "{{url('themes/shopwise/css/style.css?v=1.39.1')}}",
                "{{url('themes/shopwise/css/animate.css')}}",
                "{{url('themes/shopwise/css/ionicons.min.css')}}",
                "{{url('themes/shopwise/css/themify-icons.css')}}",
                "{{url('themes/shopwise/css/linearicons.css')}}",
                "{{url('themes/shopwise/css/flaticon.css')}}",
                "{{url('themes/shopwise/css/simple-line-icons.css')}}",
                "{{url('themes/shopwise/css/magnific-popup.css')}}",
                "{{url('themes/shopwise/plugins/bootstrap/css/bootstrap.min.css')}}",
                "{{url('themes/shopwise/plugins/owlcarousel/css/owl.carousel.min.css')}}",
                "{{url('themes/shopwise/plugins/owlcarousel/css/owl.theme.css')}}",
                "{{url('themes/shopwise/plugins/owlcarousel/css/owl.theme.default.min.css')}}"
              
            ];
           
            var scripts = [
                 "{{url('themes/shopwise/js/magnific-popup.min.js')}}",
                "{{url('themes/shopwise/plugins/slick/slick.min.js')}}",
                "{{url('themes/shopwise/js/popper.min.js')}}",
                "{{url('themes/shopwise/plugins/bootstrap/js/bootstrap.min.js')}}",
                "{{url('themes/shopwise/js/waypoints.min.js?v=4.0.1')}}",
                "{{url('themes/shopwise/plugins/owlcarousel/js/owl.carousel.min.js')}}",
                "{{url('themes/shopwise/js/jquery.elevatezoom.js')}}",
                "{{url('themes/shopwise/js/jquery.countdown.min.js')}}",
                "{{url('themes/shopwise/js/scripts.js?v=1.39.1')}}",
                "{{url('themes/shopwise/js/backend.js?v=1.39.1')}}",
               
            ];
            
            
         
              stylesheets.forEach(function(href) {
                var link = document.createElement('link');
                link.rel = 'stylesheet';
                link.type = 'text/css';
                link.media = 'all';
                link.href = href;
                head.insertBefore(link, head.firstChild);
            });
            
          var   script = document.createElement('script');
                script.src = '{{ asset('vendor/core/packages/theme/js/toast.js') }}';
                script.defer = true;
                head.insertBefore(script,head.firstChild);
    script = document.createElement('script');
                script.src = '{{url('themes/shopwise/js/jquery-3.6.0.min.js')}}';
                script.defer = true;
                head.insertBefore(script,head.firstChild);
                
             
   var newContent =`
    <div id="alert-container"></div>

    @if (is_plugin_active('newsletter') && theme_option('enable_newsletter_popup', 'yes') === 'yes')
        <div data-session-domain="{{ config('session.domain') ?? request()->getHost() }}"></div>
        <!-- Home Popup Section -->
        <div class="modal fade subscribe_popup" id="newsletter-modal" data-time="{{ (int)theme_option('newsletter_show_after_seconds', 10) * 1000 }}" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true"><i class="ion-ios-close-empty"></i></span>
                        </button>
                        <div class="row no-gutters">
                            <div class="col-sm-5">
                                @if (theme_option('newsletter_image'))
                                    <div class="background_bg h-100" data-img-src="{{ RvMedia::getImageUrl(theme_option('newsletter_image')) }}"></div>
                                @endif
                            </div>
                            <div class="col-sm-7">
                                <div class="popup_content">
                                    <div class="popup-text">
                                        @if (theme_option('newsletter_text'))
                                        <div class="heading_s4">
                                            <h4>{!! theme_option('newsletter_text') !!}</h4>
                                        </div>
                                        @endif
                                        <p>Join our community to receive<br>updates and special sales!</p>
                                    </div>
                                    <form method="post" action="{{ route('public.newsletter.subscribe') }}" class="newsletter-form">
                                        @csrf
                                        <div class="form-group">
                                            <input name="email" type="email" class="form-control rounded-0" placeholder="{{ __('Enter Your Email') }}">
                                        </div>

                                        @if (setting('enable_captcha') && is_plugin_active('captcha'))
                                            <div class="form-group">
                                                {!! Captcha::display() !!}
                                            </div>
                                        @endif

                                        <div class="chek-form text-left form-group">
                                            <div class="custome-checkbox">
                                                <input class="form-check-input" type="checkbox" name="dont_show_again" id="dont_show_again" value="">
                                                <label class="form-check-label" for="dont_show_again"><span>{{ __("Please don't show this popup again.") }}</span></label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <button class="btn btn-block text-uppercase rounded-0" type="submit" style="background: #333; color: #fff;">{{ __('Subscribe') }}</button>
                                        </div>

                                        <div class="form-group">
                                            <div class="newsletter-message newsletter-success-message" style="display: none"></div>
                                            <div class="newsletter-message newsletter-error-message" style="display: none"></div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Screen Load Popup Section -->
    @endif

    <header class="header_wrap @if (Theme::get('transparentHeader')) dd_dark_skin transparent_header @endif">
        <!--<div class="top-header d-none d-md-block">-->
        <!--    <div class="container">-->
        <!--        <div class="row align-items-center">-->
        <!--                <div class="col-md-6">-->
        <!--                    <div class="d-flex align-items-center justify-content-center justify-content-md-start">-->
        <!--                        @if (is_plugin_active('language'))-->
        <!--                            <div class="language-wrapper">-->
        <!--                                {!! Theme::partial('language-switcher') !!}-->
        <!--                            </div>-->
        <!--                        @endif-->
                               
        <!--                        @if (theme_option('hotline'))-->
        <!--                            <ul class="contact_detail text-center text-lg-left">-->
        <!--                                <li><i class="ti-mobile"></i><span>{{ theme_option('hotline') }}</span></li>-->
        <!--                            </ul>-->
        <!--                        @endif-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--            <div class="col-md-6">-->
        <!--                <div class="d-flex align-items-center justify-content-center justify-content-md-end">-->
        <!--                    @if (is_plugin_active('ecommerce'))-->
        <!--                        <ul class="header_list">-->
        <!--                            @if (EcommerceHelper::isCompareEnabled())-->
        <!--                                <li><a href="{{ route('public.compare') }}"><i class="ti-control-shuffle"></i><span>{{ __('Compare') }}</span></a></li>-->
        <!--                            @endif-->
                                   
        <!--                        </ul>-->
        <!--                    @endif-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->
        <div class="middle-header dark_skin">
            <div class="container">
                <div class="nav_block">
                    <a class="navbar-brand " href="{{ route('public.index') }}">
                        <img class="logo_dark" width="200" height="78" src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}" />
                    </a>
                   
                    @if (is_plugin_active('ecommerce'))
                        <div class="product_search_form">
                            <form action="{{ route('public.products') }}" data-ajax-url="{{ route('public.ajax.search-products') }}" method="GET">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="custom_select">
                                            <select name="categories[]" class="first_null product-category-select">
                                                <option value="">{{ __('All') }}</option>
                                                {!! ProductCategoryHelper::renderProductCategoriesSelect() !!}
                                            </select>
                                        </div>
                                    </div>
                                    <input class="form-control input-search-product" name="q" value="{{ BaseHelper::stringify(request()->query('q')) }}" placeholder="{{ __('Search Product') }}..." required  type="text">
                                    <button type="submit" class="search_btn"><i class="linearicons-magnifier"></i></button>
                                </div>
                                <div class="panel--search-result"></div>
                            </form>
                        </div>
                    @endif
                     
                    @if (theme_option('hotline'))
                        <div class="contact_phone ">
                            <a href="tel:{{ theme_option('hotline') }}">
                                <i class="linearicons-phone-wave"></i>
                                <span>{{ theme_option('hotline') }}</span>
                            </a>
                        </div>
                    @endif
                    @if (is_plugin_active('ecommerce'))
                                    @php $currencies = get_all_currencies(); @endphp
                                    @if (count($currencies) > 1)
                                        <div class="language-wrapper choose-currency mr-3">
                                            <div class="dropdown">
                                                <button class="btn btn-secondary dropdown-toggle btn-select-language" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                    {{ get_application_currency()->title }}
                                                    <span class="language-caret"></span>
                                                </button>
                                                <ul class="dropdown-menu language_bar_chooser" >
                                                    @foreach ($currencies as $currency)
                                                        <li>
                                                            <a href="{{ route('public.change-currency', $currency->title) }}" @if (get_application_currency_id() == $currency->id) class="active" @endif><span class="m-auto">{{ $currency->title }}</span></a>
                                                        </li>
                                                    @endforeach
                                                   
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                </div>
            </div>
        </div>
        <div class="bottom_header light_skin main_menu_uppercase @if (! Theme::get('transparentHeader')) bg_dark @endif @if (url()->current() === route('public.index')) mb-0 @endif">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-4 col-sm-6 col-4">
                        @if (is_plugin_active('ecommerce'))
                            <div class="categories_wrap">
                                <button type="button" data-toggle="collapse" data-target="#navCatContent" aria-expanded="false" class="categories_btn">
                                    <i class="linearicons-menu"></i><span>{{ __('All Categories') }} </span>
                                </button>
                                @php
                                    $categories = ProductCategoryHelper::getProductCategoriesWithUrl();
                                @endphp
                                    <div id="navCatContent" class="@if (Theme::get('collapsingProductCategories')) nav_cat @endif navbar collapse">
                                        <ul>
                                            {!! Theme::partial('product-categories-dropdown', ['categories' => $categories]) !!}
                                        </ul>
                                    @if (count($categories) > 100)
                                        <div class="more_categories">{{ __('More Categories') }}</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-9 col-md-8 col-sm-6 col-8">
                        @include(Theme::getThemeNamespace('partials.header-menu'))
                    </div>
                </div>
            </div>
        </div>

        @if (theme_option('enable_sticky_header', 'yes') == 'yes')
            <div class="bottom_header bottom_header_sticky light_skin main_menu_uppercase bg_dark fixed-top header_with_topbar d-none">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-md-4 col-sm-6 col-4">
                            <a class="navbar-brand" href="{{ route('public.index') }}">
                                <img class="" width="200" height="78" src="{{ RvMedia::getImageUrl(theme_option('logo_footer') ? theme_option('logo_footer') : theme_option('logo')) }}" alt="{{ theme_option('site_title') }}" loading="lazy" />
                            </a>
                        </div>
                        <div class="col-lg-9 col-md-8 col-sm-6 col-8">
                            @include(Theme::getThemeNamespace('partials.header-menu'))
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </header>
{!! Theme::content() !!}   <footer class="footer_dark">
        <div class="footer_top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-5 col-md-6 col-sm-12">
                        <div class="widget">
                            @if (theme_option('logo_footer') || theme_option('logo'))
                                <div class="footer_logo">
                                    <a href="{{ route('public.single') }}">
                                        <img  class="lazy" width="238" height="78" src="{{ RvMedia::getImageUrl(theme_option('logo_footer') ? theme_option('logo_footer') : theme_option('logo')) }}" alt="{{ theme_option('site_title') }}" />
                                    </a>
                                </div>
                            @endif
                            <p>{!! theme_option('about-us') !!}</p>
                        </div>
                        @if (theme_option('social_links'))
                            <div class="widget">
                                <ul class="social_icons social_white">
                                    @foreach(json_decode(theme_option('social_links'), true) as $socialLink)
                                        @if (count($socialLink) == 4)
                                            <li>
                                                <a href="{{ $socialLink[2]['value'] }}"
                                                   title="{{ $socialLink[0]['value'] }}" style="background-color: {{ $socialLink[3]['value'] }}; border: 1px solid {{ $socialLink[3]['value'] }};" target="_blank">
                                                    <i class="{{ $socialLink[1]['value'] }}"></i>
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                    {!! dynamic_sidebar('footer_sidebar') !!}
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="widget">
                            <h6 class="widget_title">{{ __('Contact Info') }}</h6>
                            <ul class="contact_info contact_info_light">
                                @if (theme_option('address'))
                                @php 
                                $string = theme_option('address');
                                    $clean_string = strip_tags($string);

                                @endphp
                                
                                    <li>
                                        <i class="ti-location-pin"></i>
                                        <p><a href="https://www.google.com/maps/place/{!! $clean_string !!}" target="_blank">{!! theme_option('address') !!}</a></p>
                                    </li>
                                @endif
                                @if (theme_option('email'))
                                    <li>
                                        <i class="ti-email"></i>
                                        <a href="mailto:{{ theme_option('email') }}">{{ theme_option('email') }}</a>
                                    </li>
                                @endif
                                @if (theme_option('hotline'))
                                    <li>
                                        <i class="ti-mobile"></i>
                                        <p><a href="tel:{{ theme_option('hotline') }}">{{ theme_option('hotline') }}</a></p>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bottom_footer border-top-tran">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-md-0 text-center text-md-left">{{ theme_option('copyright') }}</p>
                    </div>
                    <div class="col-md-6">
                        <ul class="footer_payment text-center text-lg-right">
                            @foreach(json_decode(theme_option('payment_methods', []), true) as $method)
                                @if (!empty($method))
                                    <li><img class="" width="49" height="32" src="{{ RvMedia::getImageUrl($method) }}" alt="payment method" loading="lazy" /></li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
     @if (is_plugin_active('ecommerce') && EcommerceHelper::isCartEnabled())
         <div id="remove-item-modal" class="modal" tabindex="-1" role="dialog">
             <div class="modal-dialog modal-dialog-centered" role="document">
                 <div class="modal-content">
                     <div class="modal-header">
                         <h5 class="modal-title">{{ __('Warning') }}</h5>
                         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                             <span aria-hidden="true">&times;</span>
                         </button>
                     </div>
                     <div class="modal-body">
                         <p>{{ __('Are you sure you want to remove this product from cart?') }}</p>
                     </div>
                     <div class="modal-footer">
                         <button type="button" class="btn btn-fill-out" data-dismiss="modal">{{ __('Cancel') }}</button>
                         <button type="button" class="btn btn-fill-line confirm-remove-item-cart">{{ __('Yes, remove it!') }}</button>
                     </div>
                 </div>
             </div>
         </div>
     @endif

    <a href="#" class="scrollup" style="display: none;"><i class="ion-ios-arrow-up"></i></a>
    `;
  appendDiv.innerHTML = newContent;

 
            scripts.forEach(function(src) {
                var script = document.createElement('script');
                script.src = src;
                script.defer = true;
                body.appendChild(script);
            });

           
            isloading = false;
        }
    }

    document.addEventListener('mousemove', removeLoading);
    document.addEventListener('scroll', removeLoading);
    document.addEventListener('touchmove', removeLoading);
    setInterval(removeLoading, 8000);
    
   
</script>
{!! Theme::footer() !!}

    @if (session()->has('success_msg') || session()->has('error_msg') || (isset($errors) && $errors->count() > 0) || isset($error_msg))
        <script type="text/javascript">
            window.onload = function () {
                @if (session()->has('success_msg'))
                    window.showAlert('alert-success', '{{ session('success_msg') }}');
                @endif

                @if (session()->has('error_msg'))
                    window.showAlert('alert-danger', '{{ session('error_msg') }}');
                @endif

                @if (isset($error_msg))
                    window.showAlert('alert-danger', '{{ $error_msg }}');
                @endif

                @if (isset($errors))
                    @foreach ($errors->all() as $error)
                        window.showAlert('alert-danger', '{!! $error !!}');
                    @endforeach
                @endif
            };
        </script>
    @endif

    </body>
</html>

