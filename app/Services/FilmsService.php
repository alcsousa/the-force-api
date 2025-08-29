<?php

namespace App\Services;

use App\DTOs\FilmSearchResult;
use App\Enums\ErrorIdentifierEnum;
use App\Exceptions\Service\SearchFailedException;
use App\Repositories\FilmsRepositoryContract;
use Throwable;

final class FilmsService implements FilmsServiceContract
{
    public function __construct(
        private readonly FilmsRepositoryContract $filmsRepository,
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
}
