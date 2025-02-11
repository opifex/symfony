<?php

declare(strict_types=1);

namespace App\Application\MessageHandler\GetAccountsByCriteria;

use App\Domain\Entity\Account;
use App\Domain\Entity\AccountRoleCollection;
use App\Domain\Entity\AccountStatus;
use DateTimeInterface;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
final class GetAccountsByCriteriaItem
{
    public function __construct(
        public readonly string $uuid,
        public readonly string $email,
        public readonly string $locale,
        public readonly AccountStatus $status,
        public readonly AccountRoleCollection $roles,
        public readonly DateTimeInterface $createdAt,
    ) {
    }

    public static function create(Account $account): self
    {
        return new self(
            uuid: $account->getUuid(),
            email: $account->getEmail(),
            locale: $account->getLocale(),
            status: $account->getStatus(),
            roles: $account->getRoles(),
            createdAt: $account->getCreatedAt(),
        );
    }
}
