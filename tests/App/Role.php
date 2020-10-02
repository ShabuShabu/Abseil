<?php

namespace ShabuShabu\Abseil\Tests\App;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use ShabuShabu\Abseil\Model;

class Role extends Model
{
    public const JSON_TYPE        = 'roles';
    public const ROUTE_PARAM      = 'role';
    public const ALLOWED_INCLUDES = [
        'user',
    ];

    protected $table = 'roles';

    protected $fillable = [
        'id',
        'name',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
}
