@if (setting('payment_braintree_status') == 1)
    <li class="list-group-item">
        <input
            class="magic-radio js_payment_method"
            id="payment_braintree"
            name="payment_method"
            type="radio"
            value="braintree" 
            @if ($selecting == BRAINTREE_PAYMENT_METHOD_NAME) checked @endif
        >
        <label
            class="text-start"
            for="payment_braintree"
        >
            {{ setting('payment_braintree_name', trans('plugins/payment::payment.payment_via_card')) }}
        </label>
        <div
            class="payment_braintree_wrap payment_collapse_wrap collapse @if ($selecting == BRAINTREE_PAYMENT_METHOD_NAME) show @endif"
            style="padding: 15px 0;"
        >
            <p>{!! BaseHelper::clean(get_payment_setting('description', BRAINTREE_PAYMENT_METHOD_NAME)) !!}</p>
            @if (get_payment_setting('payment_type', BRAINTREE_PAYMENT_METHOD_NAME, 'braintree_api_charge') == 'braintree_api_charge')
            <div id="dropin-container">
     <h4 class="text-center">Payment Gateway is Loading...</h4>
           <h2 class="text-center">
                <div class="">
                          <i class="fas fa-spinner fa-spin"></i>
                      </div>
           </h2>
    
</div>
        <input type="hidden" id="payment_method_nonce" name="payment_method_nonce">
    

            @endif

            @php $supportedCurrencies =  (new Botble\Braintree\Services\Gateways\BraintreePaymentService)->supportedCurrencyCodes(); @endphp
            @if (
                !in_array(get_application_currency()->title, $supportedCurrencies) &&
                    !get_application_currency()->replicate()->newQuery()->where('title', 'USD')->exists())
                <div
                    class="alert alert-warning"
                    style="margin-top: 15px;"
                >
                    {{ __(":name doesn't support :currency. List of currencies supported by :name: :currencies.", ['name' => 'Stripe', 'currency' => get_application_currency()->title, 'currencies' => implode(', ', $supportedCurrencies)]) }}
                    @php
                        $currencies = get_all_currencies();

                        $currencies = $currencies->filter(function ($item) use ($supportedCurrencies) {
                            return in_array($item->title, $supportedCurrencies);
                        });
                    @endphp
                    @if (count($currencies))
                        <div style="margin-top: 10px;">
                            {{ __('Please switch currency to any supported currency') }}:&nbsp;&nbsp;
                            @foreach ($currencies as $currency)
                                <a
                                    href="{{ route('public.change-currency', $currency->title) }}"
                                    @if (get_application_currency_id() == $currency->id) class="active" @endif
                                ><span>{{ $currency->title }}</span></a>
                                @if (!$loop->last)
                                    &nbsp; | &nbsp;
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </li>
    
@endif
