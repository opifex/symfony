<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Middleware;

use App\Application\Contract\RequestTraceManagerInterface;
use App\Infrastructure\Messenger\Stamp\RequestTraceStamp;
use Override;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class RequestTraceMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly RequestTraceManagerInterface $requestTraceManager,
    ) {
    }

    #[Override]
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $requestTraceStamp = $envelope->last(stampFqcn: RequestTraceStamp::class);
        $traceId = $this->requestTraceManager->getTraceId();

        if ($requestTraceStamp instanceof RequestTraceStamp) {
            $this->requestTraceManager->setTraceId($requestTraceStamp->getTraceId());

            return $stack->next()->handle($envelope, $stack);
        }

        $envelope = $envelope->with(new RequestTraceStamp($traceId));

        return $stack->next()->handle($envelope, $stack);
    }
}
