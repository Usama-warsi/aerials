<?php

namespace Botble\Braintree\Providers;

use Botble\Base\Facades\Html;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Facades\PaymentMethods;
use Botble\Braintree\Forms\BraintreePaymentMethodForm;
use Botble\Braintree\Services\Gateways\BraintreePaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(PAYMENT_FILTER_ADDITIONAL_PAYMENT_METHODS, [$this, 'registerBraintreeMethod'], 1, 2);

        $this->app->booted(function () {
            add_filter(PAYMENT_FILTER_AFTER_POST_CHECKOUT, [$this, 'checkoutWithBraintree'], 1, 2);
        });

        add_filter(PAYMENT_METHODS_SETTINGS_PAGE, [$this, 'addPaymentSettings'], 1);

        add_filter(BASE_FILTER_ENUM_ARRAY, function ($values, $class) {
            if ($class == PaymentMethodEnum::class) {
                $values['BRAINTREE'] = BRAINTREE_PAYMENT_METHOD_NAME;
            }

            return $values;
        }, 1, 2);

        add_filter(BASE_FILTER_ENUM_LABEL, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == BRAINTREE_PAYMENT_METHOD_NAME) {
                $value = 'Braintree';
            }

            return $value;
        }, 1, 2);

        add_filter(BASE_FILTER_ENUM_HTML, function ($value, $class) {
            if ($class == PaymentMethodEnum::class && $value == BRAINTREE_PAYMENT_METHOD_NAME) {
                $value = Html::tag(
                    'span',
                    PaymentMethodEnum::getLabel($value),
                    ['class' => 'label-success status-label']
                )
                    ->toHtml();
            }

            return $value;
        }, 1, 2);

        add_filter(PAYMENT_FILTER_GET_SERVICE_CLASS, function ($data, $value) {
            if ($value == BRAINTREE_PAYMENT_METHOD_NAME) {
                $data = BraintreePaymentService::class;
            }

            return $data;
        }, 1, 2);

        add_filter(PAYMENT_FILTER_PAYMENT_INFO_DETAIL, function ($data, $payment) {
            if ($payment->payment_channel == BRAINTREE_PAYMENT_METHOD_NAME) {
                $paymentDetail = (new BraintreePaymentService())->getPaymentDetails($payment->charge_id);

                $data = view('plugins/braintree::detail', ['payment' => $paymentDetail])->render();
            }

            return $data;
        }, 1, 2);

        if (defined('PAYMENT_FILTER_FOOTER_ASSETS')) {
            add_filter(PAYMENT_FILTER_FOOTER_ASSETS, function ($data) {
                if ($this->app->make(BraintreePaymentService::class)->isBraintreeApiCharge()) {
                    return $data . view('plugins/braintree::assets')->render();
                }

                return $data;
            }, 1);
        }
    }

    public function addPaymentSettings(?string $settings): string
    {
        return $settings . BraintreePaymentMethodForm::create()->renderForm();
    }

    public function registerBraintreeMethod(?string $html, array $data): string
    {
        PaymentMethods::method(BRAINTREE_PAYMENT_METHOD_NAME, [
            'html' => view('plugins/braintree::methods', $data)->render(),
        ]);

        return $html;
    }

    public function checkoutWithBraintree(array $data, Request $request): array
    {
        if ($data['type'] !== BRAINTREE_PAYMENT_METHOD_NAME) {
            return $data;
        }
            
        $braintreePaymentService = $this->app->make(BraintreePaymentService::class);

        $currentCurrency = get_application_currency();

        $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [], $request);
    //   echo '<pre>';  print_r($request->input()); exit();
        $supportedCurrencies = $braintreePaymentService->supportedCurrencyCodes();

        if (! in_array($paymentData['currency'], $supportedCurrencies) && strtoupper($currentCurrency->title) !== 'USD') {
            $currencyModel = $currentCurrency->replicate();

            $supportedCurrency = $currencyModel->query()->where('title', 'USD')->first();

            if ($supportedCurrency) {
                $paymentData['currency'] = strtoupper($supportedCurrency->title);
                if ($currentCurrency->is_default) {
                    $paymentData['amount'] = $paymentData['amount'] * $supportedCurrency->exchange_rate;
                } else {
                    $paymentData['amount'] = format_price(
                        $paymentData['amount'] / $currentCurrency->exchange_rate,
                        $currentCurrency,
                        true
                    );
                }
            }
        }
     
        if (! in_array($paymentData['currency'], $supportedCurrencies)) {
            $data['error'] = true;
            $data['message'] = __(
                ":name doesn't support :currency. List of currencies supported by :name: :currencies.",
                [
                    'name' => 'Braintree',
                    'currency' => $paymentData['currency'],
                    'currencies' => implode(', ', $supportedCurrencies),
                ]
            );

            return $data;
        }

        $result = $braintreePaymentService->execute($paymentData);

        if ($braintreePaymentService->getErrorMessage()) {
            $data['error'] = true;
            $data['message'] = $braintreePaymentService->getErrorMessage();
        } elseif ($result) {
            if ($braintreePaymentService->isBraintreeApiCharge()) {
                $data['charge_id'] = $result;
            } else {
                $data['checkoutUrl'] = $result;
            }
        }

        return $data;
    }
}
