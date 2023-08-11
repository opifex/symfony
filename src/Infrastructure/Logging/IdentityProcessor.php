<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use App\Domain\Contract\IdentityManagerInterface;
use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;

#[AsMonologProcessor]
final class IdentityProcessor
{
    public function __construct(private IdentityManagerInterface $identityManager)
    {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $record->extra['uuid'] = $this->identityManager->extractIdentifier();

        return $record;
    }
}
