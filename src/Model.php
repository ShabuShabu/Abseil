<?php

namespace ShabuShabu\Abseil;

use Illuminate\Database\Eloquent\{Builder, Model as Eloquent};
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use ShabuShabu\Abseil\Contracts\{Abseil, HeaderValues, Queryable};
use Spatie\QueryBuilder\QueryBuilder;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

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
 * @method static Builder whereNotIn($column, $values, $boolean = 'and')
 * @method static Builder where($column, $value)
 * @method static Builder withoutGlobalScope($scope)
 * @method static Builder withoutGlobalScopes($scopes)
 * @method static LengthAwarePaginator paginate($perPage, $columns, $pageName, $page)
 */
abstract class Model extends Eloquent implements Abseil, HeaderValues, Queryable
{
    use GenerateUuidOnCreate;

    public const JSON_TYPE        = '';
    public const ROUTE_PARAM      = '';
    public const ALLOWED_INCLUDES = [];

    public static bool $useUuidPattern = true;

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
    public static function allowedIncludes(): array
    {
        return static::ALLOWED_INCLUDES;
    }

    /**
     * {@inheritDoc}
     */
    public static function jsonType(): string
    {
        return static::JSON_TYPE;
    }

    /**
     * {@inheritDoc}
     */
    public static function routeParam(): string
    {
        return static::ROUTE_PARAM;
    }

    /**
     * {@inheritDoc}
     */
    public static function modifyPagedQuery(QueryBuilder $query, Request $request): QueryBuilder
    {
        return $query->allowedIncludes(static::allowedIncludes());
    }

    /**
     * {@inheritDoc}
     */
    public function url(): ?string
    {
        if (! $this->exists) {
            return null;
        }

        try {
            return route(static::jsonType() . '.show', [static::routeParam() => $this->getKey()]);
        } catch (RouteNotFoundException $exception) {
            return null;
        }
    }
}
