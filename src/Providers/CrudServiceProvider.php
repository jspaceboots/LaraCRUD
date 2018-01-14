<?php

namespace jspaceboots\LaraCRUD\Providers;

use jspaceboots\LaraCRUD\Interfaces\CrudServiceInterface;
use jspaceboots\LaraCRUD\Services\CrudService;
use Illuminate\Support\ServiceProvider;

class CrudServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CrudServiceInterface::class, function($app) {
            return new CrudService();
        });
    }
}
