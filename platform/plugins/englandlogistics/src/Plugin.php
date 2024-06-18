<?php

namespace Botble\Englandlogistics;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Facades\Setting;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Setting::delete([
            'shipping_englandlogistics_name',
            'shipping_englandlogistics_status',
            'shipping_englandlogistics_api_key',
            'shipping_englandlogistics_customer_id',
            'shipping_englandlogistics_sandbox',
            'shipping_englandlogistics_logging',
            'shipping_englandlogistics_cache_response',
            'shipping_englandlogistics_webhooks',
        ]);
    }
}
