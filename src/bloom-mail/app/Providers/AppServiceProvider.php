<?php

namespace App\Providers;

use App\Repositories\MailRepository;
use App\Repositories\RoleRepository;
use App\Repositories\ShopRepository;
use App\Repositories\SpamRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Vite;
use App\Repositories\FolderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\API\AuthRepository;
use App\Repositories\TemplateRepository;
use App\Repositories\PermissionRepository;
use App\Interfaces\MailRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\ShopRepositoryInterface;
use App\Interfaces\SpamRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\NotificationRepository;
use App\Interfaces\FolderRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\API\AuthRepositoryInterface;
use App\Interfaces\TemplateRepositoryInterface;
use App\Repositories\API\ShopProductRepository;
use App\Repositories\TemplateCategoryRepository;
use App\Interfaces\PermissionRepositoryInterface;
use App\Interfaces\NotificationRepositoryInterface;
use App\Interfaces\API\ShopProductRepositoryInterface;
use App\Interfaces\TemplateCategoryRepositoryInterface;
use App\Repositories\API\NotificationRepository as ApiNotificationRepository;
use App\Interfaces\API\NotificationRepositoryInterface as ApiNotificationRepositoryInterface;

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
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        //app system
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(NotificationRepositoryInterface::class, NotificationRepository::class);


        //API
        $this->app->bind(AuthRepositoryInterface::class, AuthRepository::class);
        $this->app->bind(ShopProductRepositoryInterface::class, ShopProductRepository::class);
        $this->app->bind(ShopRepositoryInterface::class, ShopRepository::class);
        $this->app->bind(ApiNotificationRepositoryInterface::class, ApiNotificationRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        $url = env('APP_URL');

        $parsedUrl = parse_url($url);

        if (isset($parsedUrl['scheme'])) {
            if ($parsedUrl['scheme'] === 'https') {
                $this->app['request']->server->set('HTTPS', true);
            }
        }
    }
}
