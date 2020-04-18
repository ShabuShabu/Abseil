<?php

namespace ShabuShabu\Abseil\Http\Resources;

use Illuminate\Http\Resources\MissingValue;
use LogicException;

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
        $namespace = rtrim(__NAMESPACE__, '\\') . '\\';
        $resource  = $namespace . class_basename($related);

        while (! class_exists($resource)) {
            if (! $related = get_parent_class($related)) {
                throw new LogicException("No parent class found for [$related]");
            }

            $resource = $namespace . class_basename($related);
        }

        return $resource;
    }
}
