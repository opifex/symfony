<?php

declare(strict_types=1);

namespace App\Application\Listener;

use App\Domain\Contract\PrivacyProtectorInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsEventListener(event: ExceptionEvent::class)]
final class ExceptionEventListener
{
    public function __construct(
        private LoggerInterface $logger,
        private NormalizerInterface $normalizer,
        private PrivacyProtectorInterface $privacyProtector,
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

        $request = (array) $this->normalizer->normalize($event->getRequest());
        $exception = (array) $this->normalizer->normalize($throwable);

        $this->logger->error('Application exception event.', array_filter([
            'route' => $event->getRequest()->attributes->get(key: '_route'),
            'request' => $this->privacyProtector->protect($request),
            'exception' => $exception,
        ]));

        $exceptionClass = new ReflectionClass($throwable);
        $httpStatus = ($exceptionClass->getAttributes(name: WithHttpStatus::class)[0] ?? null)?->newInstance();

        [$statusCode, $headers] = match (true) {
            $httpStatus instanceof WithHttpStatus => [$httpStatus->statusCode, $httpStatus->headers],
            $throwable instanceof HttpException => [$throwable->getStatusCode(), $throwable->getHeaders()],
            default => [Response::HTTP_INTERNAL_SERVER_ERROR, []],
        };

        $content = $this->normalizer->normalize($exception);

        $event->setResponse(new JsonResponse($content, $statusCode, $headers));
    }
}
