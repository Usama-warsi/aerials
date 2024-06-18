@php
    $logo = theme_option('logo_in_the_checkout_page') ?: theme_option('logo');
@endphp

@if ($logo)
    <div class="row">
        <div class="checkout-logo col-6">
            <a href="{{ BaseHelper::getHomepageUrl() }}" title="{{ theme_option('site_title') }}">
                <img src="{{ RvMedia::getImageUrl($logo) }}" alt="{{ theme_option('site_title') }}" />
            </a>
        </div>
        
        <div class="backtocart-col col-6">
            <a class="back-tocartbtn" href="{{ route('public.cart') }}">
                <x-core::icon name="ti ti-arrow-narrow-left" />
                <span class="d-inline-block back-to-cart">{{ __('Back to cart') }}</span>
            </a>
        </div>
    </div>
    
    <hr>
@endif