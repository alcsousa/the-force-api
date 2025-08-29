<?php

namespace App\DTOs;

final readonly class FilmSearchResult
{
    public function __construct(
        public int $id,
        public string $title,
    ) {}
}
