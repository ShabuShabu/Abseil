<?php

namespace ShabuShabu\Abseil\Tests\App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use ShabuShabu\Abseil\Tests\App\{Page, User};

class AppServiceProvider extends ServiceProvider
{
    public const MORPH_MAP = [
        'page' => Page::class,
        'user' => User::class,
    ];

    public function boot(): void
    {
        Relation::morphMap(self::MORPH_MAP);
    }
}
