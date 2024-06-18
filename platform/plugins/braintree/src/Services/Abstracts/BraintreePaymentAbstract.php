<?php

namespace Botble\Braintree\Services\Abstracts;

use Botble\Payment\Services\Traits\PaymentErrorTrait;
use Botble\Braintree\Supports\BraintreeHelper;
use Exception;
use DB;
require '../vendor/autoload.php';
use Braintree\Gateway;
use Braintree\Transaction;

abstract class BraintreePaymentAbstract
{
    use PaymentErrorTrait;

    protected ?string $token = null;

    protected float $amount;

    protected string $currency;

    protected string $chargeId;

    protected bool $supportRefundOnline = true;

    public function getSupportRefundOnline(): bool
    {
        return $this->supportRefundOnline;
    }

    public function execute(array $data): ?string
    {
        $this->token = request()->input('payment_method_nonce');

        $chargeId = null;

        try {
            $chargeId = $this->makePayment($data);
        } catch (Exception $exception) {
            $this->setErrorMessageAndLogging($exception->getMessage());
        }

        return $chargeId;
    }

    abstract public function makePayment(array $data): ?string;

    abstract public function afterMakePayment(string $chargeId, array $data);

    public function getPaymentDetails(string $chargeId): ?Transaction
    {
        try {
            $gateway = $this->getGateway();

            return $gateway->transaction()->find($chargeId);


        } catch (Exception $exception) {
            $this->setErrorMessageAndLogging($exception->getMessage());
            return null;
        }
    }

    protected function getGateway(): Gateway
    {
       
    
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

        // Your Braintree configuration settings
        return new Gateway([
            'environment' => $environment,
            'merchantId'  => $merchantId,
            'publicKey'   => $publicKey,
            'privateKey'  => $privateKey
        ]);
    }

    // Other methods remain unchanged
}