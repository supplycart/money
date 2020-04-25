<?php

namespace Supplycart\Money;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use Money\Money as MoneyPHP;
use NumberFormatter;

/**
 * Class Money
 * @package App\Domains\Shared\Money
 *
 * @method string getCurrency()
 */
class Money implements Arrayable, JsonSerializable
{
    /**
     * @var string
     */
    private string $amount;

    /**
     * @var string
     */
    private string $currency;

    /**
     * @var \Money\Money
     */
    private MoneyPHP $instance;

    /**
     * Money constructor.
     * @param string $amount
     * @param string $currency
     */
    public function __construct(string $amount, string $currency = Currency::MYR)
    {
        $this->amount = $amount;
        $this->currency = $currency;

        $this->instance = new MoneyPHP($amount, new \Money\Currency($currency));
    }

    public function __call($name, $arguments)
    {
        return $this->instance->{$name}($arguments);
    }

    public function getAmount(): string
    {
        return (string) $this->amount;
    }

    public static function fromMoney(MoneyPHP $money)
    {
        return new static($money->getAmount(), $money->getCurrency()->getCode());
    }

    public function toDecimal(): float
    {
        return (float) $this->instance->getAmount() / 100;
    }

    public function toDecimalFormat()
    {
        $currencies = new ISOCurrencies();

        $currencyConfig = config("currency.format.{$this->currency}");
        $locale = data_get($currencyConfig, 'locale', config('app.locale'));

        $numberFormatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        return $moneyFormatter->format($this->getInstance());
    }

    public function toCurrencyFormat()
    {
        $currencies = new ISOCurrencies();

        $currencyConfig = config("currency.format.{$this->currency}");
        $locale = data_get($currencyConfig, 'locale', config('app.locale'));
        $format = data_get($currencyConfig, 'formatWithSign', data_get($currencyConfig, 'format'));

        $numberFormatter = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        $format && $numberFormatter->setPattern($format);

        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

        return $moneyFormatter->format($this->getInstance());
    }

    public function getInstance(): MoneyPHP
    {
        return $this->instance;
    }

    public function add($amount): Money
    {
        if ($amount instanceof Money) {
            $amount = $amount->getInstance();
        } else {
            $amount = new MoneyPHP($amount, $this->instance->getCurrency());
        }

        return static::fromMoney($amount->add($amount));
    }

    public function subtract($amount): Money
    {
        if ($amount instanceof Money) {
            $amount = $amount->getInstance();
        } else {
            $amount = new MoneyPHP($amount, $this->instance->getCurrency());
        }

        return static::fromMoney($amount->subtract($amount));
    }

    public function multiply($number): Money
    {
        return static::fromMoney($this->instance->multiply($number));
    }

    public function divide($number): Money
    {
        return static::fromMoney($this->instance->divide($number));
    }

    public function withTax($amount): Money
    {
        if ($amount instanceof Money) {
            $tax = $amount->getInstance();
        } else {
            $tax = new MoneyPHP($amount, $this->instance->getCurrency());
        }

        return static::fromMoney($this->instance->add($tax));
    }

    public static function format(string $amount, string $currency): string
    {
        $money = new static($amount, $currency);

        return $money->toDecimalFormat();
    }

    public static function formatWithSign(string $amount, string $currency)
    {
        $money = new static($amount, $currency);

        return $money->toCurrencyFormat();
    }

    public function __toString()
    {
        return (string) $this->toDecimal();
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'amount' => (int) $this->instance->getAmount(),
            'currency' => $this->instance->getCurrency()->getCode(),
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
