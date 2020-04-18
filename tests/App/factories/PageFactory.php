<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use ShabuShabu\Abseil\Tests\App\{Category, Page, User};

$factory->define(Page::class, fn(Faker $faker) => [
    'user_id'     => null,
    'category_id' => null,
    'title'       => $faker->sentence,
    'content'     => $faker->paragraphs(3, true),
]);

$factory->state(Page::class, 'withId', fn() => [
    'id' => Str::orderedUuid()->toString(),
]);

$factory->state(Page::class, 'withUser', fn() => [
    'user_id' => factory(User::class),
]);

$factory->state(Page::class, 'withCategory', fn() => [
    'category_id' => factory(Category::class),
]);
