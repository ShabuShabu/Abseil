<?php

declare(strict_types=1);

namespace ShabuShabu\Abseil;

use Illuminate\Support\{Arr, Enumerable, Facades\Route, Str};
use InvalidArgumentException;
use LogicException;

/**
 * @return \Illuminate\Support\Enumerable
 */
function morph_map(): Enumerable
{
    $location = config('abseil.morph_map_location');
    $morphMap = "$location::MORPH_MAP";

    if (! defined($morphMap)) {
        throw new InvalidArgumentException("Constant [$morphMap] was not found");
    }

    return collect(constant($morphMap));
}

/**
 * @return string
 */
function resource_namespace(): string
{
    return rtrim(config('abseil.resource_namespace'), '\\') . '\\';
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
 * @return array
 */
function inflate(iterable $array): iterable
{
    $inflated = [];
    foreach ($array as $key => $value) {
        Arr::set($inflated, $key, $value);
    }

    return $inflated;
}

/**
 * @param string        $namespace
 * @param string|object $model
 * @param string        $suffix
 * @return string|null
 */
function get_first_resource(string $namespace, $model, string $suffix = '')
{
    $namespace = rtrim($namespace, '\\') . '\\';
    $resource  = $namespace . class_basename($model) . $suffix;

    while (! class_exists($resource)) {
        $current = is_string($model) ? $model : get_class($model);

        if (! $model = get_parent_class($model)) {
            throw new LogicException("No parent class found for [$current].");
        }

        $resource = $namespace . class_basename($model) . $suffix;
    }

    return $resource;
}

/**
 * @param string   $string
 * @param int|null $len
 * @return string
 */
function tokenize(string $string, ?int $len = null): string
{
    return implode('.', array_slice(explode('.', $string), 0, $len));
}

/**
 * @param string $string
 * @return array
 */
function tokenize_all(string $string): array
{
    $tokens = [];
    if (($count = substr_count($string, '.')) > 0) {
        $i = 1;

        while ($i <= $count) {
            $tokens[] = tokenize($string, $i);
            $i++;
        }
    }

    $tokens[] = $string;

    return array_unique($tokens);
}
