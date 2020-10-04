<?php

namespace Supplycart\Money;

use Brick\Math\BigDecimal;
use Brick\Math\BigInteger;
use Brick\Math\RoundingMode;
use Brick\Money\Money as BrickMoney;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

class Money implements Arrayable, JsonSerializable
{
    private BigInteger $amount;

    private string $currency;

    public static int $scale = 3;

    public static int $roundingMode = RoundingMode::HALF_UP;

    /**
     * Money constructor.
     * @param string $amount
     * @param string $currency
     */
    public function __construct(string $amount, string $currency = Currency::MYR)
    {
        $this->amount = BigInteger::of($amount);
        $this->currency = $currency;
    }

    public function __call($name, $arguments)
    {
        return BrickMoney::ofMinor($this->amount, $this->currency)->{$name}($arguments);
    }

    public static function parse($value, $currency = null)
    {
        $currency = $currency ?? Currency::default();

        if ($value instanceof Money) {
            $amount = $value->getAmount();
            $currency = $value->getCurrency();
        } elseif (is_array($value)) {
            $amount = data_get($value, 'amount', 0);
            $currency = data_get($value, 'currency', $currency);
        } else {
            $amount = $value;
        }

        return new static($amount, $currency);
    }

    public static function fromCents(int $amount, string $currency = Currency::MYR)
    {
        return new static($amount, $currency);
    }

    public static function fromDecimal(string $amount, string $currency = Currency::MYR)
    {
        $intAmount = BigDecimal::of($amount)
            ->toScale(static::$scale, static::$roundingMode)
            ->multipliedBy(100)
            ->toBigInteger();

        return new static($intAmount, $currency);
    }

    public function toDecimal(): string
    {
        return (string) BrickMoney::ofMinor($this->amount, $this->currency)
            ->getAmount()
            ->toScale(static::$scale, static::$roundingMode);
    }

    public function toCurrencyFormat()
    {
        $locale = Locale::$currencies[$this->currency];

        return BrickMoney::ofMinor($this->amount, $this->currency)->formatTo($locale);
    }

    public function getAmount(): int
    {
        return $this->amount->toInt();
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function add($value): Money
    {
        $value = BrickMoney::ofMinor($this->amount, $this->currency)->plus(
            static::parse($value),
            static::$roundingMode
        );

        return new static($value->getMinorAmount(), $this->currency);
    }

    public function subtract($value): Money
    {
        $value = BrickMoney::ofMinor($this->amount, $this->currency)->minus(
            static::parse($value),
            static::$roundingMode
        );

        return new static($value->getMinorAmount(), $this->currency);
    }

    public function multiply($value): Money
    {
        $value = BrickMoney::ofMinor($this->amount, $this->currency)->multipliedBy(
            $value,
            static::$roundingMode
        );

        return new static($value->getMinorAmount(), $this->currency);
    }

    public function divide($value): Money
    {
        $value = BrickMoney::ofMinor($this->amount, $this->currency)->dividedBy(
            $value,
            static::$roundingMode
        );

        return new static($value->getMinorAmount(), $this->currency);
    }

    public function withTax($amount): Money
    {
        $taxValue = static::parse($amount);

        return $this->add($taxValue);
    }

    public static function format(string $amount, string $currency): string
    {
        $money = new static($amount, $currency);

        return $money->toDecimal();
    }

    public static function formatWithSign(string $amount, string $currency)
    {
        $money = new static($amount, $currency);

        return $money->toCurrencyFormat();
    }

    public function __toString()
    {
        return $this->toDecimal();
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

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
