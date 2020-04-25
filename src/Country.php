<?php

namespace Supplycart\Money;

class Country
{
    const MALAYSIA = 'Malaysia';
    const SINGAPORE = 'Singapore';
    const THAILAND = 'Thailand';
    const INDONESIA = 'Indonesia';
    const PHILIPPINES = 'Philippines';
    const VIETNAM = 'Vietnam';
    const HONG_KONG = 'Hong Kong';
    const BRUNEI = 'Brunei';
    const CAMBODIA = 'Cambodia';
    const MYANMAR = 'Myanmar';

    public static function default()
    {
        return self::MALAYSIA;
    }

    public static function options()
    {
        $class = new \ReflectionClass(__CLASS__);

        $values = array_values($class->getConstants());

        return array_combine($values, $values);
    }
}