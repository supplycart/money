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
}

class Tax implements \Supplycart\Money\Contracts\Tax
{

    public function getTaxRate(): string
    {
        return '6.0';
    }

    public function getTaxDescription(): string
    {
        return '';
    }

    public function getTaxCountry(): string
    {
        return Country::MALAYSIA;
    }

    public function getTaxCurrency(): string
    {
        return Currency::MYR;
    }
}
