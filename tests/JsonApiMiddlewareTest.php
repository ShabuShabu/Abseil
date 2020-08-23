<?php

namespace ShabuShabu\Abseil\Tests;

use Illuminate\Http\Response;
use Orchestra\Testbench\TestCase;
use ShabuShabu\Abseil\Http\Middleware\JsonApiMediaType;
use ShabuShabu\Abseil\Http\Resources\Resource;

class JsonApiMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['router']
            ->middleware(JsonApiMediaType::class)
            ->post('_test/json-api-enabled', static fn() => response('OK'));
    }

    /**
     * @test
     */
    public function ensure_that_an_exception_is_thrown_for_an_invalid_content_type_header(): void
    {
        $response = $this->postJson('_test/json-api-enabled', [
            'test' => 'bla',
        ]);

        $response->assertStatus(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
    }

    /**
     * @test
     */
    public function ensure_that_a_valid_content_type_header_does_nothing(): void
    {
        $response = $this->postJson('_test/json-api-enabled', [
            'test' => 'bla',
        ], [
            'Content-Type' => Resource::MEDIA_TYPE,
        ]);

        $response->assertStatus(Response::HTTP_OK);
    }
}
