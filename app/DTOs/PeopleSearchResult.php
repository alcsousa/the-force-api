<?php

namespace App\DTOs;

final readonly class PeopleSearchResult
{
    public function __construct(
        public string $id,
        public string $name
    ) {}
}
