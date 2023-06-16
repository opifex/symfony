<?php

declare(strict_types=1);

namespace App\Application\Listener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Throwable;

#[AsEventListener(event: ExceptionEvent::class)]
final class ExceptionEventListener
{
    public function __construct(
        private LoggerInterface $logger,
        private NormalizerInterface $normalizer,
        private SerializerInterface $serializer,
    ) {
    }

    /**
     * @throws SerializerExceptionInterface
     */
    public function __invoke(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if ($throwable instanceof HandlerFailedException) {
            $throwable = $throwable->getPrevious() ?? $throwable;
        }

        $exception = (array)$this->normalizer->normalize($throwable, Throwable::class);
        $this->logger->error('Kernel exception event.', $exception);

        $code = $throwable instanceof HttpException ? $throwable->getStatusCode() : 500;
        $format = $event->getRequest()->getPreferredFormat(default: JsonEncoder::FORMAT) ?? JsonEncoder::FORMAT;
        $context = [JsonEncode::OPTIONS => JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT];
        $response = new Response($this->serializer->serialize($exception, $format, $context), $code);

        $event->setResponse($response);
    }
}
