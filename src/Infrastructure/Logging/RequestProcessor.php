<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use App\Domain\Contract\PrivacyProtectorInterface;
use App\Domain\Contract\RequestIdentifierInterface;
use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\Request;
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
        private RequestIdentifierInterface $requestIdentifier,
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $request = $this->requestStack->getMainRequest();

        if (!array_key_exists(key: 'params', array: $this->cache)) {
            $params = $request instanceof Request ? $this->normalizer->normalize($request) : null;
            $this->cache['params'] = is_array($params) ? $this->privacyProtector->protect($params) : null;
        }

        $record->extra['request'] = array_filter([
            'identifier' => $this->requestIdentifier->identify($request),
            'route' => $request?->attributes->get(key: '_route') ?? $record->context['route'] ?? null,
            'params' => $this->cache['params'] ?? null,
        ]);

        return $record;
    }
}
