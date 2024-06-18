<?php

namespace Supplycart\Money;

class Currency
{
    public const MYR = 'MYR';
    public const IDR = 'IDR';
    public const SGD = 'SGD';
    public const HKD = 'HKD';
    public const VND = 'VND';
    public const THB = 'THB';
    public const BND = 'BND';
    public const PHP = 'PHP';

    public static function default()
    {
        return self::MYR;
    }

    public static function options()
    {
        $class = new \ReflectionClass(self::class);

        return $class->getConstants();
    }
}
