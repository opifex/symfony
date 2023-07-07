<?php

declare(strict_types=1);

namespace App\Application\Handler\GetAccountsByCriteria;

use App\Domain\Contract\AccountInterface;
use DateTimeInterface;

final class GetAccountsByCriteriaItem
{
    public readonly string $uuid;

    public readonly string $email;

    public readonly string $status;

    /** @var string[] */
    public readonly array $roles;

    public readonly ?DateTimeInterface $createdAt;

    public readonly ?DateTimeInterface $updatedAt;

    public function __construct(AccountInterface $account)
    {
        $this->createdAt = $account->getCreatedAt();
        $this->email = $account->getEmail();
        $this->roles = $account->getRoles();
        $this->status = $account->getStatus();
        $this->updatedAt = $account->getUpdatedAt();
        $this->uuid = $account->getUuid();
    }
}
