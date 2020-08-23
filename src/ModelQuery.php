<?php

namespace ShabuShabu\Abseil;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\{AllowedFilter, QueryBuilder};

class ModelQuery
{
    protected QueryBuilder $query;

    protected Request $request;

    protected string $model;

    /**
     * @param string                   $model
     * @param \Illuminate\Http\Request $request
     */
    protected function __construct(string $model, Request $request)
    {
        $this->query   = QueryBuilder::for($model::query(), $request);
        $this->request = $request;
        $this->model   = $model;
    }

    /**
     * @param string                   $model
     * @param \Illuminate\Http\Request $request
     * @return static
     */
    public static function make(string $model, Request $request): self
    {
        return new self($model, $request);
    }

    /**
     * @param string|int $uuid
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function find($uuid)
    {
        $query = $this->query->allowedFilters($this->allowedFilters());

        if (! method_exists($this->model, 'methodForModelQuery')) {
            return $query->findOrFail($uuid);
        }

        $method = $this->model::methodForModelQuery($uuid);

        return $query->$method($uuid)->firstOrFail();
    }

    /**
     * @return array
     */
    protected function allowedFilters(): array
    {
        if (! is_authenticated_request()) {
            return [];
        }

        return [AllowedFilter::trashed()];
    }
}
