<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Mapping;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'account')]
class AccountEntity
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(name: 'id', type: Types::GUID)]
        public ?string $id = null,

        #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE, updatable: false)]
        public DateTimeImmutable $createdAt = new DateTimeImmutable(),

        #[ORM\Column(name: 'email', type: Types::STRING, unique: true, options: ['length' => 320])]
        public string $email = '',

        #[ORM\Column(name: 'password', type: Types::STRING, options: ['length' => 60])]
        public string $password = '',

        #[ORM\Column(name: 'locale', type: Types::STRING, options: ['length' => 5])]
        public string $locale = '',

        /** @var string[] $roles */
        #[ORM\Column(name: 'roles', type: Types::JSON)]
        public array $roles = [],

        #[ORM\Column(name: 'status', type: Types::STRING, options: ['length' => 24])]
        public string $status = '',
    ) {
    }
}
