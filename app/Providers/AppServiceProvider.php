<?php

namespace App\Providers;

use App\Repositories\API\PeopleRepository as ApiPeopleRepository;
use App\Repositories\PeopleRepositoryContract;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(
            PeopleRepositoryContract::class,
            ApiPeopleRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
