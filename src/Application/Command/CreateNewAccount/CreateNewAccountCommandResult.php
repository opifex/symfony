<?php

declare(strict_types=1);

namespace App\Application\Command\CreateNewAccount;

use App\Domain\Account\Account;
use JsonSerializable;
use Override;

final class CreateNewAccountCommandResult implements JsonSerializable
{
    private function __construct(
        private readonly mixed $payload = null,
    ) {
    }

    public static function success(Account $account): self
    {
        return new self(
            payload: [
                'id' => $account->getId()->toString(),
            ],
        );
    }

    #[Override]
    public function jsonSerialize(): mixed
    {
        return $this->payload;
    }
}
