<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

abstract class AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    protected function handleResult(object $message): Response
    {
        $envelope = $this->messageBus->dispatch($message);
        $handledStamps = $envelope->all(stampFqcn: HandledStamp::class);
        $handledResult = $handledStamps[0]->getResult();

        if (count($handledStamps) !== 1) {
            $exceptionMessage = 'Message of type "%s" was handled multiple times, but only one handler is expected.';
            throw new LogicException(sprintf($exceptionMessage, get_debug_type($envelope->getMessage())));
        }

        if (!$handledResult instanceof Response) {
            $exceptionMessage = 'Message handler for type "%s" must return valid Response object.';
            throw new LogicException(sprintf($exceptionMessage, get_debug_type($envelope->getMessage())));
        }

        return $handledResult;
    }
}
