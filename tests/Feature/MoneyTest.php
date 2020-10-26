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

        $this->assertEquals(10.000, $money->getDecimalAmount());
    }

    public function test_can_get_currency_format()
    {
        $money = new Money(1000);

        $this->assertStringContainsString("RM", $money->format());
        $this->assertStringContainsString("10.00", $money->format());
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

    public function test_can_create_zero_money()
    {
        $money = Money::zero();

        $this->assertEquals(0, $money->getAmount());
    }

    public function test_can_check_money_is_zero()
    {
        $money = Money::zero();

        $this->assertTrue($money->isZero());
    }

    public function test_can_convert_money_to_array()
    {
        $money = Money::zero();

        $this->assertIsArray((array) $money);
        $this->assertEquals([
            'amount' => 0,
            'currency' => 'MYR'
        ], $money->toArray());

        $money = Money::of(252);

        $this->assertIsArray((array) $money);
        $this->assertEquals([
            'amount' => 252,
            'currency' => 'MYR'
        ], $money->toArray());
    }
}
