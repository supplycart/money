<?php

namespace Supplycart\Money\Tests;

use Orchestra\Testbench\TestCase as TestbenchCase;
use Supplycart\Money\MoneyServiceProvider;

class TestCase extends TestbenchCase
{
    protected function getPackageProviders($app)
    {
        return [MoneyServiceProvider::class];
    }
}