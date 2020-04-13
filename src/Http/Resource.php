<?php

namespace ShabuShabu\Abseil\Http;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\{Collection};
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
            'type'          => $this->resource::JSON_TYPE,
            'attributes'    => $this->resourceAttributes($request),
            'links'         => [
                'self' => $this->resource->url(),
            ],
            'relationships' => $this->relationships(),
        ];
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function toObject($value)
    {
        if ($value instanceof Arrayable) {
            return empty($value->toArray()) ? new stdClass() : $value;
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
    protected function date(?CarbonInterface $date): ?string
    {
        return $date instanceof CarbonInterface ? $date->toJSON() : null;
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
        $user = auth()->user();

        return $this->when(
            is_string($id) || ($user->id ?? null) === $id,
            $value,
            $default
        );
    }

    /**
     * @return array|\Illuminate\Http\Resources\MissingValue
     */
    protected function includes()
    {
        /** @var Collection $includes */
        $includes = collect($this->resource::ALLOWED_INCLUDES)
            ->reduce(
                fn (Collection $includes, string $relation) => $this->included($includes, $relation),
                collect()
            )
            ->filter();

        return $this->when(
            ! $this->isEmpty($includes),
            $includes->unique('id')->values()->toArray()
        );
    }

    /**
     * @param \Illuminate\Support\Collection $includes
     * @param string                         $relation
     * @return \Illuminate\Support\Collection
     */
    protected function included(Collection $includes, string $relation): Collection
    {
        $included = $this->whenLoaded($relation, function () use ($relation) {
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
        $relationships = collect($this->resource::ALLOWED_INCLUDES)
            ->mapWithKeys(
                fn ($relation, $key) => [$relation => $this->relationship($relation)]
            );

        return $this->when(
            ! $this->isEmpty($relationships),
            $relationships->toArray()
        );
    }

    /**
     * @param \Illuminate\Support\Collection $collection
     * @return bool
     */
    protected function isEmpty(Collection $collection): bool
    {
        return $collection
            ->filter(fn ($item) => ! $item instanceof MissingValue)
            ->isEmpty();
    }

    /**
     * @param string $relation
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function route(string $relation)
    {
        $routeName = $this->resource::JSON_TYPE . '.relationship.' . $relation;

        return $this->when(Route::has($routeName), route($routeName, [
            $this->resource::ROUTE_PARAM => $this->resource->id,
        ]));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    protected function rel(Model $model): array
    {
        return [
            'type' => (string)$model::JSON_TYPE,
            'id'   => (string)$model->id,
        ];
    }

    /**
     * @param string $relation
     * @return array|\Illuminate\Support\Collection
     */
    protected function relationshipData(string $relation)
    {
        $relation = $this->resource->{$relation};

        return $relation instanceof Collection ?
            $relation->map(fn ($model) => $this->rel($model)) :
            $this->rel($relation);
    }

    /**
     * @param string $relation
     * @return \Illuminate\Http\Resources\MissingValue|mixed
     */
    protected function relationship(string $relation)
    {
        return $this->whenLoaded($relation, fn () => [
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
}
