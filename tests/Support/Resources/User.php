<?php

namespace ShabuShabu\Abseil\Tests\Support\Resources;

use ShabuShabu\Abseil\Http\Resource;

/**
 * @mixin \ShabuShabu\Abseil\Tests\Support\User
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
