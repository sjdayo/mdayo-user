<?php

namespace Mdayo\User;

use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/user.php',
            'user'
        );
    }


    public function boot()
    {
        // Load package routes
        $this->loadRoutesFrom(__DIR__.'/../routes/user.php');
        
        $this->publishes([
            __DIR__ . '/../routes/user.php' => base_path('routes/user.php')
        ], 'user-routes');
        
        // No need user migration just use the default migration in laaravel ^11x
        //$this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Publish config
        $this->publishes([
            __DIR__.'/../config/user.php' => config_path('user.php'),
        ], 'user-config');

        $this->publishes([
            __DIR__.'/../database/seeders/' => database_path('seeders')
        ], 'user-seeders');
      
    }
}
