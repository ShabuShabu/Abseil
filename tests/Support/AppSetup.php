<?php

namespace ShabuShabu\Abseil\Tests\Support;


use Illuminate\Routing\Router;
use ShabuShabu\Abseil\AbseilServiceProvider;
use ShabuShabu\Abseil\Tests\App\Controllers\{CategoryController, PageController, UserController};
use ShabuShabu\Abseil\Tests\App\Providers\AppServiceProvider;
use ShabuShabu\Abseil\Tests\App\User;

trait AppSetup
{
    protected ?User $authenticatedUser = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(dirname(__DIR__) . '/App/migrations');
        $this->withFactories(dirname(__DIR__) . '/App/factories');

        $this->authenticatedUser = factory(User::class)->create();
    }

    protected function getEnvironmentSetUp($app): void
    {
        $this->setupRouting($app['router']);

        $app['config']->set('abseil.resource_namespace', 'ShabuShabu\\Abseil\\Tests\\App\\Resources\\');
        $app['config']->set('abseil.morph_map_location', AppServiceProvider::class);

        $app['config']->set('database.default', 'abseil');
        $app['config']->set('database.connections.abseil', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function setupRouting(Router $router): void
    {
        $routing = [
            [PageController::class, 'pages', 'page', false],
            [CategoryController::class, 'categories', 'category', false],
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

    protected function getPackageProviders($app): array
    {
        return [
            AppServiceProvider::class,
            AbseilServiceProvider::class,
        ];
    }
}
