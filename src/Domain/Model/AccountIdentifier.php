<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Model\Common\AbstractUuidIdentifier;
use Symfony\Component\DependencyInjection\Attribute\Exclude;

#[Exclude]
class AccountIdentifier extends AbstractUuidIdentifier
{
}
