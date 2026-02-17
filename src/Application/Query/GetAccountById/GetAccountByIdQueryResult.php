<?php

declare(strict_types=1);

namespace App\Application\Query\GetAccountById;

use App\Domain\Account\Account;
use JsonSerializable;
use Override;

final class GetAccountByIdQueryResult implements JsonSerializable
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
                'email' => $account->getEmail()->toString(),
                'locale' => $account->getLocale()->toString(),
                'status' => $account->getStatus()->toString(),
                'roles' => $account->getRoles()->toArray(),
                'created_at' => $account->getCreatedAt()->toAtomString(),
            ],
        );
    }

    #[Override]
    public function jsonSerialize(): mixed
    {
        return $this->payload;
    }
}
