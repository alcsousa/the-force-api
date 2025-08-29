<?php

namespace App\Repositories;

use App\DTOs\FilmDetail;
use App\DTOs\FilmSearchResult;

interface FilmsRepositoryContract
{
    /**
     * @return FilmSearchResult[]
     */
    public function searchByTitle(string $title): array;

    public function getById(int $id): FilmDetail;
}
