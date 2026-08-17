<?php

declare(strict_types=1);

namespace Supplycart\Money;

final class Country
{
    public const MALAYSIA = 'Malaysia';

    public const SINGAPORE = 'Singapore';

    public const THAILAND = 'Thailand';

    public const INDONESIA = 'Indonesia';

    public const PHILIPPINES = 'Philippines';

    public const VIETNAM = 'Vietnam';

    public const HONG_KONG = 'Hong Kong';

    public const BRUNEI = 'Brunei';

    public const CAMBODIA = 'Cambodia';

    public const MYANMAR = 'Myanmar';

    public static function default(): string
    {
        return self::MALAYSIA;
    }

    /** @return array<string, string> */
    public static function options(): array
    {
        $values = [
            self::MALAYSIA,
            self::SINGAPORE,
            self::THAILAND,
            self::INDONESIA,
            self::PHILIPPINES,
            self::VIETNAM,
            self::HONG_KONG,
            self::BRUNEI,
            self::CAMBODIA,
            self::MYANMAR,
        ];

        return array_combine($values, $values);
    }
}
