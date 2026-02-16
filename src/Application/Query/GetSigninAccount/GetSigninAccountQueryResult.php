<?php

declare(strict_types=1);

namespace App\Application\Query\GetSigninAccount;

use App\Domain\Account\Account;
use App\Domain\Foundation\AbstractHandlerResult;

final class GetSigninAccountQueryResult extends AbstractHandlerResult
{
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
}
