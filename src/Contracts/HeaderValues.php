<?php

namespace ShabuShabu\Abseil\Contracts;

interface HeaderValues
{
    /**
     * Get the value for the location header
     *
     * @return string
     */
    public function url(): string;

    /**
     * Get the id for the model
     *
     * @return mixed
     */
    public function getKey();
}
