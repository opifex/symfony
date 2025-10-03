<?php

declare(strict_types=1);

namespace App\Domain\Account;

use App\Domain\Foundation\ValueObject\AbstractUuidIdentifier;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountIdentifier extends AbstractUuidIdentifier
{
}
