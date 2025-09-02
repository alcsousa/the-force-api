<?php

namespace App\Enums;

enum HttpStatusEnum: int
{
    case Ok = 200;
    case BadRequest = 400;
    case Unauthorized = 401;
    case Forbidden = 403;
    case NotFound = 404;
    case InternalServerError = 500;
    case ServiceUnavailable = 503;
}
