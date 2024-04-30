<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Domain\Contract\RequestIdGeneratorInterface;
use Override;
use Symfony\Component\Uid\Uuid;

final class UuidV4RequestIdGenerator implements RequestIdGeneratorInterface
{
    #[Override]
    public function generate(): string
    {
        return Uuid::v4()->toRfc4122();
    }
}
