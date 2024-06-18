<?php

namespace Supplycart\Money;

class Country
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

    public static function default()
    {
        return self::MALAYSIA;
    }

    public static function options()
    {
        $class = new \ReflectionClass(self::class);

        $values = array_values($class->getConstants());

        return array_combine($values, $values);
    }
}