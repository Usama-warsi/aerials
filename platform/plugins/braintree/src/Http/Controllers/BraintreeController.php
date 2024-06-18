<?php

namespace Botble\Braintree\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Braintree\Http\Requests\BraintreePaymentCallbackRequest;
use Botble\Braintree\Services\Gateways\BraintreePaymentService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use GuzzleHttp\Client;
use DB;

// require_once base_path().'\platform\plugins\braintree\vendor\autoload.php';
require_once base_path() . DIRECTORY_SEPARATOR . 'platform' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'braintree' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Braintree\Gateway;

class BraintreeController extends BaseController
{
    public function webhook(Request $request)
    {
        $webhookSecret = get_payment_setting('webhook_secret', 'stripe');
        $signature = $request->server('HTTP_STRIPE_SIGNATURE');
        $content = $request->getContent();

        if (! $webhookSecret || ! $signature || ! $content) {
            return response()->noContent();
        }

        try {
            $event = Webhook::constructEvent(
                $content,
                $signature,
                $webhookSecret
            );

            /**
             * @var PaymentIntent $paymentIntent
             */
            if ($event->type == 'payment_intent.succeeded') {
                $paymentIntent = $event->data->object;

                $payment = Payment::query()
                    ->where('charge_id', $paymentIntent->id)
                    ->first();

                if ($payment) {
                    $payment->status = PaymentStatusEnum::COMPLETED;
                    $payment->save();

                    do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                        'charge_id' => $payment->charge_id,
                        'order_id' => $payment->order_id,
                    ]);
                }
            }
        } catch (SignatureVerificationException $e) {
            BaseHelper::logError($e);
        }

        return response()->noContent();
    }

    public function success(
        StripePaymentCallbackRequest $request,
        StripePaymentService $stripePaymentService,
        BaseHttpResponse $response
    ) {
        try {
            $stripePaymentService->setClient();

            $session = Session::retrieve($request->input('session_id'));

            if ($session->payment_status == 'paid') {
                $metadata = $session->metadata->toArray();

                $orderIds = json_decode($metadata['order_id'], true);

                $charge = PaymentIntent::retrieve($session->payment_intent);

                if (! $charge->latest_charge) {
                    return $response
                        ->setError()
                        ->setNextUrl(PaymentHelper::getCancelURL())
                        ->setMessage(__('No payment charge. Please try again!'));
                }

                $chargeId = $charge->latest_charge;

                do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
                    'amount' => $metadata['amount'],
                    'currency' => strtoupper($session->currency),
                    'charge_id' => $chargeId,
                    'order_id' => $orderIds,
                    'customer_id' => Arr::get($metadata, 'customer_id'),
                    'customer_type' => Arr::get($metadata, 'customer_type'),
                    'payment_channel' => 'Braintree',
                    'status' => PaymentStatusEnum::COMPLETED,
                ]);

                return $response
                    ->setNextUrl(PaymentHelper::getRedirectURL() . '?charge_id=' . $chargeId)
                    ->setMessage(__('Checkout successfully!'));
            }

            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->setMessage(__('Payment failed!'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->withInput()
                ->setMessage($exception->getMessage() ?: __('Payment failed!'));
        }
    }

    public function error(BaseHttpResponse $response)
    {
        return $response
            ->setError()
            ->setNextUrl(PaymentHelper::getCancelURL())
            ->withInput()
            ->setMessage(__('Payment failed!'));
    }
    public function gettoken(BaseHttpResponse $response)
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


            
try {
    $gateway = new Gateway([
        'environment' => $environment,
        'merchantId'  => $merchantId,
        'publicKey'   => $publicKey,
        'privateKey'  => $privateKey
    ]);

    $clientToken = $gateway->clientToken()->generate();
    
    return $clientToken;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

    }
}
