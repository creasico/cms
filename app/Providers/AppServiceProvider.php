<?php

namespace App\Providers;

use Carbon\Carbon;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;
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
    private $providers = [
        SentryLaravelServiceProvider::class,
        HttpClientServicePrvider::class,
    ];

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Set app locale to Faker?
        $this->app->singleton(FakerGenerator::class, function () {
            return FakerFactory::create($this->app->config['app.locale']);
        });

        // Set app locale to Carbon?
        Carbon::setLocale($this->app->config['app.locale']);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (!$this->app->environment('production')) {
            $this->providers[] = ClockworkServiceProvider::class;
            $this->providers[] = QueryTracerServiceProvider::class;
        }

        foreach ($this->providers as $provider) {
            $this->app->register($provider);
        }
    }
}
