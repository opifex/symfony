<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use App\Domain\Contract\MessageIdentifierInterface;
use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;

#[AsMonologProcessor]
final class MessageProcessor
{
    public function __construct(private MessageIdentifierInterface $messageIdentifier)
    {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $record->extra['message'] = $this->messageIdentifier->identify();

        return $record;
    }
}
