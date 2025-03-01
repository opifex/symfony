<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

use Symfony\Component\Messenger\Exception\LogicException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

abstract class AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    protected function handleMessage(object $message): mixed
    {
        $envelope = $this->messageBus->dispatch($message);
        $handledStamps = $envelope->all(stampFqcn: HandledStamp::class);

        if (count($handledStamps) !== 1) {
            throw new LogicException(
                message: sprintf(
                    'Message of type "%s" was handled multiple times, but only one handler is expected.',
                    get_debug_type($envelope->getMessage()),
                ),
            );
        }

        return $handledStamps[0]->getResult();
    }

    protected function normalizeResult(mixed $data): mixed
    {
        return $this->normalizer->normalize($data);
    }
}
