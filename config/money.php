<?php

use Supplycart\Money\Country;
use Supplycart\Money\Currency;
use Supplycart\Money\Locale;

return [
    'default' => [
        'country' => Country::MALAYSIA,
        'currency' => Currency::MYR,
        'locale' => Locale::$countries[Country::MALAYSIA],
    ],
];