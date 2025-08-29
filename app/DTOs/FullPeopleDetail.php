<?php

namespace App\DTOs;

final readonly class FullPeopleDetail
{
    /**
     * @param  FilmDetail[]  $films
     */
    public function __construct(
        public PeopleDetail $people,
        public array $films
    ) {}
}
