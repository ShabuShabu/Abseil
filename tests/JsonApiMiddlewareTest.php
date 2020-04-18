<?php

namespace ShabuShabu\Abseil\Tests;

use Orchestra\Testbench\TestCase;
use ShabuShabu\Abseil\AbseilServiceProvider;
use ShabuShabu\Abseil\Http\Resources\Resource;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class JsonApiMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['router']
            ->middleware('json.api')
            ->post('_test/json-api-enabled', static fn() => response('OK'));
    }

    protected function getPackageProviders($app): array
    {
        return [AbseilServiceProvider::class];
    }

    /**
     * @test
     */
    public function ensure_that_an_exception_is_thrown_for_an_invalid_content_type_header(): void
    {
        $this->expectException(UnsupportedMediaTypeHttpException::class);

        $this->postJson('_test/json-api-enabled', [
            'test' => 'bla',
        ]);
    }

    /**
     * @test
     */
    public function ensure_that_a_valid_content_type_header_does_nothing(): void
    {
        try {
            $this->postJson('_test/json-api-enabled', [
                'test' => 'bla',
            ], [
                'Content-Type' => Resource::MEDIA_TYPE,
            ]);
        } catch (UnsupportedMediaTypeHttpException $e) {
            $this->assertTrue(false);
            return;
        }

        $this->assertTrue(true);
    }
}
