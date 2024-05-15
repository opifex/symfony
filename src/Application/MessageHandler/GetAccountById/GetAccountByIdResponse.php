<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountById;

use App\Domain\Entity\Account;
use DateTimeInterface;

final class GetAccountByIdResponse
{
    public readonly string $uuid;

    public readonly string $email;

    public readonly string $locale;

    public readonly string $status;

    /** @var string[] */
    public readonly array $roles;

    public readonly DateTimeInterface $createdAt;

    public function __construct(Account $account)
    {
        $this->uuid = $account->getUuid();
        $this->email = $account->getEmail();
        $this->locale = $account->getLocale();
        $this->status = $account->getStatus();
        $this->roles = $account->getRoles();
        $this->createdAt = $account->getCreatedAt();
    }
}
