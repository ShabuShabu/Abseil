<?php

namespace ShabuShabu\Abseil\Tests;

use Orchestra\Testbench\TestCase;
use ShabuShabu\Abseil\AbseilServiceProvider;

class HttpTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/Support/migrations');
        $this->withFactories(__DIR__ . '/Support/factories');
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'abseil');
        $app['config']->set('database.connections.abseil', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function getPackageProviders($app): array
    {
        return [AbseilServiceProvider::class];
    }

    /**
     * @test
     * @group fail
     */
    public function ensure_true_is_true(): void
    {
        // register some dummy routes
        // test the responses
    }
}
