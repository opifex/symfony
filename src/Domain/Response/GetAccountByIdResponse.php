<?php

declare(strict_types=1);

namespace App\Domain\Response;

use App\Domain\Entity\Account;
use DateTimeImmutable;

final class GetAccountByIdResponse
{
    public readonly string $uuid;

    public readonly string $email;

    public readonly string $status;

    /**
     * @var string[]
     */
    public readonly array $roles;

    public readonly ?DateTimeImmutable $createdAt;

    public readonly ?DateTimeImmutable $updatedAt;

    public function __construct(Account $account)
    {
        $this->createdAt = $account->getCreatedAt();
        $this->email = $account->getEmail();
        $this->roles = $account->getRoles();
        $this->status = $account->getStatus();
        $this->updatedAt = $account->getUpdatedAt();
        $this->uuid = $account->getUuid();
    }
}
