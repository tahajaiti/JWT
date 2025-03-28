<?php

namespace Kyojin\JWT\Providers;

use Kyojin\JWT\Services\JwtService;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * JwtServiceProvider for Laravel JWT Authentication Package
 * 
 * This service provider handles the configuration, binding, and middleware 
 * registration for the JWT authentication package.
 * 
 * @package Kyojin\JWT\Providers
 * @version 1.0.1
 */
class JwtServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     * 
     * This method is responsible for:
     * - Publishing configuration files
     * - Registering package middleware
     * 
     * @return void
     */
    public function boot()
    {
        // publish config file to allow customization
        $this->publishes([
            __DIR__ . '/../../config/jwt.php' => $this->app->configPath('jwt.php'),
        ], 'jwt-config');

        // register the jwt middleware
        Route::aliasMiddleware('jwt', \Kyojin\JWT\Http\Middleware\JwtMiddleware::class);
    }

    /**
     * Register any application services.
     * 
     * This method is responsible for:
     * - Merging package configuration
     * - Binding the JWT service to the service container
     * 
     * @return void
     */
    public function register()
    {
        // using laravel pre-defined method to merge config files
        $this->mergeConfigFrom(__DIR__ . '/../../config/jwt.php', 'jwt');

        // bind the jwt service to the service container
        $this->app->singleton('JWT', function () {
            return new JwtService();
        });
    }
}