<?php

declare(strict_types=1);

namespace App\Domain\Messenger;

use Symfony\Component\Messenger\Stamp\NonSendableStampInterface;

class ResponseStamp implements NonSendableStampInterface
{
    /**
     * @param int|null $code
     * @param array&array<string, string> $headers
     */
    public function __construct(private ?int $code = null, private array $headers = [])
    {
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    /**
     * @return array<string, string>
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
