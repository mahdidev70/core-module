<?php

namespace TechStudio\Core\app\Providers;

use Illuminate\Support\ServiceProvider;
use TechStudio\Core\app\Repositories\FollowRepository;
use TechStudio\Core\app\Repositories\Interfaces\FollowRepositoryInterface;

class CoreRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(FollowRepositoryInterface::class, FollowRepository::class);

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
