<?php

namespace App\Providers;

use App\Repositories\SpamRepository;
use Illuminate\Support\Facades\Vite;
use App\Repositories\FolderRepository;
use Illuminate\Support\ServiceProvider;
use App\Interfaces\SpamRepositoryInterface;
use App\Interfaces\FolderRepositoryInterface;
use App\Repositories\TemplateCategoryRepository;
use App\Interfaces\TemplateCategoryRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(TemplateCategoryRepositoryInterface::class, TemplateCategoryRepository::class);
        $this->app->bind(FolderRepositoryInterface::class, FolderRepository::class);
        $this->app->bind(SpamRepositoryInterface::class, SpamRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
