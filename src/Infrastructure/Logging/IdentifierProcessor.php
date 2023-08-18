<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use App\Domain\Contract\MessageIdentifierInterface;
use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;

#[AsMonologProcessor]
final class IdentifierProcessor
{
    private string $identifier;

    public function __construct(private MessageIdentifierInterface $messageIdentifier)
    {
        $this->identifier = $this->messageIdentifier->identify();
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $record->extra['identifier'] = $this->identifier;

        return $record;
    }
}
