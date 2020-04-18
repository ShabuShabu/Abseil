<?php

namespace ShabuShabu\Abseil\Tests\App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use ShabuShabu\Abseil\Model;
use Spatie\QueryBuilder\QueryBuilder;

class Category extends Model
{
    public const JSON_TYPE   = 'categories';
    public const ROUTE_PARAM = 'category';

    protected $table = 'categories';

    protected $fillable = [
        'title',
    ];

    public static function modifyPagedQuery(QueryBuilder $query, Request $request): QueryBuilder
    {
        return $query;
    }

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class, 'category_id');
    }
}
