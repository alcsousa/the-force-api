<?php

namespace App\Exceptions;

use App\Enums\ErrorIdentifierEnum;
use App\Enums\HttpStatusEnum;
use Exception;
use Psr\Log\LogLevel;

abstract class HttpRenderableException extends Exception
{
    abstract public function httpStatus(): HttpStatusEnum;

    abstract public function userMessage(): string;

    abstract public function errorIdentifier(): ErrorIdentifierEnum;

    protected function logLevel(): string
    {
        return LogLevel::ERROR;
    }
}
