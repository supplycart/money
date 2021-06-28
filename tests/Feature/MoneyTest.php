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

    public function test_can_get_decimal_value_from_money_for_4_decimal_point()
    {
        $money = new Money(10000, 'MYR', 4);

        $this->assertEquals(1.0000, $money->getDecimalAmount());
    }

    public function test_can_get_currency_format()
    {
        $money = new Money(1000);

        $this->assertStringContainsString("RM", $money->format());
        $this->assertStringContainsString("10.00", $money->format());
        $this->assertTrue(true);
    }

    public function test_can_add_integer()
    {
        $money = new Money(1000);

        $this->assertEquals(1500, $money->add(500)->getAmount());
    }

    public function test_can_add_money()
    {
        $money = new Money(1000);
        $money2 = new Money(500);

        $this->assertEquals(1500, $money->add($money2)->getAmount());
    }

    public function test_can_add_money_for_4_decimal_place()
    {
        $money = new Money(10000, 'MYR', 4);
        $money2 = new Money(500, 'MYR', 4);

        $this->assertEquals(10500, $money->add($money2)->getAmount());
        $this->assertEquals(1.0500, $money->add($money2)->getDecimalAmount());
    }

    public function test_can_minus_money()
    {
        $money = new Money(1000);
        $money2 = new Money(500);

        $this->assertEquals(500, $money->subtract($money2)->getAmount());
    }

    public function test_can_minus_money_for_4_decimal_place()
    {
        $money = new Money(10000, 'MYR', 4);
        $money2 = new Money(500, 'MYR', 4);

        $this->assertEquals(9500, $money->subtract($money2)->getAmount());
        $this->assertEquals(0.9500, $money->subtract($money2)->getDecimalAmount());
    }

    public function test_can_multiply_money()
    {
        $money = new Money(1000);

        $this->assertEquals(5000, $money->multiply(5)->getAmount());
    }

    public function test_can_multiply_money_for_4_decimal_place()
    {
        $money = new Money(10000, 'MYR', 4);

        $this->assertEquals(50000, $money->multiply(5)->getAmount());
        $this->assertEquals(5.0000, $money->multiply(5)->getDecimalAmount());
    }

    public function test_can_divide_money()
    {
        $money = new Money(1000);

        $this->assertEquals(200, $money->divide(5)->getAmount());
    }

    public function test_can_divide_money_for_4_decimal_place()
    {
        $money = new Money(10000, 'MYR', 4);

        $this->assertEquals(2000, $money->divide(5)->getAmount());
        $this->assertEquals(0.2000, $money->divide(5)->getDecimalAmount());
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

    public function test_can_create_money_from_decimal()
    {
        $this->assertEquals(1500, Money::fromDecimal(15.0)->getAmount());
        $this->assertEquals(1500, Money::fromDecimal(15.00)->getAmount());
        $this->assertEquals(1500, Money::fromDecimal(15)->getAmount());
        $this->assertEquals(1500, Money::fromDecimal('15.0')->getAmount());
        $this->assertEquals(1500, Money::fromDecimal('15')->getAmount());
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

    public function test_can_multiply_four_decimal()
    {
        $money = Money::of(90001, 'MYR', 4);
        $result = $money->multiply(1.0001)->getAmount();
        $this->assertEquals(90010, $result);

        $money = Money::of(1000001, 'MYR', 4);
        $result1 = $money->multiply(9.1236)->getAmount();
        $this->assertEquals(9123609, $result1);
    }

    public function test_number_format_working()
    {
        $money = Money::of(12341234, 'MYR', 4);
        $result = $money->toNumberFormat(2);

        $this->assertEquals('1,234.12', $result);
    }

}
