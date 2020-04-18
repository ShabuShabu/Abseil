<?php

namespace ShabuShabu\Abseil\Tests\App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\{Relations\HasMany, SoftDeletes};
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use ShabuShabu\Abseil\Contracts\Trashable;
use ShabuShabu\Abseil\Model;
use Spatie\QueryBuilder\QueryBuilder;

class User extends Model implements Trashable, AuthorizableContract, AuthenticatableContract, CanResetPasswordContract
{
    use Authenticatable,
        Authorizable,
        CanResetPassword,
        Notifiable,
        SoftDeletes;

    public const JSON_TYPE   = 'users';
    public const ROUTE_PARAM = 'user';

    protected $fillable = [
        'name',
        'password',
        'email',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
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
