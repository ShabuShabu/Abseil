<?php

namespace ShabuShabu\Abseil\Contracts;

use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

interface Queryable
{
    /**
     * Modify the query
     *
     * @param QueryBuilder             $query
     * @param \Illuminate\Http\Request $request
     * @return QueryBuilder
     */
    public static function modifyPagedQuery(QueryBuilder $query, Request $request): QueryBuilder;
}
