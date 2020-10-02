<?php

namespace ShabuShabu\Abseil\Tests\App\Resources;

use ShabuShabu\Abseil\Http\Resources\Resource;

/**
 * @mixin \ShabuShabu\Abseil\Tests\App\Role
 */
class Role extends Resource
{
    public function resourceAttributes($request): array
    {
        return [
            'name' => (string)$this->name,
        ];
    }
}
