<?php

namespace App\Providers;

use App\Repositories\AuthRepository;
use App\Repositories\MailRepository;
use App\Repositories\SpamRepository;
use Illuminate\Support\Facades\Vite;
use App\Repositories\FolderRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\TemplateRepository;
use App\Interfaces\AuthRepositoryInterface;
use App\Interfaces\MailRepositoryInterface;
use App\Interfaces\SpamRepositoryInterface;
use App\Interfaces\FolderRepositoryInterface;
use App\Interfaces\TemplateRepositoryInterface;
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
        $this->app->bind(TemplateRepositoryInterface::class, TemplateRepository::class);
        $this->app->bind(MailRepositoryInterface::class, MailRepository::class);
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
