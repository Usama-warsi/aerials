<?php

namespace Botble\Braintree;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Facades\Setting;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Setting::delete([
                   'payment_braintree_payment_type',
            'payment_braintree_name',
            'payment_braintree_description',
            'payment_braintree_merchant_id',
            'payment_braintree_secret',
            'payment_braintree_public',
            'payment_braintree_status',
            'payment_braintree_logo'
        ]);
    }
}
