<?php

namespace ShabuShabu\Abseil\Http;

use Closure;
use Illuminate\Database\Eloquent\{Model, Relations\Relation};
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\{Request as HttpRequest, Response};
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\{Arr, Collection as BaseCollection, Str};
use LogicException;
use ShabuShabu\Abseil\Contracts\{HeaderValues, Trashable};
use ShabuShabu\Abseil\Events\{ResourceCreated, ResourceDeleted, ResourceRelationshipSaved};
use ShabuShabu\Harness\Request;
use Spatie\QueryBuilder\QueryBuilderRequest;
use function ShabuShabu\Abseil\{inflate, model_name, resource_guard};

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
        if ($query instanceof Relation) {
            $query = $query->getQuery();
        }

        $this->authorize('overview', $class = model_name($query));

        $resourceNamespace = config('abseil.resource_namespace');

        $resource = $resourceNamespace . class_basename($class);

        if (!class_exists($collection = $resource . 'Collection')) {
            $collection = $resourceNamespace . 'Collection';
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

        if (!$response) {
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
        $this->authorize('view', $model);

        resource_guard(
            $resource = $resource ?: config('abseil.resource_namespace') . class_basename($model)
        );

        $includes = QueryBuilderRequest::fromRequest($request)
                                       ->includes()
                                       ->intersect($model::ALLOWED_INCLUDES)
                                       ->toArray();

        return new $resource(
            $model->load($includes)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request  $request
     * @param Model    $model
     * @param \Closure $callback
     * @return Response
     * @throws \Throwable
     */
    protected function updateResource(Request $request, Model $model, Closure $callback = null): Response
    {
        $response = $callback ?
            $callback($request, $model) :
            $model->fill($this->modelFieldsFrom($request))->save();

        if (!$response) {
            return $this->badGateway();
        }

        $this->saveRelationships($request, $model);

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
        $this->authorize('delete', $model);

        if (!($model instanceof Trashable ? $model->trashOrDelete() : $model->delete())) {
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
        $this->authorize('delete', $model);

        if (!$model->trashed()) {
            return $this->notModified();
        }

        if (!$model->restore()) {
            return $this->badGateway();
        }

        return $this->noContent('Restored');
    }

    /**
     * Create or update the relationships
     *
     * @param \ShabuShabu\Harness\Request         $request
     * @param \Illuminate\Database\Eloquent\Model $model
     * @throws \Throwable
     */
    protected function saveRelationships(Request $request, Model $model): void
    {
        $relationships = collect($request->validated())->get('relationships', []);

        foreach ($relationships as $name => $relationship) {
            $method = 'sync' . Str::kebab($name);

            throw_unless(
                method_exists($model, $method),
                LogicException::class,
                sprintf('Method [%s] does not exist for model [%s]', $method, get_class($model))
            );

            $model->{$method}(collect($relationship));

            ResourceRelationshipSaved::dispatch($model, $relationship);
        }
    }

    /**
     * Flatten the attributes and the id to an array for insertion
     *
     * @param \ShabuShabu\Harness\Request $request
     * @param bool                        $asArray
     * @return array|\Illuminate\Support\Collection
     */
    protected function modelFieldsFrom(Request $request, bool $asArray = true): iterable
    {
        $data = Arr::only(
            $request->validated()['data'],
            ['id', 'attributes', 'relationships']
        );

        return collect(Arr::dot($data))
            ->mapWithKeys(
                fn($value, $key) => [str_replace('attributes.', '', $key) => $value]
            )
            ->pipe(
                fn(BaseCollection $collection) => inflate($collection, $asArray)
            );
    }
}
