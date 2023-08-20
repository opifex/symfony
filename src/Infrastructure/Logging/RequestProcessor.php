<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use App\Domain\Contract\MessageIdentifierInterface;
use App\Domain\Contract\PrivacyProtectorInterface;
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
    private array $request = [];

    public function __construct(
        private MessageIdentifierInterface $messageIdentifier,
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

        $this->request['identifier'] ??= $this->messageIdentifier->identify();
        $this->request['route'] ??= $request?->attributes->get(key: '_route');

        if (!array_key_exists(key: 'params', array: $this->request)) {
            $params = $request instanceof Request ? $this->normalizer->normalize($request) : null;
            $params = is_array($params) ? $this->privacyProtector->protect($params) : null;
            $this->request['params'] = $params;
        }

        $record->extra['request'] = array_filter($this->request);

        return $record;
    }
}
