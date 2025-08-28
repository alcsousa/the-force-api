<?php

namespace App\Exceptions\Service;

use App\Enums\ErrorIdentifierEnum;
use App\Enums\HttpStatusEnum;
use App\Exceptions\HttpRenderableException;

class SearchFailedException extends HttpRenderableException
{
    public function httpStatus(): HttpStatusEnum
    {
        return HttpStatusEnum::ServiceUnavailable;
    }

    public function userMessage(): string
    {
        return 'Unable to complete search at this time. Please try again later.';
    }

    public function errorIdentifier(): ErrorIdentifierEnum
    {
        return ErrorIdentifierEnum::PeopleSearchFailed;
    }
}
