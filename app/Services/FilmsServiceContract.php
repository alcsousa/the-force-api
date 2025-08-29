<?php

namespace App\Services;

use App\DTOs\FilmSearchResult;
use App\DTOs\FullFilmDetail;

interface FilmsServiceContract
{
    /**
     * @return FilmSearchResult[]
     */
    public function searchFilmByTitle(string $title): array;

    public function getById(string $id): FullFilmDetail;
}
