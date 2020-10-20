<?php

namespace Supplycart\Money;

use Illuminate\Database\Eloquent\Model;
use Supplycart\Money\Contracts\Tax as TaxContract;

class Tax extends Model implements TaxContract
{
    protected $fillable = [
        'name',
        'description',
        'rate',
        'country',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'rate' => 'float',
        'is_active' => 'boolean',
    ];

    public function getTaxRate(): string
    {
        return (string) $this->rate;
    }

    public function getTaxDescription(): string
    {
        return (string) $this->description;
    }

    public function getTaxCountry(): string
    {
        return (string) $this->country;
    }

    public function getTaxCurrency(): string
    {
        return (string) $this->currency;
    }
}