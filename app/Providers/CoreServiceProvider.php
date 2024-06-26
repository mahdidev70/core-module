<?php

namespace TechStudio\Core\app\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;


class CoreServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/api.php');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

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

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/sms.php',
            'sms'
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

        $this->morphMap();

    }

    public function morphMap(): void
    {
        Relation::morphMap([
            'course' => 'TechStudio/Lms/app/Models/Course',
        ]);
    }


    public function register()
    {
        // $this->app->register(AuthServiceProvider::class);
    }
}
