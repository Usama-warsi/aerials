<?php

namespace Botble\Braintree\Forms;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\FieldOptions\CheckboxFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Payment\Forms\PaymentMethodForm;

class BraintreePaymentMethodForm extends PaymentMethodForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->paymentId(BRAINTREE_PAYMENT_METHOD_NAME)
            ->paymentName('Braintree')
            ->paymentDescription(trans('plugins/payment::payment.braintree_description'))
            ->paymentLogo(url('https://www.braintreepayments.com/images/braintree-logo-black.png'))
            ->paymentUrl('https://www.braintreepayments.com')
            // ->paymentInstructions(view('plugins/braintree::instructions')->render())
            ->add(
                'payment_braintree_merchant_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/payment::payment.braintree_merchantid'))
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('merchant_id', 'braintree'))
                    ->placeholder('*************')
                    ->attributes(['data-counter' => 400])
                    ->toArray()
            )
            ->add(
                'payment_braintree_secret',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/payment::payment.braintree_secret'))
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('secret', 'braintree'))
                    ->placeholder('*************')
                    ->toArray()
            )
            ->add(
                'payment_braintree_public',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/payment::payment.braintree_public'))
                    ->value(BaseHelper::hasDemoModeEnabled() ? '*******************************' : get_payment_setting('public', 'braintree'))
                    ->placeholder('*************')
                    ->toArray()
            )
            ->add(
                'payment_' . BRAINTREE_PAYMENT_METHOD_NAME . '_payment_type',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Payment Type'))
                    ->choices([
                        'braintree_api_charge' => 'BrainTree API Charge',
                        // 'braintree_checkout' => 'Stripe Checkout',
                    ])
                    ->selected(get_payment_setting(
                        'payment_type',
                        BRAINTREE_PAYMENT_METHOD_NAME,
                        'braintree_api_charge',
                    ))
                    ->toArray()
            ) 
            ->add(
                'payment_' . BRAINTREE_PAYMENT_METHOD_NAME . '_payment_mode',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(__('Payment Mode'))
                    ->choices([
                        'sandbox' => 'Sandbox',
                        'production' => 'Production',
                    ])
                    ->selected(get_payment_setting(
                        'payment_mode',
                        BRAINTREE_PAYMENT_METHOD_NAME,
                        'sandbox',
                    ))
                    ->toArray()
            ) 
            ;
    }
}
