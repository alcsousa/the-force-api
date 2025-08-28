<?php

namespace App\Services;

use App\DTOs\PeopleSearchResult;

interface PeopleServiceContract
{
    /**
     * @return PeopleSearchResult[]
     */
    public function searchPeopleByName(string $name): array;
}
