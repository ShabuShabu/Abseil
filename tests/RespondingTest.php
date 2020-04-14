<?php

namespace ShabuShabu\Abseil\Tests;

use Illuminate\Http\Response;
use Orchestra\Testbench\TestCase;
use ShabuShabu\Abseil\Http\Responding;

class RespondingTest extends TestCase
{
    /**
     * @return array
     */
    public function responseProvider(): array
    {
        return [
            'not-modified' => ['notModified', 'Not Modified', Response::HTTP_NOT_MODIFIED],
            'bad-gateway'  => ['badGateway', 'Bad Gateway', Response::HTTP_BAD_GATEWAY],
            'no-content'   => ['noContent', 'No Content', Response::HTTP_NO_CONTENT],
            'created'      => ['created', 'Created', Response::HTTP_CREATED],
        ];
    }

    /**
     * @test
     * @dataProvider responseProvider
     * @param string $method
     * @param string $message
     * @param int    $status
     */
    public function ensure_that_the_correct_response_are_returned(string $method, string $message, int $status): void
    {
        $responder = new class() {
            use Responding;

            public function call($method, $message = null)
            {
                return $message ? $this->{$method}($message) : $this->{$method}();
            }
        };

        /** @var Response $response */
        $response = $responder->call($method);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame($message, $response->getContent());
        $this->assertSame($status, $response->getStatusCode());

        /** @var Response $response */
        $response = $responder->call($method, 'Other Message');

        $this->assertSame('Other Message', $response->getContent());
    }
}
