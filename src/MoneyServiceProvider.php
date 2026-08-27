<?php

declare(strict_types=1);

namespace Supplycart\Money;

use Illuminate\Support\ServiceProvider;

final class MoneyServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/money.php', 'money');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/money.php' => config_path('money.php'),
            ], 'money-config');
        }
    }
}
