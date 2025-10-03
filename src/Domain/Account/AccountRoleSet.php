<?php

declare(strict_types=1);

namespace App\Domain\Account;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountRoleSet
{
    /**
     * @param AccountRole[] $roles
     */
    final private function __construct(
        private readonly array $roles,
    ) {
    }

    public static function fromStrings(string ...$roles): self
    {
        return new self(array_map([AccountRole::class, 'fromString'], array_unique($roles)));
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return array_map(fn(AccountRole $role) => $role->toString(), $this->roles);
    }
}
