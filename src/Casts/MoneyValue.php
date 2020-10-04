<?php

namespace Supplycart\Money\Casts;

use ArrayAccess;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Supplycart\Money\Money;

class MoneyValue implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return new Money($value, $model->currency);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        $amount = 0;

        if ($value instanceof Money) {
            $amount = $value->getAmount();
        }

        if ($value instanceof ArrayAccess && data_get($value, 'amount')) {
            $amount = data_get($value, 'amount');
        }

        if (blank($value)) {
            $amount = 0;
        }

        return $amount;
    }
}