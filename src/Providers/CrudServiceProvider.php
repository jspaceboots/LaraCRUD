<?php

namespace jspaceboots\laracrud\Providers;

use jspaceboots\laracrud\Interfaces\CrudServiceInterface;
use jspaceboots\laracrud\Services\CrudService;
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
