<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository\Account;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Mapping;

#[Mapping\Entity]
#[Mapping\Table(name: 'account')]
final class AccountEntity
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        #[Mapping\Id]
        #[Mapping\Column(type: Types::GUID)]
        public readonly string $uuid,

        #[Mapping\Column(type: Types::DATETIME_IMMUTABLE, updatable: false)]
        public readonly DateTimeImmutable $createdAt,

        #[Mapping\Column(type: Types::STRING, unique: true, options: ['length' => 320])]
        public readonly string $email,

        #[Mapping\Column(type: Types::STRING, options: ['length' => 60])]
        public readonly string $password,

        #[Mapping\Column(type: Types::STRING, options: ['length' => 5])]
        public readonly string $locale,

        #[Mapping\Column(type: Types::JSON)]
        public readonly array $roles,

        #[Mapping\Column(type: Types::STRING, options: ['length' => 24])]
        public readonly string $status,
    ) {
    }
}
