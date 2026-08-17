<?php

declare(strict_types=1);

namespace Supplycart\Money\Tests;

use Orchestra\Testbench\TestCase as TestbenchCase;
use Supplycart\Money\MoneyServiceProvider;

abstract class TestCase extends TestbenchCase
{
    /** @return list<class-string> */
    #[\Override]
    protected function getPackageProviders($app): array
    {
        return [MoneyServiceProvider::class];
    }
}
