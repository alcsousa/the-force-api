<?php

namespace App\Services;

use App\DTOs\PeopleSearchResult;
use App\Exceptions\Service\SearchFailedException;
use App\Repositories\PeopleRepositoryContract;
use Throwable;

readonly class PeopleService
{
    public function __construct(
        private PeopleRepositoryContract $peopleRepository
    ) {}

    /**
     * @return PeopleSearchResult[]
     *
     * @throws Throwable
     */
    public function searchPeopleByName(string $name): array
    {
        try {
            return $this->peopleRepository->searchPeopleByName($name);
        } catch (Throwable $throwable) {
            throw new SearchFailedException(
                message: "Unable to complete search at this time for keyword {$name}",
                previous: $throwable
            );
        }
    }
}
