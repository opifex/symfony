<?php

declare(strict_types=1);

namespace App\Application\Messenger;

use App\Domain\Contract\RequestIdStorageInterface;
use Override;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class RequestIdMiddleware implements MiddlewareInterface
{
    public function __construct(private RequestIdStorageInterface $requestIdStorage)
    {
    }

    #[Override]
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $requestIdStamp = $envelope->last(stampFqcn: RequestIdStamp::class);
        $requestIdStamp ??= new RequestIdStamp($this->requestIdStorage->getRequestId());
        $envelope = $envelope->withoutAll($requestIdStamp::class)->with($requestIdStamp);

        $this->requestIdStorage->setRequestId($requestIdStamp->getRequestId());

        return $stack->next()->handle($envelope, $stack);
    }
}
