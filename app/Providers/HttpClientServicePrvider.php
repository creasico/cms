<?php

namespace App\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Middleware;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\MessageFormatter;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class HttpClientServicePrvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind([ClientInterface::class, 'guzzle'], function ($app) {
            return new Client([
                'handler' => $app->make(HandlerStack::class)
            ]);
        });

        $this->app->alias(ClientInterface::class, Client::class);

        $this->app->bindIf(HandlerStack::class, function () {
            return HandlerStack::start();
        });

        $this->app->resolving(HandlerStack::class, function (HandlerStack $stack) {
            $stack->unshift(Middleware::log(
                $this->app->make(LoggerInterface::class),
                new MessageFormatter
            ));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [ClientInterface::class, Client::class, 'guzzle'];
    }
}
