<?php

namespace ShabuShabu\Abseil\Tests\App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function syncCategory(Collection $category): bool
    {
        $this->category()->associate($category->get('id'));

        return $this->save();
    }
}
