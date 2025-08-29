<?php

namespace App\Providers;

use App\Repositories\API\FilmsRepository;
use App\Repositories\API\PeopleRepository as ApiPeopleRepository;
use App\Repositories\FilmsRepositoryContract;
use App\Repositories\PeopleRepositoryContract;
use App\Services\PeopleService;
use App\Services\PeopleServiceContract;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        /*
         * Repositories
         */
        $this->app->singleton(
            PeopleRepositoryContract::class,
            ApiPeopleRepository::class
        );
        $this->app->singleton(
            FilmsRepositoryContract::class,
            FilmsRepository::class
        );

        /*
         * Services
         */
        $this->app->singleton(
            PeopleServiceContract::class,
            PeopleService::class
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
