<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Clockwork\Support\Laravel\ClockworkServiceProvider;
use Fitztrev\QueryTracer\Providers\QueryTracerServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->provides() as $provider) {
            $this->app->register($provider);
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function provides()
    {
        $providers = [];

        if ($this->app->config['app.debug'] === true) {
            $providers[] = ClockworkServiceProvider::class;
            $providers[] = QueryTracerServiceProvider::class;
        }

        return $providers;
    }
}
