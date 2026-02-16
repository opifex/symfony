<?php

declare(strict_types=1);

namespace App\Infrastructure\HttpKernel\EventListener;

use App\Application\Contract\PrivacyDataProtectorInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\HttpKernel\Attribute\WithLogLevel;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsEventListener(event: ExceptionEvent::class)]
final class KernelExceptionEventListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly NormalizerInterface $normalizer,
        private readonly PrivacyDataProtectorInterface $privacyDataProtector,
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

        /** @var array<string, mixed> $request */
        $request = (array) $this->normalizer->normalize($event->getRequest());
        $exception = (array) $this->normalizer->normalize($throwable);
        $exceptionClass = new ReflectionClass($throwable);
        $httpStatus = ($exceptionClass->getAttributes(name: WithHttpStatus::class)[0] ?? null)?->newInstance();
        $logLevel = ($exceptionClass->getAttributes(name: WithLogLevel::class)[0] ?? null)?->newInstance();

        [$statusCode, $headers] = match (true) {
            $httpStatus instanceof WithHttpStatus => [$httpStatus->statusCode, $httpStatus->headers],
            $throwable instanceof HttpExceptionInterface => [$throwable->getStatusCode(), $throwable->getHeaders()],
            default => [500, []],
        };

        $this->logger->log(
            level: $logLevel !== null ? $logLevel->level : LogLevel::ERROR,
            message: $throwable->getMessage() !== '' ? $throwable->getMessage() : 'Application exception event.',
            context: array_filter(
                array: [
                    'route' => $event->getRequest()->attributes->get(key: '_route'),
                    'request' => $this->privacyDataProtector->protect($request),
                    'exception' => $exception,
                ],
                callback: static fn(mixed $value): bool => $value !== '' && $value !== [],
            ),
        );

        $event->allowCustomResponseCode();
        $event->setResponse(new JsonResponse($exception, $statusCode, $headers));
    }
}
