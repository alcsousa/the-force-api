<?php

namespace App\DTOs;

final readonly class FullFilmDetail
{
    /**
     * @param  PeopleDetail[]  $characters
     */
    public function __construct(
        public FilmDetail $film,
        public array $characters = []
    ) {}
}
