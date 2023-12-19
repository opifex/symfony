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
    private static array $cache = [];

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

        if (!isset(self::$cache['route'])) {
            self::$cache['route'] = $request?->attributes->get(key: '_route');
            self::$cache['route'] ??= $record->context['route'] ?? null;
            self::$cache = array_filter(self::$cache);
        }

        if (!isset(self::$cache['params'])) {
            $params = (array) $this->normalizer->normalize($request);
            self::$cache['params'] = $this->privacyProtector->protect($params);
            self::$cache = array_filter(self::$cache);
        }

        if (self::$cache !== []) {
            $record->extra['request'] = self::$cache;
        }

        return $record;
    }
}
