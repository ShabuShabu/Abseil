<?php

declare(strict_types=1);

namespace ShabuShabu\Abseil;

use Illuminate\Support\{Arr, Collection, Facades\Route, Str};
use InvalidArgumentException;

/**
 * @return \Illuminate\Support\Collection
 */
function morph_map(): Collection
{
    $location = config('abseil.morph_map_location');
    $morphMap = $location . '::MORPH_MAP';

    if (! defined($morphMap)) {
        throw new InvalidArgumentException("Constant [$morphMap] was not found");
    }

    return collect(constant($morphMap));
}

/**
 * @return bool
 */
function is_authenticated_request(): bool
{
    if (! $route = Route::current()) {
        return false;
    }

    return in_array(config('abseil.auth_middleware'), $route->gatherMiddleware(), true);
}

/**
 * Transform array keys to camelCase
 *
 * @param array $data
 * @return array
 */
function to_camel_case(array $data): array
{
    $out = [];
    foreach ($data as $key => $sub) {
        $out[Str::camel($key)] = is_array($sub) ? to_camel_case($sub) : $sub;
    }

    return $out;
}

/**
 * Check if a class exists
 *
 * @param $resource
 * @throws InvalidArgumentException
 */
function resource_guard($resource): void
{
    if (! class_exists($resource)) {
        throw new InvalidArgumentException("Resource [$resource] does not exist");
    }
}

/**
 * @param iterable $array
 * @param bool     $asArray
 * @return array
 */
function inflate(iterable $array, bool $asArray = true): iterable
{
    $inflated = [];
    foreach ($array as $key => $value) {
        Arr::set($inflated, $key, $value);
    }

    return $asArray ? $inflated : collect($inflated);
}
