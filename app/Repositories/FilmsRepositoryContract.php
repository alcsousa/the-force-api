<?php

namespace App\Repositories;

use App\DTOs\FilmDetail;

interface FilmsRepositoryContract
{
    public function getById(int $id): FilmDetail;
}
