<?php

namespace ShabuShabu\Abseil\Tests\App\Requests;

use ShabuShabu\Abseil\Tests\App\Category;
use ShabuShabu\Harness\Request;
use function ShabuShabu\Harness\r;

class PageRequest extends Request
{
    public function ruleset(): array
    {
        return [
            'attributes'    => [
                'title'   => r()->required()->string(),
                'content' => r()->required()->string(),
            ],
            'relationships' => [
                'category' => [
                    'data' => [
                        'type' => r()->nullable()->in([Category::jsonType()]),
                        'id'   => r()->nullable()->uuid(),
                    ],
                ],
            ],
        ];
    }

    public function feedback(): array
    {
        return [
            'attributes'    => [
                'title'   => [
                    'required' => 'The title is required',
                    'string'   => 'The title must be a string',
                ],
                'content' => [
                    'required' => 'The content field is required',
                    'string'   => 'The content field must be a string',
                ],
            ],
            'relationships' => [
                'category' => [
                    'data' => [
                        'type' => [
                            'in' => 'The category relationship type must be ' . Category::jsonType(),
                        ],
                        'id'   => [
                            'uuid' => 'The category relationship id is not a valid UUID',
                        ],
                    ],
                ],
            ],
        ];
    }
}
