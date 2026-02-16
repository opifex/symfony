<?php

declare(strict_types=1);

namespace App\Domain\Foundation;

abstract class AbstractHandlerResult
{
    protected function __construct(
        private readonly mixed $payload = null,
    ) {
    }

    public function getPayload(): mixed
    {
        return $this->payload;
    }
}
