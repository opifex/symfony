<?php

declare(strict_types=1);

namespace App\Domain\Model;

use DateTimeImmutable;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class Account
{
    /**
     * @param string[] $roles
     */
    public function __construct(
        public readonly string $id,
        public readonly DateTimeImmutable $createdAt,
        public string $email,
        public string $password,
        public string $locale,
        public array $roles,
        public string $status,
    ) {
    }

    public static function create(string $email, string $hashedPassword, string $locale): Account
    {
        return new self(
            id: '',
            createdAt: new DateTimeImmutable(),
            email: $email,
            password: $hashedPassword,
            locale: $locale,
            roles: [AccountRole::USER],
            status: AccountStatus::CREATED,
        );
    }

    public function isActive(): bool
    {
        return $this->status === AccountStatus::ACTIVATED;
    }
}
