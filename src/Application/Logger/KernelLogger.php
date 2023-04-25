<?php

declare(strict_types=1);

namespace App\Application\Logger;

use Monolog\LogRecord;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\RequestStack;

#[AutoconfigureTag(name: 'monolog.processor')]
final class KernelLogger
{
    public function __construct(private RequestStack $requestStack)
    {
    }

    public function __invoke(LogRecord $record): LogRecord
    {
        $request = $this->requestStack->getMainRequest();
        $identifier = $request?->headers->get(key: 'X-Request-Id');

        $record->extra['identifier'] = $identifier;

        return $record;
    }
}
