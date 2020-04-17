<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use ShabuShabu\Abseil\Tests\App\User;

$factory->define(User::class, fn(Faker $faker) => [
    'id'    => null,
    'name'  => $faker->name,
    'email' => $faker->unique()->safeEmail,
]);

$factory->state(User::class, 'withId', fn() => [
    'id' => Str::orderedUuid()->toString(),
]);

$factory->state(User::class, 'trashed', fn() => [
    'deleted_at' => now(),
]);
