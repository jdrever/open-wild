<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\QueryService;
use App\Services\NbnQueryService;

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
            return new NbnQueryService();
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
