<?php

namespace Supplycart\Money\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Supplycart\Money\Currency;
use Supplycart\Money\Money;

class MoneyValue implements CastsAttributes
{
    public function get($model, string $key, $value, array $attributes)
    {
        return new Money($value, $model->currency ?? Currency::default());
    }

    public function set($model, string $key, $value, array $attributes)
    {
        if ($value instanceof Money) {
            return $value->getAmount();
        }

        if (is_array($value) && data_get($value, 'amount')) {
            return data_get($value, 'amount');
        }

        if (blank($value)) {
            return 0;
        }

        return $value;
    }
}