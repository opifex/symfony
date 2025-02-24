<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Mapping\Default;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as Mapping;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
#[Mapping\Entity]
#[Mapping\Table(name: 'account')]
final class AccountEntity
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        #[Mapping\Id]
        #[Mapping\Column(name: 'uuid', type: Types::GUID)]
        public readonly string $uuid,

        #[Mapping\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE, updatable: false)]
        public readonly DateTimeImmutable $createdAt,

        #[Mapping\Column(name: 'email', type: Types::STRING, unique: true, options: ['length' => 320])]
        public readonly string $email,

        #[Mapping\Column(name: 'password', type: Types::STRING, options: ['length' => 60])]
        public readonly string $password,

        #[Mapping\Column(name: 'locale', type: Types::STRING, options: ['length' => 5])]
        public readonly string $locale,

        #[Mapping\Column(name: 'roles', type: Types::JSON)]
        public readonly array $roles,

        #[Mapping\Column(name: 'status', type: Types::STRING, options: ['length' => 24])]
        public readonly string $status,
    ) {
    }
}
