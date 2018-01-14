<?php

namespace jspaceboots\LaraCRUD;

use App\Console\Commands\MakeModelCommand;
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
        $this->publishes([__DIR__ . '/src/Config/crud.php' => config_path('crud.php'),]);
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
        //
    }
}