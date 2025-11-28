<?php

namespace Mdayo\User;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class UserRouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     */
    protected $namespace = 'Mdayo\User\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        parent::boot();

        // Optional: route model binding
        Route::model('user', \Mdayo\User\Models\User::class);
    }

    /**
     * Define the routes for the User package.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
    }

    /**
     * Define API routes
     */
    protected function mapApiRoutes(): void
    {
        $path = base_path('routes/user.php');

        if (file_exists($path)) {
            // Use published routes if they exist
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group($path);
        } else {
            // Otherwise, use package default
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(__DIR__.'/../routes/user.php');
        }
    }
}
