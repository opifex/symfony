<?php

declare(strict_types=1);

namespace App\Application\Command\CreateNewAccount;

use App\Domain\Account\Account;
use App\Domain\Foundation\AbstractHandlerResult;

final class CreateNewAccountCommandResult extends AbstractHandlerResult
{
    public static function success(Account $account): self
    {
        return new self(
            payload: [
                'id' => $account->getId()->toString(),
            ],
        );
    }
}
