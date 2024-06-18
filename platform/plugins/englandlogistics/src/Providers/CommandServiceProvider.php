<?php

namespace Botble\Englandlogistics\Providers;

use Botble\Englandlogistics\Commands\InitEnglandlogisticsCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->commands([
            InitEnglandlogisticsCommand::class,
        ]);
    }
}
