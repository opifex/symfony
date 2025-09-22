<?php

declare(strict_types=1);

namespace Tests\Support;

use Symfony\Component\HttpFoundation\Response;

class HttpMockClientResponse
{
    public function __construct(
        public readonly string $requestMethod,
        public readonly string $requestUrl,
        public readonly string $responseBody = '',
        public readonly int $responseStatusCode = Response::HTTP_OK,
        public readonly array $responseHeaders = [],
    ) {
    }
}
