<?php

namespace Botble\Braintree\Services\Gateways;

use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Braintree\Services\Abstracts\BraintreePaymentAbstract;
use Botble\Braintree\Supports\BraintreeHelper;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use DB;
require_once base_path() . DIRECTORY_SEPARATOR . 'platform' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'braintree' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
use Braintree\Gateway;
class BraintreePaymentService extends BraintreePaymentAbstract
{
    public function makePayment(array $data): string|null
    {
        $request = request();
        $this->amount = $data['amount'];
        $this->currency = strtoupper($data['currency']);

        if ($this->isBraintreeApiCharge()) {
            if (! $this->token) {
                $this->setErrorMessage(trans('plugins/payment::payment.could_not_get_braintree_token'));

                Log::error(
                    trans('plugins/payment::payment.could_not_get_braintree_token'),
                    PaymentHelper::formatLog(
                        [
                            'error' => 'missing braintree token',
                            'last_4_digits' => $request->input('last4Digits'),
                            'name' => $request->input('name'),
                            'client_IP' => $request->input('clientIP'),
                            'time_created' => $request->input('timeCreated'),
                            'live_mode' => $request->input('liveMode'),
                        ],
                        __LINE__,
                        __FUNCTION__,
                        __CLASS__
                    )
                );

                return null;
            }

            // $charge = Charge::create([
            //     'amount' => $this->convertAmount($this->amount),
            //     'currency' => $this->currency,
            //     'source' => $this->token,
            //     'description' => trans('plugins/payment::payment.payment_description', [
            //         'order_id' => implode(', #', $data['order_id']),
            //         'site_url' => $request->getHost(),
            //     ]),
            //     'metadata' => ['order_id' => json_encode($data['order_id'])],
            // ]);
          
    

$environment = '';
$merchantId = '';
$publicKey = '';
$privateKey = '';


            $settings = DB::table('settings')
            ->select('id', 'key', 'value', 'created_at', 'updated_at')
            ->where('key', 'like', '%braintree%')
            ->get();
            foreach ($settings as $setting) {
           

                if($setting->key == 'payment_braintree_merchant_id'){
                    $merchantId =  $setting->value;
            }
    
            if($setting->key == 'payment_braintree_secret'){
                $privateKey =  $setting->value;
        }
    
        if($setting->key == 'payment_braintree_public'){
            $publicKey =  $setting->value;
    }
    
    if($setting->key == 'payment_braintree_payment_mode'){
        $environment =  $setting->value;
    }
            }


try {
            
$gateway = new Gateway([
    'environment' => $environment,
    'merchantId'  => $merchantId,
    'publicKey'   => $publicKey,
    'privateKey'  => $privateKey
  ]);


$nonceFromTheClient = $this->token;


$result = $gateway->transaction()->sale([
    'amount' => $this->amount,
    'paymentMethodNonce' => $nonceFromTheClient,
    'options' => [
        'submitForSettlement' => true
    ]
]);


// echo'<pre>'; print_r($result); exit();
if ($result->success) {
      $this->chargeId = $result->transaction->id;
 if ($this->chargeId) {
                $this->afterMakePayment($this->chargeId, $data);
            }
  return $this->chargeId;
          
} else {
    $this->chargeId = '';
 return $this->chargeId;
}
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}


           
        }

        $lineItems = [];

        foreach ($data['products'] as $product) {
            $lineItems[] = [
                'price_data' => [
                    'product_data' => [
                        'name' => $product['name'],
                        'metadata' => [
                            'pro_id' => $product['id'],
                        ],
                        'description' => $product['name'],
                        'images' => array_filter([Arr::get($product, 'image')]),
                    ],
                    'unit_amount' => $this->convertAmount($product['price_per_order'] / $product['qty']),
                    'currency' => $this->currency,
                ],
                'quantity' => $product['qty'],
            ];
        }

        $requestData = [
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('payments.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payments.stripe.error'),
            'metadata' => [
                'order_id' => json_encode($data['order_id']),
                'amount' => $this->amount,
                'currency' => $this->currency,
                'customer_id' => Arr::get($data, 'customer_id'),
                'customer_type' => Arr::get($data, 'customer_type'),
                'return_url' => Arr::get($data, 'return_url'),
                'callback_url' => Arr::get($data, 'callback_url'),
            ],
        ];

        if (! empty($data['shipping_method'])) {
            $requestData['shipping_options'] = [
                [
                    'shipping_rate_data' => [
                        'type' => 'fixed_amount',
                        'fixed_amount' => [
                            'amount' => $this->convertAmount($data['shipping_amount']),
                            'currency' => $this->currency,
                        ],
                        'display_name' => $data['shipping_method'],
                    ],
                ],
            ];
        }

        $checkoutSession = StripeCheckoutSession::create($requestData);

        return $checkoutSession->url;
    }

    protected function convertAmount(float $amount): int
    {
        $multiplier = StripeHelper::getStripeCurrencyMultiplier($this->currency);

        if ($multiplier > 1) {
            $amount = round($amount, 2) * $multiplier;
        }

        return (int)$amount;
    }

    public function afterMakePayment(string $chargeId, array $data): string
    {
        try {
            $payment = $this->getPaymentDetails($chargeId);
       
            
            if ($payment && ( $payment->status == 'submitted_for_settlement')) {
                $paymentStatus = PaymentStatusEnum::COMPLETED;
            } else {
                $paymentStatus = PaymentStatusEnum::FAILED;
            }
        } catch (Exception) {
            $paymentStatus = PaymentStatusEnum::FAILED;
        }

        do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
            'amount' => $data['amount'],
            'currency' => $data['currency'],
            'charge_id' => $chargeId,
            'order_id' => (array)$data['order_id'],
            'customer_id' => Arr::get($data, 'customer_id'),
            'customer_type' => Arr::get($data, 'customer_type'),
            'payment_channel' => BRAINTREE_PAYMENT_METHOD_NAME,
            'status' => $paymentStatus,
        ]);

        return $chargeId;
    }

    public function isBraintreeApiCharge(): bool
    {
        $key = 'braintree_api_charge';

        return get_payment_setting('payment_type', BRAINTREE_PAYMENT_METHOD_NAME, $key) == $key;
    }

    public function supportedCurrencyCodes(): array
    {
        return [
            'USD',
            'AED',
            'AFN',
            'ALL',
            'AMD',
            'ANG',
            'AOA',
            'ARS',
            'AUD',
            'AWG',
            'AZN',
            'BAM',
            'BBD',
            'BDT',
            'BGN',
            'BHD',
            'BIF',
            'BMD',
            'BND',
            'BOB',
            'BRL',
            'BSD',
            'BWP',
            'BYN',
            'BZD',
            'CAD',
            'CDF',
            'CHF',
            'CLP',
            'CNY',
            'COP',
            'CRC',
            'CVE',
            'CZK',
            'DJF',
            'DKK',
            'DOP',
            'DZD',
            'EGP',
            'ETB',
            'EUR',
            'FJD',
            'FKP',
            'GBP',
            'GEL',
            'GIP',
            'GMD',
            'GNF',
            'GTQ',
            'GYD',
            'HKD',
            'HNL',
            'HRK',
            'HTG',
            'HUF',
            'IDR',
            'ILS',
            'INR',
            'ISK',
            'JMD',
            'JOD',
            'JPY',
            'KES',
            'KGS',
            'KHR',
            'KMF',
            'KRW',
            'KWD',
            'KYD',
            'KZT',
            'LAK',
            'LBP',
            'LKR',
            'LRD',
            'LSL',
            'MAD',
            'MDL',
            'MGA',
            'MKD',
            'MMK',
            'MNT',
            'MOP',
            'MRO',
            'MUR',
            'MVR',
            'MWK',
            'MXN',
            'MYR',
            'MZN',
            'NAD',
            'NGN',
            'NIO',
            'NOK',
            'NPR',
            'NZD',
            'OMR',
            'PAB',
            'PEN',
            'PGK',
            'PHP',
            'PKR',
            'PLN',
            'PYG',
            'QAR',
            'RON',
            'RSD',
            'RUB',
            'RWF',
            'SAR',
            'SBD',
            'SCR',
            'SEK',
            'SGD',
            'SHP',
            'SLE',
            'SOS',
            'SRD',
            'STD',
            'SZL',
            'THB',
            'TJS',
            'TND',
            'TOP',
            'TRY',
            'TTD',
            'TWD',
            'TZS',
            'UAH',
            'UGX',
            'UYU',
            'UZS',
            'VND',
            'VUV',
            'WST',
            'XAF',
            'XCD',
            'XOF',
            'XPF',
            'YER',
            'ZAR',
            'ZMW',
            'USDC',
            'BTN',
            'GHS',
            'EEK',
            'LVL',
            'SVC',
            'VEF',
            'LTL',
            'SLL',
        ];
    }
}
