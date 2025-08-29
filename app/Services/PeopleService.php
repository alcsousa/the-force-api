<?php

namespace App\Services;

use App\DTOs\FullPeopleDetail;
use App\DTOs\PeopleSearchResult;
use App\Enums\ErrorIdentifierEnum;
use App\Exceptions\Service\SearchFailedException;
use App\Repositories\FilmsRepositoryContract;
use App\Repositories\PeopleRepositoryContract;
use Throwable;

final class PeopleService implements PeopleServiceContract
{
    public function __construct(
        private readonly PeopleRepositoryContract $peopleRepository,
        private readonly FilmsRepositoryContract $filmsRepository
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
                errorIdentifierEnum: ErrorIdentifierEnum::PeopleSearchFailed,
                message: "Unable to complete search at this time for keyword {$name}",
                previous: $throwable
            );
        }
    }

    /**
     * @throws SearchFailedException
     */
    public function getById(string $id): FullPeopleDetail
    {
        try {
            $peopleDetail = $this->peopleRepository->getDetails($id);
            $films = [];

            foreach ($peopleDetail->filmIds as $filmId) {
                $films[] = $this->filmsRepository->getById($filmId);
            }

            return new FullPeopleDetail($peopleDetail, $films);
        } catch (Throwable $throwable) {
            throw new SearchFailedException(
                errorIdentifierEnum: ErrorIdentifierEnum::PeopleSearchFailed,
                message: "Unable to find person with id {$id}",
                previous: $throwable
            );
        }
    }
}
