<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger\Middleware;

use App\Infrastructure\Messenger\Stamp\CorrelationIdStamp;
use App\Infrastructure\Observability\CorrelationIdProvider;
use Override;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class CorrelationIdMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly CorrelationIdProvider $correlationIdProvider,
    ) {
    }

    #[Override]
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $correlationIdStamp = $envelope->last(stampFqcn: CorrelationIdStamp::class);

        if ($correlationIdStamp instanceof CorrelationIdStamp) {
            $this->correlationIdProvider->setCorrelationId($correlationIdStamp->getCorrelationId());

            return $stack->next()->handle($envelope, $stack);
        }

        $correlationId = $this->correlationIdProvider->getCorrelationId();
        $envelope = $envelope->with(new CorrelationIdStamp($correlationId));

        return $stack->next()->handle($envelope, $stack);
    }
}
