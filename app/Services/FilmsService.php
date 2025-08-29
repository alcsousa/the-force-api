<?php

namespace App\Services;

use App\DTOs\FilmSearchResult;
use App\DTOs\FullFilmDetail;
use App\Enums\ErrorIdentifierEnum;
use App\Exceptions\Service\SearchFailedException;
use App\Repositories\FilmsRepositoryContract;
use App\Repositories\PeopleRepositoryContract;
use Throwable;

final class FilmsService implements FilmsServiceContract
{
    public function __construct(
        private readonly FilmsRepositoryContract $filmsRepository,
        private readonly PeopleRepositoryContract $peopleRepository
    ) {}

    /**
     * @return FilmSearchResult[]
     *
     * @throws SearchFailedException
     */
    public function searchFilmByTitle(string $title): array
    {
        try {
            return $this->filmsRepository->searchByTitle($title);
        } catch (Throwable $throwable) {
            throw new SearchFailedException(
                errorIdentifierEnum: ErrorIdentifierEnum::FilmSearchFailed,
                message: "Unable to complete search at this time for keyword {$title}",
                previous: $throwable
            );
        }
    }

    /**
     * @throws SearchFailedException
     */
    public function getById(string $id): FullFilmDetail
    {
        try {
            $filmDetail = $this->filmsRepository->getById((int) $id);
            $characters = [];

            foreach ($filmDetail->characterIds as $characterId) {
                $characters[] = $this->peopleRepository->getDetails($characterId);
            }

            return new FullFilmDetail($filmDetail, $characters);
        } catch (Throwable $throwable) {
            throw new SearchFailedException(
                errorIdentifierEnum: ErrorIdentifierEnum::FilmSearchFailed,
                message: "Unable to find film with id {$id}",
                previous: $throwable
            );
        }
    }
}
