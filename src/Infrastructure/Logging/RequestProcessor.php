<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Monolog\Attribute\AsMonologProcessor;
use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\String\UnicodeString;

#[AsMonologProcessor]
final class RequestProcessor
{
    /** @var array&array<string, mixed> */
    private array $request = [];

    /** @var string[] */
    private array $templates = [
        'email' => '/(?<=.).(?=.*.{1}@)/u',
        'password' => '/./u',
    ];

    public function __construct(
        private NormalizerInterface $normalizer,
        private RequestStack $requestStack,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $request = $this->requestStack->getMainRequest();
        $routeName = $request?->attributes->get(key: '_route');

        if ($this->request === [] && $request instanceof Request && is_string($routeName)) {
            $this->request = [
                'message' => $this->extractMessage($routeName),
                'params' => $this->transformParams($this->extractParams($request)),
            ];
        }

        if ($this->request !== []) {
            $record->extra['request'] = $this->request;
        }

        return $record;
    }

    private function extractMessage(string $route): string
    {
        $controllerName = (new UnicodeString($route))->afterLast(needle: '\\');
        $messageName = $controllerName->beforeLast(needle: 'Controller');

        return $messageName->toString();
    }

    /**
     * @throws ExceptionInterface
     * @return array<string, mixed>
     */
    private function extractParams(Request $request): array
    {
        $params = $this->normalizer->normalize($request);

        return is_array($params) ? $params : [];
    }

    /**
     * @param array&array<string, mixed> $params
     *
     * @return array<string, mixed>
     */
    private function transformParams(array $params): array
    {
        foreach ($params as $key => $value) {
            if (array_key_exists($key, $this->templates) && is_string($value)) {
                $params[$key] = $this->replaceTemplate($value, $this->templates[$key]);
            } elseif (is_array($value)) {
                $params[$key] = $this->transformParams($value);
            }
        }

        return $params;
    }

    private function replaceTemplate(string $value, string $template): string
    {
        return (new UnicodeString($value))->replaceMatches($template, '*')->toString();
    }
}
