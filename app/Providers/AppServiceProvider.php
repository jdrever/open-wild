<?php

namespace App\Providers;

use App\Interfaces\QueryService;
use App\Services\CachedNbnQueryService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(QueryService::class, function () {
            return new CachedNbnQueryService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
