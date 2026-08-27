<?php

declare(strict_types=1);

namespace Supplycart\Money\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use Supplycart\Money\Currency;
use Supplycart\Money\Money;

/** @implements CastsAttributes<Money, Money|array<string, mixed>|int|float|string|null> */
final class MoneyValue implements CastsAttributes
{
    #[\Override]
    public function get(Model $model, string $key, mixed $value, array $attributes): Money
    {
        $scale = 2;

        if (method_exists($model, 'getDecimalValue')) {
            $configuredScale = $model->getDecimalValue();

            if (is_int($configuredScale) && $configuredScale >= 2) {
                $scale = $configuredScale;
            }
        }

        $currency = $model->getAttribute('currency');

        if (! is_string($currency) || $currency === '') {
            $currency = Currency::default();
        }

        if (! is_int($value) && ! is_float($value) && ! is_string($value) && $value !== null) {
            throw new InvalidArgumentException('A money cast value must be numeric or null.');
        }

        return new Money($value, $currency, $scale);
    }

    #[\Override]
    public function set(Model $model, string $key, mixed $value, array $attributes): int|float|string
    {
        if ($value instanceof Money) {
            return $value->getAmount();
        }

        if (is_array($value) && array_key_exists('amount', $value)) {
            $value = $value['amount'];
        }

        if (blank($value)) {
            return 0;
        }

        if (is_int($value) || is_float($value) || is_string($value)) {
            return $value;
        }

        throw new InvalidArgumentException('A money cast value must be numeric, an amount array, or a Money instance.');
    }
}
