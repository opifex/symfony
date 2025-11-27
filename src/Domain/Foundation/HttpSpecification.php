<?php

declare(strict_types=1);

namespace App\Domain\Foundation;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class HttpSpecification
{
    /** Custom HTTP headers */
    public const string HEADER_X_CORRELATION_ID = 'X-Correlation-Id';
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
