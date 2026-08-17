<?php

declare(strict_types=1);

namespace Supplycart\Money;

final class Currency
{
    public const MYR = 'MYR';

    public const IDR = 'IDR';

    public const SGD = 'SGD';

    public const HKD = 'HKD';

    public const VND = 'VND';

    public const THB = 'THB';

    public const BND = 'BND';

    public const PHP = 'PHP';

    public static function default(): string
    {
        return self::MYR;
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        return [
            self::MYR => self::MYR,
            self::IDR => self::IDR,
            self::SGD => self::SGD,
            self::HKD => self::HKD,
            self::VND => self::VND,
            self::THB => self::THB,
            self::BND => self::BND,
            self::PHP => self::PHP,
        ];
    }
}
