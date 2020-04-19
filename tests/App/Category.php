<?php

namespace ShabuShabu\Abseil\Tests\App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use ShabuShabu\Abseil\Model;

class Category extends Model
{
    public const JSON_TYPE        = 'categories';
    public const ROUTE_PARAM      = 'category';
    public const ALLOWED_INCLUDES = [
        'pages',
    ];

    protected $table = 'categories';

    protected $fillable = [
        'title',
    ];

    public function pages(): HasMany
    {
        return $this->hasMany(Page::class, 'category_id');
    }
}
