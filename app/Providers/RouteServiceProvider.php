<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @param  \Illuminate\Routing\Router  $route
     * @return void
     */
    public function boot(Router $route)
    {
        //

        parent::boot($route);
    }

    /**
     * Define the routes for the application.
     *
     * @param  \Illuminate\Routing\Router  $route
     * @return void
     */
    public function map(Router $route)
    {
        // Define the "web" routes for this project.
        $route->group([
            'namespace' => $this->namespace,
            'middleware' => 'web',
        ], function ($route) {
            require app_path('Http/routes/web.php');

            // Authentication Routes...
            $route->get('login', 'Auth\AuthController@showLoginForm');
            $route->post('login', 'Auth\AuthController@login');
            $route->get('logout', 'Auth\AuthController@logout');

            // Registration Routes...
            $route->get('register', 'Auth\AuthController@showRegistrationForm');
            $route->post('register', 'Auth\AuthController@register');

            // Password Reset Routes...
            $route->get('password/reset/{token?}', 'Auth\PasswordController@showResetForm');
            $route->post('password/email', 'Auth\PasswordController@sendResetLinkEmail');
            $route->post('password/reset', 'Auth\PasswordController@reset');
        });

        // Define the "admin" routes for this project.
        $route->group([
            'namespace' => $this->namespace . '\Admin',
            'middleware' => 'auth',
        ], function ($route) {
            require app_path('Http/routes/admin.php');
        });
    }
}
