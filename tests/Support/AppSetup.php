<?php

namespace ShabuShabu\Abseil\Tests\Support;


use Illuminate\Routing\Router;
use ShabuShabu\Abseil\AbseilServiceProvider;
use ShabuShabu\Abseil\Tests\App\{Category, Page, User};
use ShabuShabu\Abseil\Tests\App\Controllers\{CategoryController, PageController, UserController};
use ShabuShabu\Abseil\Tests\App\Providers\AppServiceProvider;
use Spatie\QueryBuilder\QueryBuilderServiceProvider;

trait AppSetup
{
    protected ?User $authenticatedUser = null;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(dirname(__DIR__) . '/App/migrations');
        $this->withFactories(dirname(__DIR__) . '/App/factories');

        $this->authenticatedUser = factory(User::class)->create();
    }

    /**
     * {@inheritDoc}
     */
    protected function getEnvironmentSetUp($app): void
    {
        $this->setupRouting($app['router']);

        $app['config']->set('abseil.policies.namespace', 'ShabuShabu\\Abseil\\Tests\\App\\Policies');
        $app['config']->set('abseil.resource_namespace', 'ShabuShabu\\Abseil\\Tests\\App\\Resources');
        $app['config']->set('abseil.morph_map_location', AppServiceProvider::class);
    }

    /**
     * @param \Illuminate\Routing\Router $router
     */
    protected function setupRouting(Router $router): void
    {
        $middleware = [
            'bindings',
            'json.api',
        ];

        $router->middleware($middleware)->group(static function (Router $router) {
            $routing = [
                [PageController::class, Page::JSON_TYPE, Page::ROUTE_PARAM, false],
                [CategoryController::class, Category::JSON_TYPE, Category::ROUTE_PARAM, false],
                [UserController::class, User::JSON_TYPE, User::ROUTE_PARAM, true],
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
        });
    }

    /**
     * {@inheritDoc}
     */
    protected function getPackageProviders($app): array
    {
        return [
            AppServiceProvider::class,
            QueryBuilderServiceProvider::class,
            AbseilServiceProvider::class,
        ];
    }
}