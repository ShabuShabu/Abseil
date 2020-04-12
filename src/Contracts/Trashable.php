<?php

namespace ShabuShabu\Abseil\Contracts;

interface Trashable
{
    /**
     * Get the value for the location header
     *
     * @return bool
     */
    public function trashed();

    /**
     * Determine if the model instance has been soft-deleted.
     *
     * @return bool
     */
    public function restore();

    /**
     * Fully delete the model or just trash it
     *
     * @return bool
     */
    public function trashOrDelete(): bool;
}
