<?php

namespace App\Enums;

enum ErrorIdentifierEnum: string
{
    case PeopleSearchFailed = 'PEOPLE_SEARCH_FAILED';
    case FilmSearchFailed = 'FILM_SEARCH_FAILED';
}
