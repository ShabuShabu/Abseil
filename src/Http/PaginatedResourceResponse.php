<?php

namespace ShabuShabu\Abseil\Http;

use Illuminate\Http\Resources\Json\PaginatedResourceResponse as Response;
use Illuminate\Support\Collection;
use function ShabuShabu\Abseil\to_camel_case;

class PaginatedResourceResponse extends Response
{
    /**
     * Add the pagination information to the response.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function paginationInformation($request): array
    {
        $paginated = $this->resource->resource->toArray();

        return [
            'links' => $this->paginationLinks($paginated),
            'meta'  => [
                'pagination' => $this->meta($paginated),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function meta($paginated): array
    {
        return collect(parent::meta($paginated))
            ->map(
                fn($value) => is_numeric($value) ? (int)$value : $value
            )->pipe(
                fn(Collection $meta) => to_camel_case($meta->toArray())
            );
    }
}
