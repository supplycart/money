<?php

declare(strict_types=1);

namespace Supplycart\Money\Tests\Feature;

use Brick\Money\Money as BrickMoney;
use InvalidArgumentException;
use Orchestra\Testbench\TestCase;
use Supplycart\Money\Money;

final class MoneyParseTest extends TestCase
{
    public function test_can_parse_money_from_string(): void
    {
        $money = Money::parse('1000');

        $this->assertEquals(1000, $money->getAmount());
    }

    public function test_can_parse_money_from_integer(): void
    {
        $money = Money::parse(1000);

        $this->assertEquals(1000, $money->getAmount());
    }

    public function test_can_parse_money_from_array(): void
    {
        $money = Money::parse(['amount' => 1200, 'currency' => 'MYR']);

        $this->assertEquals(1200, $money->getAmount());
    }

    public function test_can_parse_money_from_money_object(): void
    {
        $money = Money::parse(new Money(1500, 'MYR', 4));

        $this->assertEquals(1500, $money->getAmount());
        $this->assertSame(4, $money->scale);
    }

    public function test_can_parse_brick_money(): void
    {
        $money = Money::parse(BrickMoney::ofMinor(1500, 'SGD'));

        $this->assertSame(1500, $money->getAmount());
        $this->assertSame('SGD', $money->getCurrency());
    }

    public function test_money_array_requires_an_amount(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Money::parse(['currency' => 'MYR']);
    }
}
