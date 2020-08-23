<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use ShabuShabu\Abseil\Tests\App\User;

$factory->define(User::class, fn(Faker $faker) => [
    'name'           => $faker->name,
    'email'          => $faker->unique()->safeEmail,
    'is_admin'       => false,
    'password'       => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
    'remember_token' => Str::random(10),
]);

$factory->state(User::class, 'withId', fn() => [
    'id' => Str::orderedUuid()->toString(),
]);

$factory->state(User::class, 'admin', fn() => [
    'is_admin' => true,
]);

$factory->state(User::class, 'trashed', fn() => [
    'deleted_at' => now(),
]);
