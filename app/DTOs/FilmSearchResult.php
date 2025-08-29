<?php

namespace App\DTOs;

class FilmSearchResult
{
    public function __construct(
        public int $id,
        public string $title,
    ) {}
}
