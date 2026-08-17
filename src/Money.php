<?php

declare(strict_types=1);

namespace Supplycart\Money;

use Brick\Math\BigDecimal;
use Brick\Math\BigNumber;
use Brick\Math\BigRational;
use Brick\Math\RoundingMode;
use Brick\Money\Context\CustomContext;
use Brick\Money\Currency as BrickCurrency;
use Brick\Money\Money as BrickMoney;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use InvalidArgumentException;
use JsonSerializable;
use Stringable;
use Supplycart\Money\Contracts\Tax as TaxContract;

/** @implements Arrayable<string, int|string> */
final class Money implements Arrayable, Jsonable, JsonSerializable, Stringable
{
    private readonly BrickMoney $instance;

    private ?TaxContract $tax = null;

    public static RoundingMode $roundingMode = RoundingMode::HalfUp;

    /** @var int<2, max> */
    public readonly int $scale;

    public function __construct(
        BigNumber|int|float|string|null $amount = 0,
        string $currency = Currency::MYR,
        int $scale = 2,
    ) {
        if ($scale < 2) {
            throw new InvalidArgumentException('Money scale must be two or greater.');
        }

        $this->scale = $scale;
        $this->instance = $this->createInstance($amount ?? 0, $currency, $scale);
    }

    public static function of(
        BigNumber|int|float|string|null $amount = 0,
        string $currency = Currency::MYR,
        int $decimal = 2,
    ): self {
        return new self($amount, $currency, $decimal);
    }

    /**
     * @param  array{amount?: BigNumber|int|float|string|null, currency?: string|null}|BigNumber|BrickMoney|Money|int|float|string|null  $value
     */
    public static function parse(
        BigNumber|BrickMoney|self|array|int|float|string|null $value,
        ?string $currency = null,
    ): self {
        $currency ??= Currency::default();

        if ($value instanceof self) {
            return new self($value->getAmount(), $value->getCurrency(), $value->scale);
        }

        if ($value instanceof BrickMoney) {
            return new self($value->getMinorAmount(), (string) $value->getCurrency());
        }

        if (is_array($value) && array_key_exists('amount', $value)) {
            return new self($value['amount'], $value['currency'] ?? $currency);
        }

        if (is_array($value)) {
            throw new InvalidArgumentException('A money array must contain an amount key.');
        }

        if (is_float($value)) {
            return new self(BigDecimal::of((string) $value)->getUnscaledValue(), $currency);
        }

        return new self($value, $currency);
    }

    public static function fromCents(int $amount, string $currency = Currency::MYR): self
    {
        $instance = BrickMoney::ofMinor($amount, $currency);

        return new self($instance->getMinorAmount(), $currency);
    }

    public static function fromDecimal(BigNumber|int|float|string $amount, string $currency = Currency::MYR): self
    {
        $instance = BrickMoney::of(self::normalizeNumber($amount), $currency);

        return new self($instance->getMinorAmount(), $currency);
    }

    public function getAmount(): int
    {
        return $this->instance
            ->getAmount()
            ->dividedBy($this->getDivider(), $this->scale, self::$roundingMode)
            ->getUnscaledValue()
            ->toInt();
    }

    /**
     * The argument is retained for backward compatibility; the instance scale
     * remains authoritative.
     */
    public function getDecimalAmount(int $scale = 2): string
    {
        return (string) $this->instance
            ->getAmount()
            ->dividedBy($this->getDivider(), $this->scale, self::$roundingMode)
            ->toScale($this->scale, self::$roundingMode);
    }

    /** @deprecated use `getDecimalAmount()` */
    public function toDecimal(): string
    {
        return $this->getDecimalAmount(2);
    }

    /** @deprecated use `format()` */
    public function toCurrencyFormat(): string
    {
        return $this->format();
    }

    public function format(?string $locale = null): string
    {
        $locale ??= Locale::$currencies[$this->getCurrency()] ?? 'en_MY';

        return $this->instance->formatToLocale($locale);
    }

    public function toNumberFormat(int $decimal = 2): string
    {
        return number_format((float) $this->getDecimalAmount(), $decimal);
    }

    public function getCurrency(): string
    {
        return (string) $this->instance->getCurrency();
    }

    public function add(BigNumber|self|int|float|string $value): self
    {
        if (! $value instanceof self) {
            $value = self::of($value, $this->getCurrency(), $this->scale);
        }

        $result = $this->instance->plus(
            $value->multiply($this->getDivider())->getDecimalAmount(),
            self::$roundingMode,
        );

        return new self($result->getMinorAmount(), $this->getCurrency(), $this->scale);
    }

    public function subtract(BigNumber|self|int|float|string $value): self
    {
        if (! $value instanceof self) {
            $value = self::of($value, $this->getCurrency(), $this->scale);
        }

        $result = $this->instance->minus(
            $value->multiply($this->getDivider())->getDecimalAmount(),
            self::$roundingMode,
        );

        return new self($result->getMinorAmount(), $this->getCurrency(), $this->scale);
    }

    public function multiply(BigNumber|int|float|string $value): self
    {
        $result = $this->instance->multipliedBy(self::normalizeNumber($value), self::$roundingMode);

        return new self($result->getMinorAmount(), (string) $result->getCurrency(), $this->scale);
    }

    public function divide(BigNumber|int|float|string $value): self
    {
        $result = $this->instance->dividedBy(self::normalizeNumber($value), self::$roundingMode);

        return new self($result->getMinorAmount(), (string) $result->getCurrency(), $this->scale);
    }

    public function withTax(TaxContract $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    public function getTaxAmount(BigNumber|int|float|string $quantity = 1): self
    {
        if (! $this->tax instanceof TaxContract) {
            return self::of(0, $this->getCurrency(), $this->scale);
        }

        $taxValue = $this->instance->toRational()
            ->multipliedBy($this->getTaxRate())
            ->multipliedBy(self::normalizeNumber($quantity))
            ->toContext($this->instance->getContext(), self::$roundingMode);

        return self::of($taxValue->getMinorAmount(), $this->getCurrency(), $this->scale);
    }

    public function getTaxAmountFromInclusiveTax(): self
    {
        if (! $this->tax instanceof TaxContract) {
            return $this;
        }

        $taxFromInclusive = $this->instance->toRational()
            ->multipliedBy($this->getTaxRate())
            ->dividedBy($this->getTaxRate()->plus(1))
            ->toContext($this->instance->getContext(), self::$roundingMode);

        return new self($taxFromInclusive->getMinorAmount(), $this->getCurrency(), $this->scale);
    }

    public function getTaxRate(): BigDecimal
    {
        if (! $this->tax instanceof TaxContract) {
            return BigDecimal::zero();
        }

        return BigRational::of($this->tax->getTaxRate())
            ->dividedBy(100)
            ->toScale($this->scale, self::$roundingMode);
    }

    public function afterTax(BigNumber|int|float|string $quantity = 1): self
    {
        if (! $this->tax instanceof TaxContract) {
            return $this;
        }

        $afterTax = $this->instance->toRational()
            ->multipliedBy($this->getTaxRate()->plus(1))
            ->multipliedBy(self::normalizeNumber($quantity))
            ->toContext($this->instance->getContext(), self::$roundingMode);

        return new self($afterTax->getMinorAmount(), $this->getCurrency(), $this->scale);
    }

    public function beforeTax(): self
    {
        if (! $this->tax instanceof TaxContract) {
            return $this;
        }

        $beforeTax = $this->instance->toRational()
            ->dividedBy($this->getTaxRate()->plus(1))
            ->toContext($this->instance->getContext(), self::$roundingMode);

        return new self($beforeTax->getMinorAmount(), $this->getCurrency(), $this->scale);
    }

    public static function zero(string $currency = Currency::MYR): self
    {
        return new self(0, $currency);
    }

    public function isZero(): bool
    {
        return $this->instance->isZero();
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->getDecimalAmount(2);
    }

    /** @return array{amount: int, currency: string} */
    #[\Override]
    public function toArray(): array
    {
        return [
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
        ];
    }

    #[\Override]
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options | JSON_THROW_ON_ERROR);
    }

    /** @return array{amount: int, currency: string} */
    #[\Override]
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getDivider(): int
    {
        return $this->scale === 2 ? 1 : 10 ** ($this->scale - 2);
    }

    public function convertToDifferentDecimalPoint(int $newDecimalPoint): self
    {
        if ($newDecimalPoint < 2) {
            throw new InvalidArgumentException('Money scale must be two or greater.');
        }

        $differenceInScale = $newDecimalPoint - $this->scale;
        $dividerOrMultiplier = 10 ** abs($differenceInScale);

        $newValue = $this->scale < $newDecimalPoint
            ? $this->instance->multipliedBy($dividerOrMultiplier, self::$roundingMode)
            : $this->instance->dividedBy($dividerOrMultiplier, self::$roundingMode);

        return new self($newValue->getMinorAmount(), (string) $newValue->getCurrency(), $newDecimalPoint);
    }

    /** @param int<2, max> $scale */
    private function createInstance(
        BigNumber|int|float|string $amount = 0,
        string $currency = Currency::MYR,
        int $scale = 2,
    ): BrickMoney {
        $currencyDefinition = BrickCurrency::of($currency);
        $currencyDefinition = new BrickCurrency(
            $currencyDefinition->getCurrencyCode(),
            $currencyDefinition->getNumericCode(),
            $currencyDefinition->getName(),
            2,
        );

        $context = new CustomContext($scale);
        $decimalAmount = BigRational::of(self::normalizeNumber($amount))
            ->dividedBy(10 ** $currencyDefinition->getDefaultFractionDigits());

        return BrickMoney::of($decimalAmount, $currencyDefinition, $context, self::$roundingMode);
    }

    private static function normalizeNumber(BigNumber|int|float|string $value): BigNumber|int|string
    {
        return is_float($value) ? (string) $value : $value;
    }
}
