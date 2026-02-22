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
                'id' => $account->id->toString(),
                'email' => $account->email->toString(),
                'locale' => $account->locale->toString(),
                'status' => $account->status->toString(),
                'roles' => $account->roles->toArray(),
                'created_at' => $account->createdAt->toAtomString(),
            ],
        );
    }

    #[Override]
    public function jsonSerialize(): mixed
    {
        return $this->payload;
    }
}
