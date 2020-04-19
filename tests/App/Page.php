<?php

namespace ShabuShabu\Abseil\Tests\App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use ShabuShabu\Abseil\Model;

class Page extends Model
{
    public const JSON_TYPE        = 'pages';
    public const ROUTE_PARAM      = 'page';
    public const ALLOWED_INCLUDES = [
        'user',
        'category',
    ];

    protected $table = 'pages';

    protected $fillable = [
        'title',
        'content',
    ];

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
