<?php

declare(strict_types=1);

namespace App\Infrastructure\HttpKernel\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsEventListener(event: RequestEvent::class)]
final readonly class RequestPayloadEventListener
{
    public function __construct(
        private NormalizerInterface $normalizer,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $payload = (array) $this->normalizer->normalize($request);

        $request->attributes->add(['_payload' => $payload]);
    }
}
