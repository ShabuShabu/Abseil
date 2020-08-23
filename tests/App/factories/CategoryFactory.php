<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use ShabuShabu\Abseil\Tests\App\{Category};

$factory->define(Category::class, fn(Faker $faker) => [
    'title' => $faker->word,
]);

$factory->state(Category::class, 'withId', fn() => [
    'id' => Str::orderedUuid()->toString(),
]);
