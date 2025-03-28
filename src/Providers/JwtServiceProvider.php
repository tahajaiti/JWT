<?php

namespace Kyojin\JWT\Providers;

use Kyojin\JWT\Services\JwtService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class JwtServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Publishing the config
        $this->publishes([
            __DIR__ . '/../../config/jwt.php' => $this->app->configPath('jwt.php'),
        ], 'jwt-config');

        // Registering the middleware
        Route::aliasMiddleware('jwt', \Kyojin\JWT\Http\Middleware\JwtMiddleware::class);
    }

    public function register()
    {
        // Merge the config
        $this->mergeConfigFrom(__DIR__ . '/../../config/jwt.php', 'jwt');

        // Binding to the service container
        $this->app->singleton('JWT', function () {
            return new JwtService();
        });
    }
}