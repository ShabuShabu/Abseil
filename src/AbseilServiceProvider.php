<?php

namespace ShabuShabu\Abseil;

use Illuminate\Support\{Collection, Facades\Route, ServiceProvider};

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
     * @return Collection
     */
    protected function uuidParams(): Collection
    {
        return $this->boundResources()->keys();
    }

    /**
     * @return Collection
     */
    protected function boundResources(): Collection
    {
        return morph_map()->filter(
            fn ($model, $key) => $model::ROUTE_PARAM === $key
        );
    }

    /**
     * Map any route patterns
     */
    protected function mapRoutePatterns(): void
    {
        if (! $this->app['config']->get('abseil.use_uuids')) {
            return;
        }

        foreach ($this->uuidParams() as $param) {
            Route::pattern($param, self::$uuidPattern);
        }
    }

    /**
     * Add any route parameters
     */
    protected function mapRouteParameters(): void
    {
        foreach ($this->boundResources() as $param => $class) {
            Route::bind(
                $param,
                fn (string $uuid) => ModelQuery::make($class::query(), request())->find($uuid)
            );
        }
    }
}
