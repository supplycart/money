<?php

namespace Supplycart\Money\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;
use Supplycart\Money\Casts\MoneyValue;

class Product extends Model
{
    protected $fillable = ['unit_price'];

    protected $casts = [
        'unit_price' => MoneyValue::class,
    ];
}