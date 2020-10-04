<?php

namespace Supplycart\Money\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Supplycart\Money\Money;
use Supplycart\Money\Tests\TestCase;

class MoneyTest extends TestCase
{
    public function test_can_get_amount_from_money()
    {
        $money = new Money(1000);

        $this->assertEquals(1000, $money->getAmount());
    }

    public function test_can_get_decimal_value_from_money()
    {
        $money = new Money(1000);

        $this->assertEquals(10.000, $money->toDecimal());
    }

    public function test_can_get_currency_format()
    {
        $money = new Money(1000);

        $this->assertStringContainsString("RM", $money->toCurrencyFormat());
        $this->assertStringContainsString("10.00", $money->toCurrencyFormat());
        $this->assertTrue(true);
    }

    public function test_can_add_money()
    {
        $money = new Money(1000);
        $money2 = new Money(500);

        $this->assertEquals(1500, $money->add($money2)->getAmount());
    }

    public function test_can_minus_money()
    {
        $money = new Money(1000);
        $money2 = new Money(500);

        $this->assertEquals(500, $money->subtract($money2)->getAmount());
    }

    public function test_can_multiply_money()
    {
        $money = new Money(1000);

        $this->assertEquals(5000, $money->multiply(5)->getAmount());
    }

    public function test_can_divide_money()
    {
        $money = new Money(1000);

        $this->assertEquals(200, $money->divide(5)->getAmount());
    }
}
