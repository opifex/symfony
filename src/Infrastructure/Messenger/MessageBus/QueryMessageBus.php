<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\MessageBus;

use App\Application\Contract\QueryMessageBusInterface;
use Override;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

#[Lazy]
final class QueryMessageBus implements QueryMessageBusInterface
{
    public function __construct(
        #[Autowire(service: 'query.bus')]
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Override]
    public function ask(object $query): mixed
    {
        $envelope = $this->messageBus->dispatch($query);
        $handledStamps = $envelope->all(stampFqcn: HandledStamp::class);
        $handledResult = $handledStamps[0]->getResult();

        if (count($handledStamps) !== 1) {
            $exceptionMessage = 'Message of type "%s" was handled multiple times, but only one handler is expected.';
            throw new LogicException(sprintf($exceptionMessage, get_debug_type($envelope->getMessage())));
        }

        return $handledResult;
    }
}
