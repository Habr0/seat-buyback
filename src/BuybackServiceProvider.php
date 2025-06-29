<?php
namespace Habr0\Buyback;

use Seat\Services\AbstractSeatPlugin;

class BuybackServiceProvider extends AbstractSeatPlugin
{
    public function boot()
    {
        $this->addRoutes();
        $this->addViews();
        $this->addMigrations();
        // $this->addPublications();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/buyback.config.php', 'seat-buyback.config');
        $this->mergeConfigFrom(__DIR__ . '/Config/buyback.sidebar.php', 'package.sidebar');

        $this->registerPermissions(__DIR__ . '/Config/buyback.permissions.php', 'buyback');
    }

    private function addRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
    }

    private function addViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'buyback');
    }

    private function addMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    private function addPublications(): void
    {
        $this->publishes([
            __DIR__ . '/resources/css' => public_path('web/css'),
            __DIR__ . '/resources/js' => public_path('web/js'),
        ], ['public', 'seat']);
    }

    public function getName(): string
    {
        return 'Seat Buyback (by Major Habro)';
    }

    public function getPackageRepositoryUrl(): string
    {
        return 'https://github.com/habr0/seat-buyback';
    }

    public function getPackagistPackageName(): string
    {
        return 'seat-buyback';
    }

    public function getPackagistVendorName(): string
    {
        return 'habr0';
    }
}
