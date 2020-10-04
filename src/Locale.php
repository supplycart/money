<?php

namespace Supplycart\Money;

class Locale
{
    public static array $currencies = [
        Currency::MYR => 'en_MY',
        Currency::SGD => 'en_SG',
        Currency::IDR => 'id_ID',
        Currency::BND => 'ms_BN',
        Currency::HKD => 'en_HK',
        Currency::PHP => 'en_PH',
        Currency::THB => 'th_TH',
    ];

    public static array $countries = [
        Country::MALAYSIA => 'en_MY',
        Country::SINGAPORE => 'en_SG',
        Country::INDONESIA => 'id_ID',
        Country::BRUNEI => 'ms_BN',
        Country::HONG_KONG => 'en_HK',
        Country::PHILIPPINES => 'en_PH',
        Country::THAILAND => 'th_TH',
    ];
}