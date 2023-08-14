<?php

declare(strict_types=1);

namespace App\Application\Messenger;

use App\Domain\Contract\MessageIdentifierInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final class IdentityMiddleware implements MiddlewareInterface
{
    public function __construct(private MessageIdentifierInterface $messageIdentifier)
    {
    }

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $identityStamp = $envelope->last(stampFqcn: IdentityStamp::class);
        $identityStamp ??= new IdentityStamp($this->messageIdentifier->identify());
        $envelope = $envelope->withoutAll(stampFqcn: IdentityStamp::class)->with($identityStamp);

        $this->messageIdentifier->replace($identityStamp->getIdentifier());

        return $stack->next()->handle($envelope, $stack);
    }
}
