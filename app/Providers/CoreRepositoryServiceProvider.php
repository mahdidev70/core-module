<?php

namespace TechStudio\Core\app\Providers;

use Illuminate\Support\ServiceProvider;
use TechStudio\Core\app\Repositories\BannerRepository;
use TechStudio\Core\app\Repositories\FollowRepository;
use TechStudio\Core\app\Repositories\Interfaces\BannerRepositoryInterface;
use TechStudio\Core\app\Repositories\Interfaces\FollowRepositoryInterface;
use TechStudio\Core\app\Repositories\Interfaces\TroubleshootingReportRepositoryInterface;
use TechStudio\Core\app\Repositories\TroubleshootingReportRepository;

class CoreRepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(FollowRepositoryInterface::class, FollowRepository::class);
        $this->app->bind(BannerRepositoryInterface::class, BannerRepository::class);
        $this->app->bind(TroubleshootingReportRepositoryInterface::class, TroubleshootingReportRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
