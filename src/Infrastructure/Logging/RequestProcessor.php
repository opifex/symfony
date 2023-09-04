<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use App\Domain\Contract\PrivacyProtectorInterface;
use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[AsMonologProcessor]
final class RequestProcessor
{
    /** @var array&array<string, mixed> */
    private array $cache = [];

    public function __construct(
        private NormalizerInterface $normalizer,
        private PrivacyProtectorInterface $privacyProtector,
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $request = $this->requestStack->getMainRequest();

        if (!isset($this->cache['route'])) {
            $this->cache['route'] = $request?->attributes->get(key: '_route');
            $this->cache['route'] ??= $record->context['route'] ?? null;
            $this->cache = array_filter($this->cache);
        }

        if (!isset($this->cache['params'])) {
            $params = (array) $this->normalizer->normalize($request);
            $this->cache['params'] = $this->privacyProtector->protect($params);
            $this->cache = array_filter($this->cache);
        }

        if ($this->cache !== []) {
            $record->extra['request'] = $this->cache;
        }

        return $record;
    }
}
