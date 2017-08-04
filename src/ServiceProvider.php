<?php

namespace Cbwar\Laravel\PageSemaphore;

use Cbwar\Laravel\PageSemaphore\Commands\WebsocketServer;
use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{

    public function register()
    {
        // Commands
        $this->commands([WebsocketServer::class]);

        // Config
        $this->mergeConfigFrom(__DIR__ . '/config/pagesemaphore.php', 'pagesemaphore');
    }

    public function boot()
    {

        // Migrations
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        // Translations
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'pagesemaphore');

        // Views
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'pagesemaphore');

        // Config
        $this->publishes([__DIR__ . '/config/pagesemaphore.php' => config_path('pagesemaphore.php')], 'config');

    }

}