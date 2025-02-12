<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\CreateNewAccount;

use App\Domain\Entity\Account;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class CreateNewAccountResponse
{
    public function __construct(
        public readonly string $uuid,
    ) {
    }

    public static function create(Account $account): self
    {
        return new self($account->getUuid());
    }
}
