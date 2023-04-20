<?php

declare(strict_types=1);

namespace App\Application\Listener\Kernel;

use InvalidArgumentException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\UuidV4;

#[AsEventListener(event: RequestEvent::class, priority: 4096)]
class RequestListener
{
    public function __construct(private NormalizerInterface $normalizer)
    {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(RequestEvent $event): void
    {
        $headers = $event->getRequest()->headers;
        $parameters = $this->normalizer->normalize($event->getRequest());

        try {
            $uuid = new UuidV4($headers->get(key: 'X-Request-Id'));
        } catch (InvalidArgumentException) {
            $uuid = new UuidV4();
        }

        $event->getRequest()->query->add(is_array($parameters) ? $parameters : []);
        $event->getRequest()->headers->add(['X-Request-Id' => $uuid->toRfc4122()]);
    }
}
