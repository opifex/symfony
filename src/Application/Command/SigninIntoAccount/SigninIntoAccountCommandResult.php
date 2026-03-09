<?php

declare(strict_types=1);

namespace App\Application\Command\SigninIntoAccount;

use JsonSerializable;
use Override;

final readonly class SigninIntoAccountCommandResult implements JsonSerializable
{
    private function __construct(
        private mixed $payload = null,
    ) {
    }

    public static function success(string $accessToken): self
    {
        return new self(
            payload: [
                'access_token' => $accessToken,
            ],
        );
    }

    #[Override]
    public function jsonSerialize(): mixed
    {
        return $this->payload;
    }
}
