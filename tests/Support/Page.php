<?php

namespace ShabuShabu\Abseil\Tests\Support;

use Illuminate\Http\Request;
use ShabuShabu\Abseil\Model;
use Spatie\QueryBuilder\QueryBuilder;

class Page extends Model
{
    public const JSON_TYPE   = 'pages';
    public const ROUTE_PARAM = 'page';

    protected $table = 'pages';

    protected $fillable = [
        'title',
        'content',
    ];

    public static function modifyPagedQuery(QueryBuilder $query, Request $request): QueryBuilder
    {
        return $query;
    }
}
