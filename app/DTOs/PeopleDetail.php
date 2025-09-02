<?php

namespace App\DTOs;

final readonly class PeopleDetail
{
    /**
     * @param  int[]  $filmIds
     */
    public function __construct(
        public int $id,
        public string $name,
        public string $birthYear,
        public string $gender,
        public string $eyeColor,
        public string $hairColor,
        public string $height,
        public string $mass,
        public array $filmIds
    ) {}
}
