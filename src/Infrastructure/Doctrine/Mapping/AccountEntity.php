<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Mapping;

use App\Domain\Model\AccountStatus;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
#[ORM\Entity]
#[ORM\Table(name: 'account', uniqueConstraints: [new ORM\UniqueConstraint(columns: ['email'])])]
class AccountEntity
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: Types::GUID)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    public ?string $id = null;

    public function __construct(
        #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE, updatable: false)]
        public readonly DateTimeImmutable $createdAt = new DateTimeImmutable(),

        #[ORM\Column(name: 'email', type: Types::STRING, unique: true, options: ['length' => 320])]
        public string $email = '',

        #[ORM\Column(name: 'password', type: Types::STRING, options: ['length' => 60])]
        public string $password = '',

        #[ORM\Column(name: 'locale', type: Types::STRING, options: ['length' => 5])]
        public string $locale = 'en_US',

        /** @var string[] $roles */
        #[ORM\Column(name: 'roles', type: Types::JSON)]
        public array $roles = [],

        #[ORM\Column(name: 'status', type: Types::STRING, options: ['length' => 24])]
        public string $status = AccountStatus::CREATED,
    ) {
    }
}
