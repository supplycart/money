<?php

declare(strict_types=1);

/** @var Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Supplycart\Money\Country;
use Supplycart\Money\Tax;

$factory->define(Tax::class, fn (Faker $faker) => [
    'name' => $name = $faker->randomElement(['GST', 'SST']),
    'rate' => $rate = $faker->randomFloat(2, 0, 20),
    'description' => $faker->sentence(3),
    'country' => $faker->randomElement(Country::options()),
    'is_active' => $faker->boolean(90),
]);
