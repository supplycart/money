<?php

namespace Supplycart\Money\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Orchestra\Testbench\TestCase;
use Supplycart\Money\Money;

class MoneyParseTest extends TestCase
{
    public function test_can_parse_money_from_string()
    {
        $money = Money::parse('1000');

        $this->assertEquals(1000, $money->getAmount());
    }

    public function test_can_parse_money_from_integer()
    {
        $money = Money::parse(1000);

        $this->assertEquals(1000, $money->getAmount());
    }

    public function test_can_parse_money_from_array()
    {
        $money = Money::parse(['amount' => 1200, 'currency' => 'MYR']);

        $this->assertEquals(1200, $money->getAmount());
    }

    public function test_can_parse_money_from_money_object()
    {
        $money = Money::parse(new Money(1500));

        $this->assertEquals(1500, $money->getAmount());
    }
}
