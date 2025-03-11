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
     * @param mixed $data
     * @param string|null $format
     * @param array<int|string, mixed> $context
     * @return array<int|string, mixed>
     */
    #[Override]
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        if (!$data instanceof Request) {
            throw new InvalidArgumentException(message: 'Object expected to be a valid request type.');
        }

        return (array) $this->transformTypes($this->filterParams($this->extractParams($data)));
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @param array<string, mixed> $context
     * @return bool
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
        return array_merge_recursive(
            $request->query->all(),
            $request->attributes->all(key: '_route_params'),
            $this->parseContent($request),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function parseContent(Request $request): array
    {
        try {
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
        return array_filter($params, static fn(string $key) => !str_starts_with($key, '_'), mode: ARRAY_FILTER_USE_KEY);
    }

    private function transformTypes(mixed $data): mixed
    {
        return match (true) {
            is_scalar($data) => match (true) {
                $data === (string) (int) $data => (int) $data,
                $data === (string) (float) $data => (float) $data,
                $data === 'true' || is_bool($data) => (bool) $data,
                $data === 'false' => false,
                $data === 'null' => null,
                default => $data,
            },
            is_array($data) => array_map(fn($item) => $this->transformTypes($item), $data),
            default => $data,
        };
    }
}
