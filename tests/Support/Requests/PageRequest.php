<?php

namespace ShabuShabu\Abseil\Tests\Support\Requests;

use ShabuShabu\Harness\Request;
use function ShabuShabu\Harness\r;

class PageRequest extends Request
{
    public function ruleset(): array
    {
        return [
            'attributes' => [
                'title'   => r()->required()->string(),
                'content' => r()->required()->string(),
            ],
        ];
    }

    public function feedback(): array
    {
        return [
            'attributes' => [
                'title'   => [
                    'required' => 'The title is required',
                    'string'   => 'The title must be a string',
                ],
                'content' => [
                    'required' => 'The content field is required',
                    'string'   => 'The content field must be a string',
                ],
            ],
        ];
    }
}
