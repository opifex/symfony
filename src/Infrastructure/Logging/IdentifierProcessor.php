<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use App\Domain\Contract\RequestIdentifierInterface;
use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\RequestStack;

#[AsMonologProcessor]
final class IdentifierProcessor
{
    public function __construct(
        private RequestIdentifierInterface $requestIdentifier,
        private RequestStack $requestStack,
    ) {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $request = $this->requestStack->getMainRequest();
        $identifier = (string) $request?->headers->get(key: 'X-Request-Id');

        $this->requestIdentifier->setIdentifier($identifier);

        $record->extra['identifier'] = $this->requestIdentifier->getIdentifier();

        return $record;
    }
}
