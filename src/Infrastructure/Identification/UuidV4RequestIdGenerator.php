<?php

declare(strict_types=1);

namespace App\Infrastructure\Identification;

use App\Application\Contract\RequestIdGeneratorInterface;
use Override;
use Symfony\Component\Uid\Uuid;

final class UuidV4RequestIdGenerator implements RequestIdGeneratorInterface
{
    #[Override]
    public function generate(): string
    {
        return Uuid::v4()->toString();
    }
}
