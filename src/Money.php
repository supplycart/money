<?php

namespace Supplycart\Money;

use Brick\Math\BigDecimal;
use Brick\Math\BigRational;
use Brick\Math\RoundingMode;
use Brick\Money\Money as BrickMoney;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Supplycart\Money\Contracts\Tax as TaxContract;
use Stringable;

class Money implements Arrayable, Jsonable, Stringable
{
    private BrickMoney $instance;

    private ?TaxContract $tax = null;

    public static int $scale = 3;

    public static int $roundingMode = RoundingMode::HALF_EVEN;

    public function __construct($amount = 0, string $currency = Currency::MYR)
    {
        $this->instance = BrickMoney::ofMinor($amount ?? 0, $currency, null, static::$roundingMode);
    }

    public static function of($amount = 0, string $currency = Currency::MYR)
    {
        return new static($amount, $currency);
    }

    /**
     * @param int|float|array|Money|BrickMoney $value
     * @param null $currency
     * @return static
     */
    public static function parse($value, $currency = null): Money
    {
        $currency = $currency ?? Currency::default();

        if ($value instanceof Money) {
            return new static($value->getAmount(), $value->getCurrency());
        }

        if ($value instanceof BrickMoney) {
            return new static($value->getMinorAmount(), $value->getCurrency());
        }

        if (is_array($value) && array_key_exists('amount', $value)) {
            return new static(data_get($value, 'amount', 0), data_get($value, 'currency', $currency));
        }

        if (is_float($value)) {
            return new static((string) BigDecimal::of($value)->getUnscaledValue(), $currency);
        }

        return new static($value, $currency);
    }

    public static function fromCents(int $amount, string $currency = Currency::MYR)
    {
        return new static($amount, $currency);
    }

    public static function fromDecimal(string $amount, string $currency = Currency::MYR)
    {
        return new static(BigDecimal::of($amount)->getUnscaledValue(), $currency);
    }

    public function getAmount(): string
    {
        return (string) $this->instance->getMinorAmount()->toScale(static::$scale, static::$roundingMode);
    }

    public function getDecimalAmount($scale): string
    {
        return (string) $this->instance->getAmount()->toScale($scale, static::$roundingMode);
    }

    /**
     * @deprecated use `getDecimalAmount()`
     */
    public function toDecimal()
    {
        return $this->getDecimalAmount(2);
    }

    /**
     * @deprecated use `format()`
     */
    public function toCurrencyFormat()
    {
        return $this->format();
    }

    public function format($locale = null)
    {
        $locale = $locale ?? Locale::$currencies[(string) $this->instance->getCurrency()];

        return $this->instance->formatTo($locale);
    }

    public function getCurrency(): string
    {
        return (string) $this->instance->getCurrency();
    }

    public function add($value): Money
    {
        return new static($this->instance->plus($value)->getMinorAmount(), $this->instance->getCurrency());
    }

    public function subtract($value): Money
    {
        return new static($this->instance->minus($value)->getMinorAmount(), $this->instance->getCurrency());
    }

    public function multiply($value): Money
    {
        $value = $this->instance->multipliedBy($value, static::$roundingMode);

        return new static($value->getMinorAmount(), $value->getCurrency());
    }

    public function divide($value): Money
    {
        $value = $this->instance->dividedBy($value, static::$roundingMode);

        return new static($value->getMinorAmount(), $this->instance->getCurrency());
    }

    public function withTax(TaxContract $tax): Money
    {
        $this->tax = $tax;

        return $this;
    }

    public function getTaxAmount($quantity = 1): Money
    {
        if (!$this->tax) {
            return static::zero($this->getCurrency());
        }

        $taxValue = $this->instance->toRational()
            ->multipliedBy($this->getTaxRate())
            ->multipliedBy($quantity)
            ->to($this->instance->getContext(), static::$roundingMode);

        return static::of($taxValue->getMinorAmount(), $this->getCurrency());
    }

    public function getTaxRate(): BigDecimal
    {
        if (!$this->tax) {
            return BigDecimal::zero();
        }

        return BigRational::of($this->tax->getTaxRate())
            ->dividedBy(100)
            ->toScale(static::$scale, static::$roundingMode);
    }

    public function afterTax(): Money
    {
        $afterTax = $this->instance->multipliedBy(1 + $this->getTaxRate());

        return new static($afterTax->getMinorAmount(), $this->getCurrency());
    }

    public static function zero(string $currency = Currency::MYR): Money
    {
        return new static(0, $currency);
    }

    public function isZero()
    {
        return $this->instance->isZero();
    }

    public function __toString()
    {
        return (string) $this->getDecimalAmount(2);
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
        ];
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}
