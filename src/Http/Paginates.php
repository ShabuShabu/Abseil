<?php

namespace ShabuShabu\Abseil\Http;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use ShabuShabu\Abseil\Contracts\Queryable;
use Spatie\QueryBuilder\QueryBuilder;

trait Paginates
{
    /**
     * Helper to paginate things
     *
     * @param mixed                    $query
     * @param \Illuminate\Http\Request $request
     * @param bool                     $queryBuilder
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function paginate($query, Request $request, bool $queryBuilder = true): LengthAwarePaginator
    {
        ['number' => $page, 'size' => $perPage] = $request->input('page');

        if ($queryBuilder) {
            $query = QueryBuilder::for($query, $request);
            $model = $query->getModel();

            if ($model instanceof Queryable) {
                $query = $model::modifyPagedQuery($query, $request);
            }
        }

        return $query->paginate($perPage, ['*'], 'page[number]', $page)
                     ->appends(['page[size]' => $perPage]);
    }
}
