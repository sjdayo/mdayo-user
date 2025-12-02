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
   
        // Publish config
        $this->publishes([
            __DIR__.'/../config/user.php' => config_path('user.php'),
        ], 'user-config');

        $this->publishes([
            __DIR__.'/../database/seeders/' => database_path('seeders')
        ], 'user-seeders');
      
    }
}
