<?php

namespace App\Repositories;

use App\DTOs\PeopleSearchResult;

interface PeopleRepositoryContract
{
    /**
     * @return PeopleSearchResult[]
     */
    public function searchPeopleByName(string $name): array;
}
