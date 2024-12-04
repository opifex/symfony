<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Common\AbstractCollection;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountRoleCollection extends AbstractCollection
{
    public function __construct(AccountRole ...$role)
    {
        parent::__construct(...$role);
    }

    /**
     * @return array<int|string, string>
     */
    public function toArray(): array
    {
        /** @var AccountRole[] $roles */
        $roles = $this->elements;

        return array_map(fn(AccountRole $role) => (string) $role->value, $roles);
    }
}
