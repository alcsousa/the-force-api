<?php

namespace App\Repositories;

use App\DTOs\PeopleDetail;
use App\DTOs\PeopleSearchResult;

interface PeopleRepositoryContract
{
    /**
     * @return PeopleSearchResult[]
     */
    public function searchPeopleByName(string $name): array;

    public function getDetails(int $id): PeopleDetail;
}
