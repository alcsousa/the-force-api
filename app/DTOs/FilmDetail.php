<?php

namespace App\DTOs;

class FilmDetail
{
    public function __construct(
        public int $id,
        public string $title
    ) {}
}
