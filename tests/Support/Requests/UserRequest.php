<?php

namespace ShabuShabu\Abseil\Tests\Support\Requests;

use ShabuShabu\Harness\Request;
use function ShabuShabu\Harness\r;

class UserRequest extends Request
{
    public function ruleset(): array
    {
        return [
            'attributes' => [
                'name'  => r()->required()->string(),
                'email' => r()->required()->email(),
            ],
        ];
    }

    public function feedback(): array
    {
        return [
            'attributes' => [
                'name'  => [
                    'required' => 'The name is required',
                    'string'   => 'The name must be a string',
                ],
                'email' => [
                    'required' => 'The email address is required',
                    'email'    => 'You must provide a valid email address',
                ],
            ],
        ];
    }
}
