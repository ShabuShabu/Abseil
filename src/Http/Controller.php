<?php

namespace ShabuShabu\Abseil\Http;

use Closure;
use Illuminate\Database\Eloquent\{Builder, Relations\Relation};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\{Request as HttpRequest, Response};
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\{Arr, Enumerable, Str};
use LogicException;
use ShabuShabu\Abseil\Contracts\{HeaderValues, Trashable};
use ShabuShabu\Abseil\Events\{ResourceCreated,
    ResourceDeleted,
    ResourceRelationshipSaved,
    ResourceRestored,
    ResourceUpdated
};
use ShabuShabu\Abseil\Http\Resources\Collection;
use ShabuShabu\Abseil\Model;
use ShabuShabu\Harness\Request;
use Spatie\QueryBuilder\QueryBuilderRequest;
use Throwable;
use function ShabuShabu\Abseil\{inflate, morph_map, resource_guard, resource_namespace};
use function ShabuShabu\Harness\to_snake_case;

class Controller extends BaseController
{
    use AuthorizesRequests, Paginates, Responding;

    protected static array $createHeaders = [
        'Location',
        'X-Request-ID',
    ];

    /**
     * Display a listing of the resource.
     *
     * @param mixed                    $query
     * @param \Illuminate\Http\Request $request
     * @return Collection
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function resourceCollection($query, HttpRequest $request): Collection
    {
        $class = $query instanceof Builder || $query instanceof Relation ?
            get_class($query->getModel()) :
            $query;

        if ($this->shouldAuthorize('overview')) {
            $this->authorize('overview', $class);
        }

        $resource = ($namespace = resource_namespace()) . class_basename($class);

        if (! class_exists($collection = $resource . 'Collection')) {
            $collection = $namespace . 'Collection';
        }

        if (! class_exists($collection)) {
            $collection = Collection::class;
        }

        resource_guard($collection);

        return new $collection(
            $this->paginate($query, $request),
            $resource
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request        $request
     * @param string|Closure $model
     * @param array|null     $headersOnly
     * @return Response
     * @throws \Throwable
     */
    protected function createResource(Request $request, $model, array $headersOnly = null): Response
    {
        $response = $model instanceof Closure ?
            $model($request) :
            call_user_func([$model, 'create'], $this->modelFieldsFrom($request));

        if (! $response) {
            return $this->badGateway();
        }

        $this->saveRelationships($request, $response);

        ResourceCreated::dispatch($response);

        return $this->resourceCreated($response, $headersOnly);
    }

    /**
     * @param HeaderValues $resource
     * @param array|null   $headersOnly
     * @return \Illuminate\Http\Response
     */
    protected function resourceCreated(HeaderValues $resource, array $headersOnly = null): Response
    {
        $headers = collect([
            'Location'     => $resource->url(),
            'X-Request-ID' => $resource->getKey(),
        ])->only(
            $headersOnly ?? self::$createHeaders
        );

        return $this->created()->withHeaders(
            $headers->toArray()
        );
    }

    /**
     * Show the specified resource
     *
     * @param \Illuminate\Http\Request $request
     * @param Model                    $model
     * @param string|null              $resource
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function showResource(HttpRequest $request, Model $model, string $resource = null)
    {
        if ($this->shouldAuthorize('view')) {
            $this->authorize('view', $model);
        }

        resource_guard(
            $resource = $resource ?: resource_namespace() . class_basename($model)
        );

        $includes = QueryBuilderRequest::fromRequest($request)
                                       ->includes()
                                       ->intersect($model::allowedIncludes())
                                       ->toArray();

        return new $resource(
            $model->load($includes)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request       $request
     * @param Model         $model
     * @param \Closure|null $callback
     * @return Response
     * @throws \Throwable
     */
    protected function updateResource(Request $request, Model $model, Closure $callback = null): Response
    {
        $response = $callback ?
            $callback($request, $model) :
            $model->fill($this->modelFieldsFrom($request))->save();

        if (! $response) {
            return $this->badGateway();
        }

        $this->saveRelationships($request, $model);

        ResourceUpdated::dispatch($model);

        return $this->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Model $model
     * @return Response
     * @throws \Exception
     */
    protected function deleteResource(Model $model): Response
    {
        if ($this->shouldAuthorize('delete')) {
            $this->authorize('delete', $model);
        }

        if (! ($model instanceof Trashable ? $model->trashOrDelete() : $model->delete())) {
            return $this->badGateway();
        }

        ResourceDeleted::dispatch($model);

        return $this->noContent('Deleted');
    }

    /**
     * Restore the specified resource.
     *
     * @param Trashable $model
     * @return Response
     * @throws \Exception
     */
    protected function restoreResource(Trashable $model): Response
    {
        if ($this->shouldAuthorize('delete')) {
            $this->authorize('delete', $model);
        }

        if (! $model->trashed()) {
            return $this->notModified();
        }

        if (! $model->restore()) {
            return $this->badGateway();
        }

        ResourceRestored::dispatch($model);

        return $this->noContent('Restored');
    }

    /**
     * Create or update the relationships
     *
     * @param \ShabuShabu\Harness\Request $request
     * @param Model                       $model
     * @throws \Throwable
     */
    protected function saveRelationships(Request $request, Model $model): void
    {
        $relationships = collect($request->validated()['data'])->get('relationships', []);

        foreach ($relationships as $type => $relationship) {
            $this->handleRelationship($model, $type, $relationship);
        }
    }

    /**
     * @param \ShabuShabu\Abseil\Model $model
     * @param string                   $type
     * @param array                    $relationship
     * @throws \Throwable
     */
    protected function handleRelationship(Model $model, string $type, array $relationship): void
    {
        $method = 'sync' . Str::studly($type);

        if (! method_exists($model, $method)) {
            throw new LogicException(
                sprintf('Method [%s] does not exist for model [%s]', $method, get_class($model))
            );
        }

        try {
            $model->{$method}(
                $relationship = static::hydrate($relationship['data'])
            );

            ResourceRelationshipSaved::dispatch($model, $type, $relationship);
        } catch (Throwable $e) {
            // fail silently...
        }
    }

    /**
     * @param array $data
     * @return mixed
     */
    public static function hydrate(array $data)
    {
        $type = $data['type'] ?? collect($data)->pluck('type')->first();

        $model = morph_map()->first(
            fn($value, $key) => $key === Str::singular($type)
        );

        if (isset($data['id'])) {
            return $model::query()->findOrFail($data['id']);
        }

        return $model::query()->whereIn('id', collect($data)->pluck('id'))->get();
    }

    /**
     * Flatten the attributes and the id to an array for insertion
     *
     * @param \ShabuShabu\Harness\Request $request
     * @return array
     */
    protected function modelFieldsFrom(Request $request): array
    {
        return collect(Arr::dot(
            Arr::only($request->validated()['data'], ['id', 'attributes'])
        ))->mapWithKeys(
            fn($value, $key) => [str_replace('attributes.', '', $key) => $value]
        )->filter(
            fn($value, $key) => ! Str::endsWith($key, '_confirmation')
        )->pipe(
            fn(Enumerable $collection) => to_snake_case(inflate($collection))
        );
    }

    /**
     * @param string $ability
     * @return bool
     */
    protected function shouldAuthorize(string $ability): bool
    {
        if (! property_exists($this, 'authorizeAbility')) {
            return true;
        }

        return isset($this->authorizeAbility[$ability]) && $this->authorizeAbility[$ability] === true;
    }
}
