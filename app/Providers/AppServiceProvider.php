<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\StockMovement;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Pagination\Paginator::useBootstrapFive();
        StockMovement::observe(StockMovement::class);
    }
}
