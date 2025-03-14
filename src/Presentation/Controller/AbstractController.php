<?php

declare(strict_types=1);

namespace App\Presentation\Controller;

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
        return $this->messageBus->dispatch($message)->last(stampFqcn: HandledStamp::class)?->getResult();
    }

    protected function normalizeResult(mixed $data): mixed
    {
        return $this->normalizer->normalize($data);
    }
}
