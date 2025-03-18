<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Common\AbstractCollection;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountRoleCollection extends AbstractCollection
{
    /** @var array<int|string, AccountRole> */
    protected array $elements = [];

    public function __construct(
        AccountRole ...$role,
    ) {
        parent::__construct(...$role);
    }

    /**
     * @return array<int|string, string>
     */
    public function toArray(): array
    {
        return array_map(static fn(AccountRole $role) => $role->value, $this->elements);
    }
}
