<?php

declare(strict_types=1);

namespace Supplycart\Money\Contracts;

interface Tax
{
    /** Tax rate as a numeric string, e.g. a 10% rate is `10.0`. */
    public function getTaxRate(): string;

    /** Human-readable tax description. */
    public function getTaxDescription(): string;

    /** Country where the tax applies. */
    public function getTaxCountry(): string;

    /** ISO 4217 currency code, e.g. MYR. */
    public function getTaxCurrency(): string;
}
