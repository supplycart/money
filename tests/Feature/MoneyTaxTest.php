<?php

namespace Supplycart\Money\Tests\Feature;

use Orchestra\Testbench\TestCase;
use Supplycart\Money\Country;
use Supplycart\Money\Currency;
use Supplycart\Money\Money;

class MoneyTaxTest extends TestCase
{
    public function test_can_get_tax_amount_for_a_money()
    {
        $money = Money::of(252)->withTax(new Tax);

        $this->assertEquals(252, $money->getAmount());
        $this->assertEquals('0.06', $money->getTaxRate());
        $this->assertEquals('0.15', $money->getTaxAmount());
        $this->assertEquals('12.10', (string) $money->getTaxAmount(80));
    }

    public function test_can_get_tax_amount_for_a_money_for_4_decimal_place()
    {
        $money = Money::of(10000, 'MYR', 4)->withTax(new Tax);

        $this->assertEquals(10000, $money->getAmount());
        $this->assertEquals('0.0600', $money->getTaxRate());
        $this->assertEquals('0.0600', $money->getTaxAmount());
        $this->assertEquals('4.8000', (string) $money->getTaxAmount(80));
    }

    public function test_can_get_after_tax_amount()
    {
        $money = Money::of(252)->withTax(new Tax);
        $this->assertEquals(267, $money->afterTax()->getAmount());

        $money = Money::of(252)->withTax(new Tax);
        $this->assertEquals(16829, $money->afterTax(63)->getAmount());

        $money = Money::of(252);
        $this->assertEquals(252, $money->afterTax()->getAmount());
    }

    public function test_can_get_after_tax_amount_for_4_decimal()
    {
        $money = Money::of(10000, 'MYR', 4)->withTax(new Tax);

        $this->assertEquals(10600, $money->afterTax()->getAmount());
        $this->assertEquals(667800, $money->afterTax(63)->getAmount());
        $this->assertEquals(66.7800, $money->afterTax(63)->getDecimalAmount());

        $money = Money::of(10000, 'MYR', 4);
        $this->assertEquals(10000, $money->afterTax()->getAmount());
        $this->assertEquals(1.0000, $money->afterTax()->getDecimalAmount());
    }

    public function test_can_get_before_tax_amount()
    {
        $money = Money::of(267)->withTax(new Tax);
        $this->assertEquals(252, $money->beforeTax()->getAmount());
        $this->assertEquals(2.52, $money->beforeTax()->getDecimalAmount());
    }

    public function test_can_get_before_tax_amount_for_4_decimal_place()
    {
        $money = Money::of(10600,'MYR', 4)->withTax(new Tax);
        $this->assertEquals(10000, $money->beforeTax()->getAmount());
        $this->assertEquals(1.0000, $money->beforeTax()->getDecimalAmount());
    }

    public function test_can_get_tax_from_price_incl_tax()
    {
        $money = Money::of(267)->withTax(new Tax);
        $this->assertEquals(15, $money->getTaxAmountFromInclusiveTax()->getAmount());
        $this->assertEquals(0.15, $money->getTaxAmountFromInclusiveTax()->getDecimalAmount());
    }

    public function test_can_get_tax_from_price_incl_tax_for_4_decimal_place()
    {
        $money = Money::of(10600, 'MYR', 4)->withTax(new Tax);
        $this->assertEquals(600, $money->getTaxAmountFromInclusiveTax()->getAmount());
        $this->assertEquals(0.0600, $money->getTaxAmountFromInclusiveTax()->getDecimalAmount());
    }
}

class Tax implements \Supplycart\Money\Contracts\Tax
{

    #[\Override]
    public function getTaxRate(): string
    {
        return '6.0';
    }

    #[\Override]
    public function getTaxDescription(): string
    {
        return '';
    }

    #[\Override]
    public function getTaxCountry(): string
    {
        return Country::MALAYSIA;
    }

    #[\Override]
    public function getTaxCurrency(): string
    {
        return Currency::MYR;
    }
}
