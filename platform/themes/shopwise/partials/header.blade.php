<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=5, user-scalable=1" name="viewport"/>
        <!--<meta name="viewport" content="width=1024">-->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {!! BaseHelper::googleFonts('https://fonts.googleapis.com/css2?family=' . urlencode(theme_option('primary_font', 'Poppins')) . ':wght@200;300;400;500;600;700;800;900&display=swap') !!}
        
  
       
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
        </style>
       <link rel="stylesheet" type="text/css" media="all" href="{{url('themes/shopwise/plugins/owlcarousel/css/owl.theme.default.min.css')}}">
       <link rel="stylesheet" type="text/css" media="all" href="{{url('themes/shopwise/plugins/owlcarousel/css/owl.theme.css')}}">
       <link rel="stylesheet" type="text/css" media="all" href="{{url('themes/shopwise/plugins/owlcarousel/css/owl.carousel.min.css')}}">
       <link rel="stylesheet" type="text/css" media="all" href="{{url('themes/shopwise/plugins/bootstrap/css/bootstrap.min.css')}}">
       <link rel="stylesheet" type="text/css" media="all" href="{{url('themes/shopwise/css/magnific-popup.css')}}">
       <link rel="stylesheet" type="text/css" media="all" href="{{url('themes/shopwise/css/simple-line-icons.css')}}">
       <link rel="stylesheet" type="text/css" media="all" href="{{url('themes/shopwise/css/flaticon.css')}}">
       <link rel="stylesheet" type="text/css" media="all" href="{{url('themes/shopwise/css/linearicons.css')}}">
       <link rel="stylesheet" type="text/css" media="all" href="{{url('themes/shopwise/css/themify-icons.css')}}">
       <link rel="stylesheet" type="text/css" media="all" href="{{url('themes/shopwise/css/ionicons.min.css')}}">
       <link rel="stylesheet" type="text/css" media="all" href="{{url('themes/shopwise/css/animate.css')}}">
       <link rel="stylesheet" type="text/css" media="all" href="{{url('themes/shopwise/css/style.css?v=1.39.1')}}">
         <script data-pagespeed-no-defer="1" src="{{url('themes/shopwise/js/jquery-3.6.0.min.js')}}"></script>
      {!! Theme::header() !!}
       
    </head>
    <body>
    {!! apply_filters(THEME_FRONT_BODY, null) !!}

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

    <!-- START HEADER -->
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
                        <img class="logo_dark lazy" width="200" height="78" src="{{url('storage/blank.webp')}}" data-src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" alt="{{ theme_option('site_title') }}" />
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
                                <img class="lazy" width="200" height="78" src="{{url('storage/blank.webp')}}" data-src="{{ RvMedia::getImageUrl(theme_option('logo_footer') ? theme_option('logo_footer') : theme_option('logo')) }}" alt="{{ theme_option('site_title') }}" loading="lazy" />
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
