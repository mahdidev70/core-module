<?php

namespace TechStudio\Core\app\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('{locale?}/api')
                ->group(
                    __DIR__.'/../../routes/api.php'
                );

            // Route::middleware('web')
            //     ->prefix('{locale?}')
            //     ->group(base_path('routes/web.php'));
        });
        // $this->app->make('router')->aliasMiddleware(
        //     'login_required',
        //     \TechStudio\Core\app\Http\Middleware\LoginRequired::class
        // );

        // $this->app->make('router')->aliasMiddleware(
        //     'login_optional',
        //     \TechStudio\Core\app\Http\Middleware\LoginOptional::class
        // );

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/flags.php',
            'flags'
        );

        // $path = $this->publishes([
        //     $configPathTerminy => config_path('flags.php'),
        // ], 'config');
        // $router = $this->app['router'];
        // $router->pushMiddlewareToGroup('web', MyPackage\Middleware\WebOne::class);

        // $this->registerConfig();
        // app('router')->aliasMiddleware(
        //     'login_required',
        //     \TechStudio\Core\app\Http\Middleware\LoginRequired::class
        // );
    }

    public function register()
    {
        // $this->app->register(AuthServiceProvider::class);
    }
}
