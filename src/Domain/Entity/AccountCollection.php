<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountCollection extends AbstractCollection
{
    public function __construct(Account ...$account)
    {
        parent::__construct(...$account);
    }
}
