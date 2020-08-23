<?php

namespace ShabuShabu\Abseil\Tests\App\Resources;

use ShabuShabu\Abseil\Http\Resources\Resource;

/**
 * @mixin \ShabuShabu\Abseil\Tests\App\Category
 */
class Category extends Resource
{
    public function resourceAttributes($request): array
    {
        return [
            'title'     => (string)$this->title,
            'createdAt' => $this->date($this->created_at),
            'updatedAt' => $this->date($this->updated_at),
        ];
    }
}
