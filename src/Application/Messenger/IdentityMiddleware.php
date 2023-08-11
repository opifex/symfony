<?php

declare(strict_types=1);

namespace App\Application\Messenger;

use App\Domain\Contract\IdentityManagerInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class IdentityMiddleware implements MiddlewareInterface
{
    public function __construct(private IdentityManagerInterface $identityManager)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $identityStamp = $envelope->last(stampFqcn: IdentityStamp::class);
        $identityStamp ??= new IdentityStamp($this->identityManager->extractIdentifier());
        $envelope = $envelope->withoutAll(stampFqcn: IdentityStamp::class)->with($identityStamp);

        $this->identityManager->changeIdentifier($identityStamp->getIdentifier());

        return $stack->next()->handle($envelope, $stack);
    }
}
