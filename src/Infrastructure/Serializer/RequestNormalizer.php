<?php

declare(strict_types=1);

namespace App\Infrastructure\Serializer;

use Override;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class RequestNormalizer implements NormalizerInterface
{
    /**
     * @param array<int|string, mixed> $context
     * @return array<int|string, mixed>
     */
    #[Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        if (!$data instanceof Request) {
            throw new InvalidArgumentException(message: 'Object expected to be a valid request type.');
        }

        return $this->filterParams($this->extractParams($data));
    }

    /**
     * @param array<string, mixed> $context
     */
    #[Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Request;
    }

    /**
     * @return array<string, bool>
     */
    #[Override]
    public function getSupportedTypes(?string $format): array
    {
        return [Request::class => true];
    }

    /**
     * @return array<string, mixed>
     */
    private function extractParams(Request $request): array
    {
        /** @var array<string, mixed> */
        return array_merge_recursive(
            (array) $this->transformTypes($request->query->all()),
            (array) $this->transformTypes($request->attributes->all(key: '_route_params')),
            $this->parseContent($request),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function parseContent(Request $request): array
    {
        try {
            /** @var array<string, mixed> */
            return $request->toArray();
        } catch (JsonException) {
            return [];
        }
    }

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    private function filterParams(array $params): array
    {
        return array_filter(
            array: $params,
            callback: static fn(string $key): bool => !str_starts_with($key, needle: '_'),
            mode: ARRAY_FILTER_USE_KEY,
        );
    }

    private function transformTypes(mixed $data): mixed
    {
        return match (true) {
            $data === null => null,
            is_string($data) => match (true) {
                $data === 'true' => true,
                $data === 'false' => false,
                $data === 'null' => null,
                preg_match(pattern: '/^-?\d+$/', subject: $data) === 1 => (int) $data,
                preg_match(pattern: '/^-?\d+\.\d+$/', subject: $data) === 1 => (float) $data,
                default => $data,
            },
            is_array($data) => array_map(fn($item) => $this->transformTypes($item), $data),
            default => $data,
        };
    }
}
