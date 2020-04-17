<?php

namespace ShabuShabu\Abseil\Tests\Support\Resources;

use ShabuShabu\Abseil\Http\Resource;

/**
 * @mixin \ShabuShabu\Abseil\Tests\Support\Page
 */
class Page extends Resource
{
    public function resourceAttributes($request): array
    {
        return [
            'title'     => (string)$this->title,
            'content'   => (string)$this->content,
            'createdAt' => $this->date($this->created_at),
            'updatedAt' => $this->date($this->updated_at),
        ];
    }
}