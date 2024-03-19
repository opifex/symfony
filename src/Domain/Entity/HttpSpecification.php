<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class HttpSpecification
{
    /** Custom HTTP headers */
    public const string HEADER_X_REQUEST_ID = 'X-Request-Id';
    public const string HEADER_X_TOTAL_COUNT = 'X-Total-Count';
    /** Standard HTTP status reasons */
    public const string STATUS_BAD_REQUEST = 'Bad Request';
    public const string STATUS_CONFLICT = 'Conflict';
    public const string STATUS_CREATED = 'Created';
    public const string STATUS_FORBIDDEN = 'Forbidden';
    public const string STATUS_NOT_FOUND = 'Not Found';
    public const string STATUS_NO_CONTENT = 'No Content';
    public const string STATUS_OK = 'OK';
    public const string STATUS_UNAUTHORIZED = 'Unauthorized';
}
