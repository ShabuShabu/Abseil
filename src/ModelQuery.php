<?php

namespace ShabuShabu\Abseil;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\{AllowedFilter, QueryBuilder};

class ModelQuery
{
    protected QueryBuilder $query;

    protected Request $request;

    /**
     * @param Builder                  $query
     * @param \Illuminate\Http\Request $request
     */
    protected function __construct(Builder $query, Request $request)
    {
        $this->query   = QueryBuilder::for($query, $request);
        $this->request = $request;
    }

    /**
     * @param Builder                  $query
     * @param \Illuminate\Http\Request $request
     * @return static
     */
    public static function make(Builder $query, Request $request): self
    {
        return new self($query, $request);
    }

    /**
     * @param string $uuid
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function find(string $uuid)
    {
        return $this->query
            ->allowedFilters($this->allowedFilters())
            ->findOrFail($uuid);
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
