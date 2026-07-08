<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger;

use App\Application\Contract\FailedMessageCounterInterface;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;

final readonly class FailedMessageCounter implements FailedMessageCounterInterface
{
    public function __construct(
        #[Autowire(service: 'messenger.transport.failed')]
        private MessageCountAwareInterface $failedTransport,
    ) {
    }

    #[Override]
    public function count(): int
    {
        return $this->failedTransport->getMessageCount();
    }
}
