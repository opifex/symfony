<?php

declare(strict_types=1);

namespace App\Infrastructure\Messenger;

use App\Domain\Exception\ValidationFailedException;
use Override;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ValidationMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly ValidatorInterface $validator)
    {
    }

    #[Override]
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $violations = $this->validator->validate($envelope->getMessage());

        if ($violations->count()) {
            throw ValidationFailedException::fromViolations($violations);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
