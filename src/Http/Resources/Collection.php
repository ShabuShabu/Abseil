<?php

namespace ShabuShabu\Abseil\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Enumerable;

class Collection extends ResourceCollection
{
    use Includes;

    /**
     * Create a new resource collection.
     *
     * @param mixed       $resource
     * @param string|null $collects
     */
    public function __construct($resource, string $collects = null)
    {
        $this->collects = $collects;

        parent::__construct($resource);
    }

    /**
     * {@inheritDoc}
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
        ];
    }

    /**
     * @return array|\Illuminate\Http\Resources\MissingValue
     */
    protected function includes()
    {
        /** @var Enumerable $includes */
        $includes = $this->resource->reduce(function (BaseCollection $collection, Resource $resource) {
            $includes = collect($resource->resource::allowedIncludes())
                ->reduce(
                    fn(BaseCollection $includes, string $relation) => $this->included(
                        $includes,
                        $relation,
                        $resource->resource
                    ),
                    collect()
                );

            return $collection->merge($includes);
        }, collect());

        $includes = $includes->unique('id')
                             ->values()
                             ->filter()
                             ->toArray();

        return empty($includes) ? new MissingValue() : $includes;
    }

    /**
     * @param \Illuminate\Support\Enumerable      $includes
     * @param string                              $relation
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Support\Enumerable
     */
    protected function included(Enumerable $includes, string $relation, Model $model): Enumerable
    {
        $included = $this->isLoaded($model, $relation, function () use ($relation, $model) {
            $resource = $this->resourceClass($model->{$relation});

            return new $resource($model->{$relation});
        }, []);

        return $includes->push($included);
    }

    /**
     * Retrieve a relationship if it has been loaded.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $relationship
     * @param mixed                               $value
     * @param mixed                               $default
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function isLoaded(Model $model, $relationship, $value = null, $default = null)
    {
        if (func_num_args() < 4) {
            $default = new MissingValue;
        }

        if (! $model->relationLoaded($relationship)) {
            return value($default);
        }

        if ($model->{$relationship} === null) {
            return null;
        }

        return value($value);
    }

    /**
     * {@inheritdoc}
     */
    public function toResponse($request): JsonResponse
    {
        return $this->resource instanceof AbstractPaginator
            ? (new PaginatedResourceResponse($this))->toResponse($request)
            : parent::toResponse($request);
    }

    /**
     * {@inheritDoc}
     */
    public function withResponse($request, $response): void
    {
        $response->withHeaders(Resource::DEFAULT_HEADERS);
    }
}
