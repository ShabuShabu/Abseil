<?php

namespace ShabuShabu\Abseil\Tests\Support;

use Illuminate\Database\Eloquent\{Relations\HasMany, SoftDeletes};
use Illuminate\Http\Request;
use ShabuShabu\Abseil\Contracts\Trashable;
use ShabuShabu\Abseil\Model;
use Spatie\QueryBuilder\QueryBuilder;

class User extends Model implements Trashable
{
    use SoftDeletes;

    public const JSON_TYPE   = 'users';
    public const ROUTE_PARAM = 'user';

    protected $fillable = [
        'name',
        'email',
    ];

    public static function modifyPagedQuery(QueryBuilder $query, Request $request): QueryBuilder
    {
        return $query;
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class, 'user_id');
    }

    public function trashOrDelete(): bool
    {
        return $this->trashed() ? $this->forceDelete() : $this->delete();
    }
}
