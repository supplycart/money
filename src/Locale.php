<?php

declare(strict_types=1);

namespace Supplycart\Money;

final class Locale
{
    /** @var array<string, string> */
    public static array $currencies = [
        Currency::MYR => 'en_MY',
        Currency::SGD => 'en_SG',
        Currency::IDR => 'id_ID',
        Currency::BND => 'ms_BN',
        Currency::HKD => 'en_HK',
        Currency::PHP => 'en_PH',
        Currency::THB => 'th_TH',
        Currency::VND => 'vi_VN',
    ];

    /** @var array<string, string> */
    public static array $countries = [
        Country::MALAYSIA => 'en_MY',
        Country::SINGAPORE => 'en_SG',
        Country::INDONESIA => 'id_ID',
        Country::BRUNEI => 'ms_BN',
        Country::HONG_KONG => 'en_HK',
        Country::PHILIPPINES => 'en_PH',
        Country::THAILAND => 'th_TH',
        Country::VIETNAM => 'vi_VN',
        Country::CAMBODIA => 'km_KH',
        Country::MYANMAR => 'my_MM',
    ];
}
