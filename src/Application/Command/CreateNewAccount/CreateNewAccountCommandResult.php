<?php

declare(strict_types=1);

namespace App\Application\Command\CreateNewAccount;

use App\Domain\Account\Account;
use App\Domain\Foundation\MessageHandlerResult;

final class CreateNewAccountCommandResult extends MessageHandlerResult
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
