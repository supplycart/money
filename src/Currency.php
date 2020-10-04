<?php

namespace Supplycart\Money;

class Currency
{
    const MYR = 'MYR';
    const IDR = 'IDR';
    const SGD = 'SGD';
    const HKD = 'HKD';
    const VND = 'VND';
    const THB = 'THB';
    const BND = 'BND';
    const PHP = 'PHP';

    public static function default()
    {
        return self::MYR;
    }

    public static function options()
    {
        $class = new \ReflectionClass(__CLASS__);

        return $class->getConstants();
    }
}
