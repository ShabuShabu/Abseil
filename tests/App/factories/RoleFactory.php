<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use ShabuShabu\Abseil\Tests\App\Role;

$factory->define(Role::class, fn(Faker $faker) => [
    'name' => $faker->word,
]);
