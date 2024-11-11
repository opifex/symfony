<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\CreateNewAccount;

use App\Domain\Entity\Account;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class CreateNewAccountResponse
{
    public readonly string $uuid;

    public function __construct(Account $account)
    {
        $this->uuid = $account->getUuid();
    }
}
