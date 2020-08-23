<?php

namespace ShabuShabu\Abseil\Contracts;

interface Abseil
{
    /**
     * Get the allowed includes
     *
     * @return array
     */
    public static function allowedIncludes(): array;

    /**
     * Get the JSON type
     *
     * @return string
     */
    public static function jsonType(): string;

    /**
     * Get the route param name
     *
     * @return string
     */
    public static function routeParam(): string;
}
