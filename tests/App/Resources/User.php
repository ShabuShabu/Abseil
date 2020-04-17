<?php

namespace ShabuShabu\Abseil\Tests\App\Resources;

use ShabuShabu\Abseil\Http\Resource;

/**
 * @mixin \ShabuShabu\Abseil\Tests\App\User
 */
class User extends Resource
{
    public function resourceAttributes($request): array
    {
        return [
            'name'      => (string)$this->name,
            'email'     => (string)$this->email,
            'createdAt' => $this->date($this->created_at),
            'updatedAt' => $this->date($this->updated_at),
            'deletedAt' => $this->date($this->deleted_at),
        ];
    }
}
