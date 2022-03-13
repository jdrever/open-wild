<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\QueryService;
use App\Services\NBNQueryService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(QueryService::class, function ()
        {
            return new NBNQueryService();
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
