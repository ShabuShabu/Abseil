<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use ShabuShabu\Abseil\Tests\App\{Category};

$factory->define(Category::class, fn(Faker $faker) => [
    'id'      => null,
    'title'   => $faker->sentence,
    'content' => $faker->paragraphs(3, true),
]);

$factory->state(Category::class, 'withId', fn() => [
    'id' => Str::orderedUuid()->toString(),
]);
