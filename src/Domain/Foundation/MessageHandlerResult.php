<?php

declare(strict_types=1);

namespace App\Domain\Foundation;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class MessageHandlerResult
{
    /**
     * @param array<string, string|array|null> $headers
     */
    protected function __construct(
        private readonly mixed $data = null,
        private readonly int $status = 200,
        private readonly array $headers = [],
    ) {
    }

    public function toResponse(): Response
    {
        return new JsonResponse($this->data, $this->status, $this->headers);
    }
}
