<?php

declare(strict_types=1);

namespace App\Infrastructure\Uuid;

use App\Application\Contract\UuidIdentityGeneratorInterface;
use Symfony\Component\Uid\Uuid;

final class UuidIdentityGenerator implements UuidIdentityGeneratorInterface
{
    public function generate(): string
    {
        return Uuid::v7()->toString();
    }
}
