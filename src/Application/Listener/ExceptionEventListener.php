<?php

declare(strict_types=1);

namespace App\Application\Listener;

use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
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
     * @throws ReflectionException
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

        $refClass = new ReflectionClass($throwable);
        $httpStatus = ($refClass->getAttributes(name: WithHttpStatus::class)[0] ?? null)?->newInstance();

        [$status, $headers] = match (true) {
            $httpStatus instanceof WithHttpStatus => [$httpStatus->statusCode, $httpStatus->headers],
            $throwable instanceof HttpException => [$throwable->getStatusCode(), $throwable->getHeaders()],
            default => [Response::HTTP_INTERNAL_SERVER_ERROR, []],
        };

        $headers = ['Content-Type' => 'application/json', ...$headers];
        $context = [JsonEncode::OPTIONS => JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT];
        $content = $this->serializer->serialize($exception, format: JsonEncoder::FORMAT, context: $context);

        $event->setResponse(new Response($content, $status, $headers));
    }
}
