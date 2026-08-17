<?php

declare(strict_types=1);

namespace Supplycart\Money\Tests\Feature;

use InvalidArgumentException;
use stdClass;
use Supplycart\Money\Currency;
use Supplycart\Money\Money;
use Supplycart\Money\Tests\Stubs\Product;
use Supplycart\Money\Tests\TestCase;

final class MoneyValueTest extends TestCase
{
    public function test_can_save_money_value_using_string(): void
    {
        $product = new Product;
        $product->unit_price = '100';

        $this->assertEquals(new Money(100), $product->unit_price);
    }

    public function test_can_save_money_value_using_money_object(): void
    {
        $product = new Product;
        $product->unit_price = new Money(100);

        $this->assertEquals(new Money(100), $product->unit_price);
    }

    public function test_can_save_money_value_using_array(): void
    {
        $product = new Product;
        $product->unit_price = [
            'amount' => 100,
        ];

        $this->assertEquals(new Money(100), $product->unit_price);
    }

    public function test_cast_uses_the_model_currency(): void
    {
        $product = new Product;
        $product->setAttribute('currency', Currency::SGD);
        $product->unit_price = 100;

        $this->assertSame(Currency::SGD, $product->unit_price->getCurrency());
    }

    public function test_cast_rejects_unsupported_values(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $product = new Product;
        $product->setAttribute('unit_price', new stdClass);
    }
}
