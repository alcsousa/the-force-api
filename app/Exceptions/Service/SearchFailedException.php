<?php

namespace App\Exceptions\Service;

use App\Enums\ErrorIdentifierEnum;
use App\Enums\HttpStatusEnum;
use App\Exceptions\HttpRenderableException;
use Throwable;

class SearchFailedException extends HttpRenderableException
{
    public function __construct(
        private readonly ErrorIdentifierEnum $errorIdentifierEnum,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

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
        return $this->errorIdentifierEnum;
    }
}
