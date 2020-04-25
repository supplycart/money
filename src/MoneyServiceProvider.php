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
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);

            if (!class_exists('CreateSnapshotsTable')) {
                $path = __DIR__ . '/../database/migrations/create_taxes_table.php.stub';

                $this->publishes([
                    $path => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_taxes_table.php'),
                ], 'migrations');
            }
        }

        $this->loadFactoriesFrom(__DIR__ . '/../database/factories');
    }
}
