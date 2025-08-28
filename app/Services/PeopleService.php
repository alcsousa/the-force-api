<?php

namespace App\Services;

use App\DTOs\PeopleSearchResult;
use App\Exceptions\Service\SearchFailedException;
use App\Repositories\PeopleRepositoryContract;
use Throwable;

class PeopleService implements PeopleServiceContract
{
    public function __construct(
        private readonly PeopleRepositoryContract $peopleRepository
    ) {}

    /**
     * @return PeopleSearchResult[]
     *
     * @throws SearchFailedException
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
