<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Middleware;

use App\Application\Contract\RequestIdStorageInterface;
use App\Infrastructure\Messenger\Stamp\RequestIdStamp;
use Override;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class RequestIdMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly RequestIdStorageInterface $requestIdStorage,
    ) {
    }

    #[Override]
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $requestId = $this->requestIdStorage->getRequestId();
        $requestIdStamp = $envelope->last(stampFqcn: RequestIdStamp::class);

        if ($requestIdStamp instanceof RequestIdStamp) {
            $this->requestIdStorage->setRequestId($requestIdStamp->getRequestId());
        } elseif ($requestId !== null) {
            $envelope = $envelope->with(new RequestIdStamp($requestId));
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
