<?php

declare(strict_types=1);

namespace App\Application\EventListener;

use App\Domain\Contract\Protection\PrivacyDataProtectorInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
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

        $this->logger->error('Application exception event.', array_filter([
            'route' => $event->getRequest()->attributes->get(key: '_route'),
            'request' => $this->privacyDataProtector->protect($request),
            'exception' => $exception,
        ]));

        $exceptionClass = new ReflectionClass($throwable);
        $httpStatus = ($exceptionClass->getAttributes(name: WithHttpStatus::class)[0] ?? null)?->newInstance();

        [$statusCode, $headers] = match (true) {
            $httpStatus instanceof WithHttpStatus => [$httpStatus->statusCode, $httpStatus->headers],
            $throwable instanceof HttpExceptionInterface => [$throwable->getStatusCode(), $throwable->getHeaders()],
            default => [Response::HTTP_INTERNAL_SERVER_ERROR, []],
        };

        $content = $this->normalizer->normalize($exception);

        $event->setResponse(new JsonResponse($content, $statusCode, $headers));
    }
}
