<?php

namespace ShabuShabu\Abseil;

use Illuminate\Database\Eloquent\{Builder, Model as Eloquent};
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use ShabuShabu\Abseil\Contracts\{HeaderValues, Queryable};
use Spatie\QueryBuilder\QueryBuilder;

/**
 * ShabuShabu\Abseil\Model
 *
 * @property string                  $id
 * @property \Carbon\CarbonInterface $created_at
 * @property \Carbon\CarbonInterface $updated_at
 * @method static static find($id)
 * @method static static findOrFail($id)
 * @method static static create($attributes)
 * @method static Builder whereNull($value)
 * @method static Builder where($column, $value)
 * @method static Builder withoutGlobalScope($scope)
 * @method static LengthAwarePaginator paginate($perPage, $columns, $pageName, $page)
 */
abstract class Model extends Eloquent implements HeaderValues, Queryable
{
    use GenerateUuidOnCreate;

    public const JSON_TYPE        = '';
    public const ROUTE_PARAM      = '';
    public const ALLOWED_INCLUDES = [];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 20;

    /**
     * {@inheritDoc}
     */
    public static function modifyPagedQuery(QueryBuilder $query, Request $request): QueryBuilder
    {
        return $query->allowedIncludes(static::ALLOWED_INCLUDES);
    }

    /**
     * {@inheritDoc}
     */
    public function url(): string
    {
        return route(static::JSON_TYPE . '.show', [static::ROUTE_PARAM => $this->getKey()]);
    }
}
