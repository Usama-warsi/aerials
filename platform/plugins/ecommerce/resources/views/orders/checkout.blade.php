@extends('plugins/ecommerce::orders.master')
@section('title', __('Checkout'))
@section('content')
    @if (Cart::instance('cart')->isNotEmpty())
        @php
            $rawTotal = Cart::instance('cart')->rawTotal();
            $b = session('shipmethod');
            $orderAmount = max($rawTotal + $shippingAmount - $promotionDiscountAmount - $couponDiscountAmount, 0);
        @endphp
        @if (is_plugin_active('payment') && $orderAmount)
            @include('plugins/payment::partials.header')
        @endif
        <x-core::form :url="route('public.checkout.process', $token)" id="checkout-form" class="checkout-form payment-checkout-form">
            <input id="checkout-token" name="checkout-token" type="hidden" value="{{ $token }}">
            <input name="shipping_amount" type="hidden" value="{{ $shippingAmount }}">
            <div class="container" id="main-checkout-product-info">
                <div class="row">
                    <div class="order-1 order-md-2 col-lg-5 col-md-6 right">
                        <div class="d-block d-sm-none">
                            @include('plugins/ecommerce::orders.partials.logo')
                        </div>
                        <div class="position-relative" id="cart-item">
                            <div class="payment-info-loading" style="display: none;">
                                <div class="payment-info-loading-content">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                            </div>
                            {!! apply_filters(RENDER_PRODUCTS_IN_CHECKOUT_PAGE, $products) !!}
                            <div class="mt-2 p-2 order-summary">
                                <!-- Subtotal -->
                                <div class="row subtotal-row">
                                    <div class="col-6">
                                        <p>{{ __('Subtotal') }}:</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="price-text sub-total-text text-end got-price">
                                            {{ format_price(Cart::instance('cart')->rawSubTotal()) }}
                                        </p>
                                    </div>
                                </div>
                                <!-- Tax -->
                                @if (EcommerceHelper::isTaxEnabled())
                                    <div class="row">
                                        <div class="col-6">
                                            <p>
                                                {{ __('Tax') }}
                                                @if (Cart::instance('cart')->rawTax())
                                                    (<small class="sel-tax">{{ Cart::instance('cart')->taxClassesName() }}</small>)
                                                @endif
                                            </p>
                                        </div>
                                        <div class="col-6 float-end">
                                            <p class="price-text tax-price-text got-price">
                                                {{ format_price(Cart::instance('cart')->rawTax()) }}
                                            </p>
                                        </div>
                                    </div>
                                    <script>
                                        $(document).ajaxComplete(function() {
                                            // Get the subtotal and tax values
                                            const subTotalText = $('.sub-total-text').text().trim();
                                            const taxPriceText = $('.tax-price-text').text().trim();
                                        
                                            // Extract numeric values
                                            const subTotal = parseFloat(subTotalText.replace('$', ''));
                                            const taxPrice = parseFloat(taxPriceText.replace('$', ''));
                                        
                                            // Calculate the tax percentage
                                            const taxPercentage = (taxPrice / subTotal) * 100;
                                        
                                            // Get the sel-tax text and split it into individual tax types
                                            const selTaxText = $('.sel-tax').text().trim();
                                            const taxTypes = selTaxText.split(',');
                                        
                                            // Loop through the tax types and find the matching one
                                            let matchingTax = '';
                                            taxTypes.forEach(function(taxType) {
                                                const match = taxType.match(/(.+?) - (\d+)%/);
                                                if (match) {
                                                    const taxName = match[1].trim();
                                                    const percentage = parseFloat(match[2]);
                                                    if (percentage === Math.round(taxPercentage)) {
                                                        matchingTax = taxName;
                                                    }
                                                }
                                            });
                                        
                                            // Update the sel-tax element to show only the matching tax name
                                            if (matchingTax) {
                                                $('.sel-tax').text(matchingTax);
                                            } else {
                                                $('.sel-tax').text('No tax');
                                            }
                                        });
                                    </script>
                                @endif
                                <!-- Coupon Code -->
                                <div class="row giftcard-row d-none">
                                    <div class="col-6">
                                        <p>{{ __('Gift Certificate') }}:</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="price-text sub-total-text text-end gitfy-code">0</p>
                                    </div>
                                    <div class="col-6">
                                        <p>{{ __('Gift Certificate Amount') }}:</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="price-text sub-total-text text-end gitfy-amount">0</p>
                                    </div>
                                </div>
                                <!-- Coupon Code -->
                                @if (session('applied_coupon_code'))
                                    <div class="row coupon-information">
                                        <div class="col-6">
                                            <p>{{ __('Coupon code') }}:</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="price-text coupon-code-text">
                                                {{ session('applied_coupon_code') }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                                <!-- Coupon Discount Amount -->
                                @if ($couponDiscountAmount > 0)
                                    <div class="row price discount-amount">
                                        <div class="col-6">
                                            <p>{{ __('Coupon code discount amount') }}:</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="price-text total-discount-amount-text got-discount">
                                                {{ format_price($couponDiscountAmount) }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                                <!-- Promotion Discount Amount -->
                                @if ($promotionDiscountAmount > 0)
                                    <div class="row">
                                        <div class="col-6">
                                            <p>{{ __('Promotion discount amount') }}:</p>
                                        </div>
                                        <div class="col-6">
                                            <p class="price-text got-discount">
                                                {{ format_price($promotionDiscountAmount) }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                                <!-- Shipping Fee -->
                                @if (!empty($shipping) && Arr::get($sessionCheckoutData, 'is_available_shipping', true))
                                    <div class="row">
                                        <div class="col-6">
                                            <p>{{ __('Shipping fee') }}:</p>
                                        </div>
                                        <div class="col-6 float-end">
                                            <p class="price-text shipping-price-text got-price">{{ format_price($shippingAmount) }}</p>
                                        </div>
                                    </div>
                                @endif
                                <!-- Total -->
                                <div class="row">
                                    <div class="col-6">
                                        <p><strong>{{ __('Total') }}</strong>:</p>
                                    </div>
                                    <div class="col-6 float-end">
                                        <p class="total-text raw-total-text" data-price="{{ format_price($rawTotal, null, true) }}">
                                            {{ format_price($orderAmount) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <!-- Gift Card Forwarder -->
                        @if(!empty($g['contact_email']))
                            <h5>Gift Card Forwarder</h5>
                        @endif
                        @foreach($products as $product)
                            @if($product->product_type == 'gift')
                                <h6>{{$product->name}}</h6>
                                @php
                                    $s= 'Product_'.$product->id;
                                    $g= session($s);
                                @endphp
                                <label for="contact_email" class="form-label required">{{ __('To') }}</label>
                                <input class="form-control mb-3" type="text" id="session_contact_email" value="{{ $g['contact_email'] }}" readonly>
                                <label for="contact_name" class="form-label required">{{ __('From') }}</label>
                                <input class="form-control mb-3" type="text" id="session_name" value="{{ $g['name'] }}" readonly>
                                <label for="contact_content" class="form-label">{{ __('Message (optional)') }}</label>
                                <input class="form-control mb-3" type="text" id="session_content" value="{{ $g['content'] }}" readonly><hr>
                            @endif
                        @endforeach
                        
                        <style>
                            .gifty-wrapper {
                                background-image: url(/storage/gifty-bg.jpg);
                                height: 250px;
                                background-size: cover;
                                background-repeat: no-repeat;
                                padding: 10px;
                                padding-left: 150px;
                                text-align: right;
                                border-radius: 10px;
                                width: 100%;
                                background-position: bottom;
                            }
                        </style>
                        <script>
                            function applyGiftCodeValidation() {
                                $(".gift-code").css({"opacity": "0.5", "pointer-events": "none"});
                                $(".gifty-wrapper").css({"opacity": "0.5", "pointer-events": "none"});
                                setTimeout(function() {
                                    var giftCode = $(".gift-code").val().trim();
                                    var giftValue = null;
                                    var isValid = false;
                                    $(".checkout__coupon-item-code span").each(function() {
                                        if ($(this).text().trim() === giftCode) {
                                            isValid = true;
                                            giftValue = parseFloat($(this).closest(".checkout__coupon-item-content").find("h4").text().replace('$', ''));
                                            return false;
                                        }
                                    });
                        
                                    if (isValid) {
                                        $(".gift-code").removeClass("is-invalid").addClass("is-valid");
                                        $("input[name='gift_value']").val(giftValue);
                                        sessionStorage.setItem("gift_code", giftCode);
                                        sessionStorage.setItem("gift_value", giftValue);
                                        $(".checkout__coupon-heading").fadeIn().find(".gift-text").text("Certificate Balance: $" + giftValue);
                                        // Update gift card row
                                        $(".giftcard-row").removeClass("d-none");
                                        $(".gitfy-code").text(giftCode);
                                        $(".gitfy-amount").text("$" + giftValue);
                        
                                        // Calculate new total based on prices extracted from p.got-price excluding gift value
                                        var totalPrice = 0;
                                        $("p.got-price").each(function() {
                                            var priceText = $(this).text().trim();
                                            var price = parseFloat(priceText.replace("$", "").replace(",", ""));
                                            if (!isNaN(price)) {
                                                totalPrice += price;
                                            }
                                        });
                        
                                        // Subtract giftValue from new total amount
                                        var newTotal = totalPrice - giftValue;
                                        // Ensure total amount doesn't go negative
                                        newTotal = Math.max(newTotal, 0);
                                        var formattedTotal = newTotal.toLocaleString('en-US', {style: 'currency', currency: 'USD'});
                                        $("p.total-text").text(formattedTotal);
                                    } else {
                                        // Calculate new total amount based on prices extracted from p.got-price elements
                                        var totalPrice = 0;
                                        $("p.got-price").each(function() {
                                            var priceText = $(this).text().trim();
                                            var price = parseFloat(priceText.replace("$", "").replace(",", ""));
                                            if (!isNaN(price)) {
                                                totalPrice += price;
                                            }
                                        });
                                        
                                        // Subtract discount values from the total
                                        $("p.got-discount").each(function() {
                                            var discountText = $(this).text().trim();
                                            var discount = parseFloat(discountText.replace("$", "").replace(",", ""));
                                            if (!isNaN(discount)) {
                                                totalPrice -= discount;
                                            }
                                        });
                        
                                        var formattedTotal = totalPrice.toLocaleString('en-US', {style: 'currency', currency: 'USD'});
                                        $("p.total-text").text(formattedTotal);
                        
                                        // Restore the input and UI if the gift code is removed
                                        $(".gift-code").removeClass("is-valid").addClass("is-invalid");
                                        $("input[name='gift_value']").val("");
                                        $(".checkout__coupon-heading").hide();
                                        $(".giftcard-row").addClass("d-none");
                                    }
                                    $(".gift-code").css({"opacity": "1", "pointer-events": "auto"});
                                    $(".gifty-wrapper").css({"opacity": "1", "pointer-events": "auto"});
                                }, 3000);
                            }
                        </script>
                        <div class="gifty-wrapper mt-2 d-flex" style="flex-direction: column;justify-content: space-between;">
                            <div class="btn-group w-100">
                                <input class="form-control gift-code" name="gift_code" type="text" onblur="applyGiftCodeValidation()" placeholder="Have a Gift Certificate?">
                                <input name="gift_value" type="hidden">
                            </div>
                            <div class="checkout__coupon-heading" style="display: none;">
                                <img width="32" height="32" src="/vendor/core/plugins/ecommerce/images/coupon-code.gif" alt="coupon code icon">
                                <span class="gift-text"></span>
                            </div>
                        </div>

                        <div class="mt-3 mb-5 d-none">
                            @include(EcommerceHelper::viewPath('discounts.partials.form'), compact('discounts'))
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-6 left">
                        <div class="d-none d-sm-block">
                            @include('plugins/ecommerce::orders.partials.logo')
                        </div>
                        <div class="form-checkout">
                            {!! apply_filters('ecommerce_checkout_form_before', null, $products) !!}
                            @if ($isShowAddressForm)
                                <div class="mb-4">
                                    <h5 class="checkout-payment-title">{{ __('Shipping information') }}</h5>
                                    <input
                                        id="save-shipping-information-url"
                                        type="hidden"
                                        value="{{ route('public.checkout.save-information', $token) }}"
                                    >
                                    @include(
                                        'plugins/ecommerce::orders.partials.address-form',
                                        compact('sessionCheckoutData')
                                    )
                                </div>
                                {!! apply_filters('ecommerce_checkout_form_after_shipping_address_form', null, $products) !!}
                            @endif
                            @if (EcommerceHelper::isBillingAddressEnabled())
                                <div class="mb-4">
                                    <h5 class="checkout-payment-title">{{ __('Billing information') }}</h5>
                                    @include(
                                        'plugins/ecommerce::orders.partials.billing-address-form',
                                        compact('sessionCheckoutData')
                                    )
                                </div>
                                {!! apply_filters('ecommerce_checkout_form_after_billing_address_form', null, $products) !!}
                            @endif
                            @if (!is_plugin_active('marketplace'))
                                @if (Arr::get($sessionCheckoutData, 'is_available_shipping', true))
                                    <div id="shipping-method-wrapper" class="mb-4">
                                        <h5 class="checkout-payment-title">{{ __('Shipping method') }}</h5>
                                        <div class="shipping-info-loading" style="display: none;">
                                            <div class="shipping-info-loading-content">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </div>
                                        </div>
                                        @if (!empty($shipping))
                                            <div class="payment-checkout-form">
                                                <input name="shipping_option" type="hidden" value="{{ BaseHelper::stringify(old('shipping_option', $defaultShippingOption)) }}">
                                                <ul class="list-group list_payment_method methodi">
                                                    @if(array_key_exists('englandlogistics', $shipping))
                                                        <p>{!! 'Method : ' . session('shipmethod'). '<br>'. __('Shipping Fee is '.format_price($shippingAmount))  !!}</p>
                                                        <input name="shipping_method" type="hidden" value="default">
                                                        <input name="shipping_option" type="hidden" value="1">
                                                        <input name="shipping_amount" type="hidden" value="{{$shippingAmount}}">
                                                        <input name="shipping_vendor" type="hidden" value="{{$shipping_vendor}}">
                                                    @else
                                                        @foreach ($shipping as $shippingKey => $shippingItems)
                                                            @foreach ($shippingItems as $shippingOption => $shippingItem)
                                                                @include(
                                                                    'plugins/ecommerce::orders.partials.shipping-option',
                                                                    [
                                                                        'shippingItem' => $shippingItem,
                                                                        'attributes' => [
                                                                            'id' =>
                                                                                'shipping-method-' .
                                                                                $shippingKey .
                                                                                '-' .
                                                                                $shippingOption,
                                                                            'name' => 'shipping_method',
                                                                            'class' => 'magic-radio shipping_method_input',
                                                                            'checked' =>
                                                                                old(
                                                                                    'shipping_method',
                                                                                    $defaultShippingMethod) == $shippingKey &&
                                                                                old(
                                                                                    'shipping_option',
                                                                                    $defaultShippingOption) == $shippingOption,
                                                                            'disabled' => Arr::get(
                                                                                $shippingItem,
                                                                                'disabled'),
                                                                            'data-option' => $shippingOption,
                                                                        ],
                                                                    ]
                                                                )
                                                            @endforeach
                                                        @endforeach
                                                    @endif
                                                </ul>
                                                <script>
                                                $(document).ready(function() {
                                                    function updateSpanText() {
                                                        $('.methodi li span').each(function() {
                                                            var text = $(this).text();
                                                            var pos = text.indexOf('||');
                                                            if (pos !== -1) {
                                                                var newText = text.substring(pos + 2).trim();
                                                                $(this).text(newText);
                                                            }
                                                        });
                                                    }
                                                
                                                    // Initial call to update existing elements
                                                    updateSpanText();
                                                
                                                    // Attach a handler to the global AJAX complete event
                                                    $(document).ajaxComplete(function() {
                                                        updateSpanText();
                                                    });
                                                });
                                                </script>
                                            </div>
                                        @else
                                            <p>{{ __('No shipping methods available!') }}</p>
                                        @endif
                                    </div>
                                    {!! apply_filters('ecommerce_checkout_form_after_shipping_address_form', null, $products) !!}
                                @endif
                            @endif
                            {!! apply_filters('ecommerce_checkout_form_before_payment_form', null, $products) !!}
                            <input name="amount" type="hidden" value="{{ format_price($orderAmount, null, true) }}">
                            @if (is_plugin_active('payment') && $orderAmount)
                                @php
                                    $paymentMethods = apply_filters(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, null, [
                                            'amount' => format_price($orderAmount, null, true),
                                            'currency' => strtoupper(get_application_currency()->title),
                                            'name' => null,
                                            'selected' => PaymentMethods::getSelectedMethod(),
                                            'default' => PaymentMethods::getDefaultMethod(),
                                            'selecting' => PaymentMethods::getSelectingMethod(),
                                        ]) . PaymentMethods::render();
                                @endphp
                                <input name="currency" type="hidden" value="{{ strtoupper(get_application_currency()->title) }}">
                                @if($paymentMethods)
                                    <div class="position-relative mb-4">
                                        <div class="payment-info-loading" style="display: none;">
                                            <div class="payment-info-loading-content">
                                                <i class="fas fa-spinner fa-spin"></i>
                                            </div>
                                        </div>
                                        <h5 class="checkout-payment-title">{{ __('Payment method') }}</h5>
                                        {!! apply_filters(PAYMENT_FILTER_PAYMENT_PARAMETERS, null) !!}
                                        <ul class="list-group list_payment_method">
                                            {!! $paymentMethods !!}
                                        </ul>
                                    </div>
                                @endif
                            @endif
                            {!! apply_filters('ecommerce_checkout_form_after_payment_form', null, $products) !!}
                            <div @class(['form-group mb-3', 'has-error' => $errors->has('description')])>
                                <label class="form-label" for="description">{{ __('Order notes') }}</label>
                                <textarea
                                    class="form-control"
                                    id="description"
                                    name="description"
                                    rows="3"
                                    placeholder="{{ __('Notes about your order, e.g. special notes for delivery.') }}"
                                >{{ old('description') }}</textarea>
                                {!! Form::error('description', $errors) !!}
                            </div>
                            @if (EcommerceHelper::getMinimumOrderAmount() > Cart::instance('cart')->rawSubTotal())
                                <div role="alert" class="alert alert-warning">
                                    {{ __('Minimum order amount is :amount, you need to buy more :more to place an order!', ['amount' => format_price(EcommerceHelper::getMinimumOrderAmount()), 'more' => format_price(EcommerceHelper::getMinimumOrderAmount() - Cart::instance('cart')->rawSubTotal())]) }}
                                </div>
                            @endif
                            @if (EcommerceHelper::isDisplayTaxFieldsAtCheckoutPage())
                                @include(
                                    'plugins/ecommerce::orders.partials.tax-information',
                                    compact('sessionCheckoutData')
                                )
                                {!! apply_filters('ecommerce_checkout_form_after_tax_information_form', null, $products) !!}
                            @endif
                            @if($privacyPolicyUrl = theme_option('ecommerce_term_and_privacy_policy_url'))
                                <div class="form-check ps-0 mb-3">
                                    <input id="agree_terms_and_policy" name="agree_terms_and_policy" type="checkbox" value="1" @checked (old('agree_terms_and_policy', true))>
                                    <label class="form-check-label" for="agree_terms_and_policy">
                                        {!! BaseHelper::clean(__(
                                            'I agree to the :link',
                                            ['link' => Html::link($privacyPolicyUrl, __('Terms and Privacy Policy'), attributes: ['class' => 'text-decoration-underline', 'target' => '_blank'])]
                                        )) !!}
                                    </label>
                                </div>
                            @endif
                            {!! apply_filters('ecommerce_checkout_form_after', null, $products) !!}
                            <div class="row align-items-center g-3">
                                <div class="order-2 order-md-1 col-md-6 text-center text-md-start mb-4 mb-md-0">
                                    <a class="text-info" href="{{ route('public.cart') }}">
                                        <x-core::icon name="ti ti-arrow-narrow-left" />
                                        <span class="d-inline-block back-to-cart">{{ __('Back to cart') }}</span>
                                    </a>
                                    {!! apply_filters('ecommerce_checkout_form_after_back_to_cart_link', null, $products) !!}
                                </div>
                                <div class="order-1 order-md-2 col-md-6">
                                    @if (EcommerceHelper::isValidToProcessCheckout())
                                        <button
                                            class="btn payment-checkout-btn payment-checkout-btn-step float-end"
                                            data-processing-text="{{ __('Processing. Please wait...') }}"
                                            data-error-header="{{ __('Error') }}"
                                            type="submit"
                                        >
                                            {{ __('Checkout') }}
                                        </button>
                                    @else
                                        <span class="btn payment-checkout-btn-step float-end disabled">
                                            {{ __('Checkout') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </x-core::form>
        @if (is_plugin_active('payment'))
            @include('plugins/payment::partials.footer')
        @endif
    @else
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-warning my-5">
                        <span>{!! __('No products in cart. :link!', ['link' => Html::link(BaseHelper::getHomepageUrl(), __('Back to shopping'))]) !!}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif
@stop
@push('footer')
    <script type="text/javascript" src="{{ asset('vendor/core/core/js-validation/js/js-validation.js') }}"></script>
    {!! JsValidator::formRequest(
        Botble\Ecommerce\Http\Requests\SaveCheckoutInformationRequest::class,
        '#checkout-form',
    ) !!}
@endpush