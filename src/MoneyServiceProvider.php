<?php

namespace Supplycart\Money;

use Illuminate\Support\ServiceProvider;
use Supplycart\Money\Console\InstallCommand;

class MoneyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/money.php', 'money'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/money.php' => config_path('money.php'),
        ]);
    }
}
