<?php

namespace App\Providers;

use App\Repositories\API\FilmsRepository as ApiFilmsRepository;
use App\Repositories\API\PeopleRepository as ApiPeopleRepository;
use App\Repositories\FilmsRepositoryContract;
use App\Repositories\PeopleRepositoryContract;
use App\Services\FilmsService;
use App\Services\FilmsServiceContract;
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
            ApiFilmsRepository::class
        );

        /*
         * Services
         */
        $this->app->singleton(
            PeopleServiceContract::class,
            PeopleService::class
        );
        $this->app->singleton(
            FilmsServiceContract::class,
            FilmsService::class
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
