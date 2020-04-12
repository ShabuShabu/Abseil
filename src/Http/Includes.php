<?php

namespace ShabuShabu\Abseil\Http;

use Illuminate\Http\Resources\MissingValue;
use function ShabuShabu\Abseil\get_first_resource;

trait Includes
{
    /**
     * {@inheritDoc}
     */
    public function with($request): array
    {
        if (($includes = $this->includes()) instanceof MissingValue) {
            return [];
        }

        return [
            'includes' => $includes,
        ];
    }

    /**
     * @param mixed $related
     * @return string
     */
    protected function resourceClass($related): string
    {
        return get_first_resource(__NAMESPACE__, $related);
    }
}
