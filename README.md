# Supplycart Money

A small Laravel 13 value-object package for storing and calculating monetary
amounts as integers. It supports configurable precision, currency-aware
formatting, Eloquent casting, arithmetic, and tax calculations through a simple
contract.

## Requirements

- PHP 8.5 or later with `intl` and `json`
- Laravel 13

## Installation

```bash
composer require supplycart/money
php artisan vendor:publish --tag=money-config
```

The service provider is discovered automatically by Laravel.

## Usage

Amounts passed to the constructor or `of()` are integers in the configured
minor precision:

```php
use Supplycart\Money\Money;

$price = Money::of(1_250, 'MYR');

$price->getAmount();       // 1250
$price->getDecimalAmount(); // "12.50"
$price->format();           // localized MYR output
```

Use `fromDecimal()` when the input is a decimal major-unit value:

```php
$price = Money::fromDecimal('12.50', 'MYR');
```

Four-decimal storage is supported for calculations that require extra
precision:

```php
$price = Money::of(12_500, 'MYR', 4);

$price->getDecimalAmount(); // "1.2500"
```

### Eloquent cast

```php
use Illuminate\Database\Eloquent\Model;
use Supplycart\Money\Casts\MoneyValue;

final class Product extends Model
{
    protected function casts(): array
    {
        return [
            'unit_price' => MoneyValue::class,
        ];
    }
}
```

If the model has a currency attribute, the cast uses it. Models may expose a
`getDecimalValue()` method to select a precision greater than the default two
decimal places.

### Tax calculations

Implement `Supplycart\Money\Contracts\Tax`, attach it with `withTax()`, then
use `getTaxAmount()`, `afterTax()`, or `beforeTax()`.

## Development

Run the same quality gates used by CI:

```bash
composer lint:check
composer analyse
composer test
```

`composer lint` applies Laravel Pint formatting. PHPStan runs at level `max`.
The reusable coverage checker in `scripts/check-coverage.php` enforces an 85%
minimum against a Clover report when a coverage driver is available.
