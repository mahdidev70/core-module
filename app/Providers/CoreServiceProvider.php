<?php

namespace TechStudio\Core\app\Providers;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        
        // $router = $this->app['router'];
        // $router->pushMiddlewareToGroup('web', MyPackage\Middleware\WebOne::class);
        app('router')->aliasMiddleware('login_required', \TechStudio\Core\app\Http\Middleware\LoginRequired::class);
    }
}
