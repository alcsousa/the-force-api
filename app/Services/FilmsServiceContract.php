<?php

namespace App\Services;

use App\DTOs\FilmSearchResult;

interface FilmsServiceContract
{
    /**
     * @return FilmSearchResult[]
     */
    public function searchFilmByTitle(string $title): array;
}
