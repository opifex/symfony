<?php

declare(strict_types=1);

namespace App\Domain\Foundation;

class HttpSpecification
{
    /** Standard HTTP status codes */
    public const int HTTP_OK = 200;
    public const int HTTP_CREATED = 201;
    public const int HTTP_NO_CONTENT = 204;
    public const int HTTP_BAD_REQUEST = 400;
    public const int HTTP_UNAUTHORIZED = 401;
    public const int HTTP_FORBIDDEN = 403;
    public const int HTTP_NOT_FOUND = 404;
    public const int HTTP_CONFLICT = 409;
    public const int HTTP_UNPROCESSABLE_ENTITY = 422;
    public const int HTTP_TOO_MANY_REQUESTS = 429;
    public const int HTTP_INTERNAL_SERVER_ERROR = 500;
}
