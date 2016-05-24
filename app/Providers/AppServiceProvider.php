<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Sentry\SentryLaravel\SentryLaravelServiceProvider;
use Clockwork\Support\Laravel\ClockworkServiceProvider;
use Fitztrev\QueryTracer\Providers\QueryTracerServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register vendor providers that required by the application.
     *
     * @var array
     */
    private $providers = [];

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
        if ($this->app->config['app.debug'] === true) {
            $this->providers[] = ClockworkServiceProvider::class;
            $this->providers[] = QueryTracerServiceProvider::class;
            $this->providers[] = SentryLaravelServiceProvider::class;
        }

        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }
}
