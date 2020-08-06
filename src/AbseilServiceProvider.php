<?php

namespace ShabuShabu\Abseil;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\{Enumerable, ServiceProvider};
use ShabuShabu\Abseil\Http\Middleware\JsonApiMediaType;

class AbseilServiceProvider extends ServiceProvider
{
    protected static string $uuidPattern = '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}';

    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/abseil.php' => config_path('abseil.php'),
            ], 'config');
        }

        $this->app['router']->aliasMiddleware('media.type', JsonApiMediaType::class);

        $this->guessPolicies();
        $this->mapRoutePatterns();
        $this->mapRouteParameters();
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/abseil.php', 'abseil');
    }

    /**
     * @return Enumerable
     */
    protected function uuidParams(): Enumerable
    {
        return $this->boundResources()->filter(
            fn($model, $key) => property_exists($model, 'useUuidPattern') && $model::$useUuidPattern === true
        )->keys();
    }

    /**
     * @return Enumerable
     */
    protected function boundResources(): Enumerable
    {
        return morph_map()->filter(
            fn($model, $key) => method_exists($model, 'routeParam') && $model::routeParam() === $key
        );
    }

    /**
     * Map any route patterns
     */
    protected function mapRoutePatterns(): void
    {
        $this->uuidParams()->each(
            fn(string $param) => $this->app['router']->pattern($param, self::$uuidPattern)
        );
    }

    /**
     * Add any route parameters
     */
    protected function mapRouteParameters(): void
    {
        foreach ($this->boundResources() as $param => $model) {
            $this->app['router']->bind(
                $param,
                fn($uuid) => ModelQuery::make($model, request())->find($uuid)
            );
        }
    }

    /**
     * Guess the policies
     */
    protected function guessPolicies(): void
    {
        $config = config('abseil.policies');

        if ($config['disable'] === true) {
            return;
        }

        $this->app[Gate::class]->guessPolicyNamesUsing(
            static fn(string $className) => get_first_resource($config['namespace'], $className, $config['suffix'])
        );
    }
}
