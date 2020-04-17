<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use ShabuShabu\Abseil\Tests\Support\Page;

$factory->define(Page::class, fn(Faker $faker) => [
    'id'      => Str::orderedUuid()->toString(),
    'user_id' => null,
    'title'   => $faker->sentence,
    'content' => $faker->paragraphs(3, true),
]);
