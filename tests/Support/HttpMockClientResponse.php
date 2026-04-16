<?php

declare(strict_types=1);

namespace Tests\Support;

use Symfony\Component\HttpFoundation\Response;

final readonly class HttpMockClientResponse
{
    public function __construct(
        public string $requestMethod,
        public string $requestUrl,
        public string $responseBody = '',
        public int $responseStatusCode = Response::HTTP_OK,
        public array $responseHeaders = [],
    ) {
    }
}
