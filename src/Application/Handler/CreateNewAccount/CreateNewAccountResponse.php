<?php

declare(strict_types=1);

namespace App\Application\Handler\CreateNewAccount;

use App\Domain\Entity\Account;

final class CreateNewAccountResponse
{
    public readonly string $uuid;

    public function __construct(Account $account)
    {
        $this->uuid = $account->getUuid();
    }
}
