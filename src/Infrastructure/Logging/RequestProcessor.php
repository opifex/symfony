<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use App\Domain\Contract\IdentityManagerInterface;
use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;

#[AsMonologProcessor]
final class RequestProcessor
{
    public function __construct(private IdentityManagerInterface $identityManager)
    {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $record->extra['request'] = $this->identityManager->extractIdentifier();

        return $record;
    }
}
