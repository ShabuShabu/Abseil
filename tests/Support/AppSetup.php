<?php

namespace ShabuShabu\Abseil\Tests\Support;


use Illuminate\Routing\Router;
use ShabuShabu\Abseil\AbseilServiceProvider;
use ShabuShabu\Abseil\Tests\App\Controllers\{PageController, UserController};

trait AppSetup
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(dirname(__DIR__, 2) . '/App/migrations');
        $this->withFactories(dirname(__DIR__, 2) . '/App/factories');
        $this->setupRouting($this->app['router']);
    }

    protected function setupRouting(Router $router): void
    {
        $routing = [
            [PageController::class, 'pages', 'page', false],
            [UserController::class, 'users', 'user', true],
        ];

        foreach ($routing as $config) {
            [$controller, $uri, $param, $canRestore] = $config;

            $router->get($uri, [$controller, 'index'])
                   ->name($uri . '.index');

            $router->post($uri, [$controller, 'store'])
                   ->name($uri . '.store');

            $router->get($uri . '/{' . $param . '}', [$controller, 'show'])
                   ->name($uri . '.show');

            $router->put($uri . '/{' . $param . '}', [$controller, 'update'])
                   ->name($uri . '.update');

            $router->patch($uri . '/{' . $param . '}', [$controller, 'update'])
                   ->name($uri . '.patch');

            $router->delete($uri . '/{' . $param . '}', [$controller, 'destroy'])
                   ->name($uri . '.destroy');

            if ($canRestore) {
                $router->put($uri . '/{' . $param . '}/restore', [$controller, 'restore'])
                       ->name($uri . '.restore');
            }
        }
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
}
