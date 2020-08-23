<?php

namespace ShabuShabu\Abseil\Tests\App\Requests;

use function ShabuShabu\Harness\r;
use ShabuShabu\Harness\Request;

class CategoryRequest extends Request
{
    public function ruleset(): array
    {
        return [
            'attributes' => [
                'title' => r()->required()->string(),
            ],
        ];
    }

    public function feedback(): array
    {
        return [
            'attributes' => [
                'title' => [
                    'required' => 'The title is required',
                    'string'   => 'The title must be a string',
                ],
            ],
        ];
    }
}
