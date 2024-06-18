<?php

namespace Botble\Englandlogistics\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Englandlogistics\Http\Middleware\WebhookMiddleware;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\ServiceProvider;

class EnglandlogisticsServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        if (! is_plugin_active('ecommerce')) {
            return;
        }

        $this->setNamespace('plugins/englandlogistics')->loadHelpers();
    }

    public function boot(): void
    {
        if (! is_plugin_active('ecommerce')) {
            return;
        }

        $this
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->loadRoutes()
            ->loadAndPublishConfigurations(['general'])
            ->publishAssets();

        $this->app['events']->listen(RouteMatched::class, function () {
            $this->app['router']->aliasMiddleware('englandlogistics.webhook', WebhookMiddleware::class);
        });

        $config = $this->app['config'];
        if (! $config->has('logging.channels.englandlogistics')) {
            $config->set([
                'logging.channels.englandlogistics' => [
                    'driver' => 'daily',
                    'path' => storage_path('logs/englandlogistics.log'),
                ],
            ]);
        }

        $this->app->register(HookServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);
    }
}
