<?php

namespace jspaceboots\LaraCRUD;

use jspaceboots\LaraCRUD\Commands\MakeModelCommand;
use Illuminate\Support\ServiceProvider;

class LaraCRUDServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/Config/crud.php' => config_path('crud.php')], 'config');
        $this->publishes([__DIR__ . '/public' => public_path('vendor/LaraCRUD')], 'public');
        $this->loadRoutesFrom(__DIR__ . '/Http/routes.php');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'LaraCRUD');

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeModelCommand::class
            ]);
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\jspaceboots\LaraCRUD\Providers\CrudServiceProvider::class);
    }
}