<?php

namespace App\DTOs;

final readonly class FilmDetail
{
    /**
     * @param  int[]  $characterIds
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $openingCrawl = '',
        public array $characterIds = [],
    ) {}
}
