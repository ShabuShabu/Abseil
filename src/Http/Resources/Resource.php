<?php

namespace ShabuShabu\Abseil\Http\Resources;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\{Collection, Enumerable};
use ShabuShabu\Abseil\Model;
use stdClass;

/**
 * App\Http\Resources\Resource
 */
class Resource extends JsonResource
{
    use Includes;

    public const MEDIA_TYPE = 'application/vnd.api+json';

    public const DEFAULT_HEADERS = [
        'content-type' => self::MEDIA_TYPE,
    ];

    /**
     * {@inheritDoc}
     */
    public function toArray($request): array
    {
        return [
            'id'            => (string)$this->resource->id,
            'type'          => $this->config('jsonType'),
            'attributes'    => $this->resourceAttributes($request),
            'links'         => [
                'self' => $this->resource->url() ?? new MissingValue(),
            ],
            'relationships' => $this->relationships(),
        ];
    }

    /**
     * @param string $method
     * @return mixed
     */
    protected function config(string $method)
    {
        return call_user_func([get_class($this->resource), $method]);
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function toObject($value)
    {
        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }

        if (is_array($value)) {
            return empty($value) ? new stdClass() : $value;
        }

        return $value;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    protected function resourceAttributes($request): array
    {
        return [];
    }

    /**
     * @param \Carbon\CarbonInterface|null $date
     * @return string|null
     */
    protected function date(?CarbonInterface $date, ?string $format = null): ?string
    {
        if (! $date instanceof CarbonInterface) {
            return null;
        }

        return $format ? $date->format($format) : $date->toJSON();
    }

    /**
     * Retrieve a value based on a given condition.
     *
     * @param string $id
     * @param mixed  $value
     * @param mixed  $default
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function ownerOnly($id, $value, $default = null)
    {
        return $this->when(auth()->id() === $id, $value, $default);
    }

    /**
     * @return array|\Illuminate\Http\Resources\MissingValue
     */
    protected function includes()
    {
        /** @var Enumerable $includes */
        $includes = $this->allowedIncludes()->reduce(
            fn(Collection $includes, string $relation) => $this->included($includes, $relation),
            collect()
        )->filter();

        return $this->when(
            ! $this->isEmpty($includes),
            $includes->unique('id')->values()->toArray()
        );
    }

    /**
     * @param \Illuminate\Support\Enumerable $includes
     * @param string                         $relation
     * @return \Illuminate\Support\Enumerable
     */
    protected function included(Enumerable $includes, string $relation): Enumerable
    {
        $included = $this->whenLoaded($relation, function() use ($relation) {
            $related = $this->resource->{$relation};

            $resource = $this->resourceClass($related);

            return new $resource($related);
        }, []);

        return $includes->push($included);
    }

    /**
     * @return array|\Illuminate\Http\Resources\MissingValue
     */
    protected function relationships()
    {
        $relationships = $this->allowedIncludes()->mapWithKeys(
            fn($relation, $key) => [$relation => $this->relationship($relation)]
        );

        return $this->when(
            ! $this->isEmpty($relationships),
            $relationships->toArray()
        );
    }

    /**
     * @param \Illuminate\Support\Enumerable $collection
     * @return bool
     */
    protected function isEmpty(Enumerable $collection): bool
    {
        return $collection
            ->filter(fn($item) => ! $item instanceof MissingValue)
            ->isEmpty();
    }

    /**
     * @param string $relation
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function route(string $relation)
    {
        $routeName = $this->config('jsonType') . '.relationship.' . $relation;

        return $this->when(Route::has($routeName), fn() => route($routeName, [
            $this->config('routeParam') => $this->resource->id,
        ]));
    }

    /**
     * @param Model $model
     * @return array
     */
    protected function rel(Model $model): array
    {
        return [
            'type' => $model::jsonType(),
            'id'   => (string)$model->getKey(),
        ];
    }

    /**
     * @param string $relation
     * @return array|\Illuminate\Support\Enumerable
     */
    protected function relationshipData(string $relation)
    {
        $relation = $this->resource->{$relation};

        return $relation instanceof Enumerable ?
            $relation->map(fn($model) => $this->rel($model)) :
            $this->rel($relation);
    }

    /**
     * @param string $relation
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function relationship(string $relation)
    {
        return $this->whenLoaded($relation, fn() => [
            'links' => [
                'related' => $this->route($relation),
            ],
            'data'  => $this->relationshipData($relation),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function withResponse($request, $response): void
    {
        $response->withHeaders(self::DEFAULT_HEADERS);
    }

    /**
     * @return \Illuminate\Support\Enumerable
     */
    protected function allowedIncludes(): Enumerable
    {
        return collect($this->config('allowedIncludes'));
    }
}
