<?php

declare(strict_types=1);

namespace Supplycart\Money\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;
use Supplycart\Money\Casts\MoneyValue;
use Supplycart\Money\Money;

/**
 * @property-read Money $unit_price
 * @property-write Money|array<string, mixed>|int|float|string|null $unit_price
 */
final class Product extends Model
{
    /** @var list<string> */
    protected $fillable = ['unit_price'];

    /** @return array<string, class-string> */
    #[\Override]
    protected function casts(): array
    {
        return [
            'unit_price' => MoneyValue::class,
        ];
    }
}
