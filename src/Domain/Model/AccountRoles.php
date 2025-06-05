<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountRoles
{
    /**
     * @param Role[] $roles
     */
    final protected function __construct(
        private readonly array $roles = [],
    ) {
    }

    public static function fromStrings(string ...$roles): self
    {
        return new self(array_map([Role::class, 'fromString'], $roles));
    }

    /**
     * @return string[]
     */
    public function toArray(): array
    {
        return array_map(fn(Role $role) => $role->toString(), $this->roles);
    }
}
