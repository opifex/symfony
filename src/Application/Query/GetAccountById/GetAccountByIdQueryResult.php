<?php

declare(strict_types=1);

namespace App\Application\Query\GetAccountById;

use App\Domain\Account\Account;
use App\Domain\Foundation\HttpSpecification;
use App\Domain\Foundation\MessageHandlerResult;

final class GetAccountByIdQueryResult extends MessageHandlerResult
{
    public static function success(Account $account): self
    {
        return new self(
            data: [
                'id' => $account->getId()->toString(),
                'email' => $account->getEmail()->toString(),
                'locale' => $account->getLocale()->toString(),
                'status' => $account->getStatus()->toString(),
                'roles' => $account->getRoles()->toArray(),
                'created_at' => $account->getCreatedAt()->toAtomString(),
            ],
            status: HttpSpecification::HTTP_OK,
        );
    }
}
