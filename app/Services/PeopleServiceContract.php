<?php

namespace App\Services;

use App\DTOs\FullPeopleDetail;
use App\DTOs\PeopleSearchResult;

interface PeopleServiceContract
{
    /**
     * @return PeopleSearchResult[]
     */
    public function searchPeopleByName(string $name): array;

    public function getById(string $id): FullPeopleDetail;
}
