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
     * @param mixed $object
     * @param string|null $format
     * @param array<string, mixed> $context
     * @return array<string, mixed>
     */
    #[Override]
    public function normalize(mixed $object, ?string $format = null, array $context = []): array
    {
        if (!$object instanceof Request) {
            throw new InvalidArgumentException(message: 'Object expected to be a valid request type.');
        }

        $params = $this->extractParams($object);
        $params = $this->filterParams($params);
        $params = $this->transformTypes($params);

        return is_array($params) ? $params : [];
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
            $params = $request->toArray();
        } catch (JsonException) {
            $params = [];
        }

        return $params;
    }

    /**
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    private function filterParams(array $params): array
    {
        return array_filter($params, static fn(string $key) => !str_starts_with($key, '_'), mode: ARRAY_FILTER_USE_KEY);
    }

    private function transformTypes(mixed $params): mixed
    {
        return match (true) {
            is_scalar($params) => match (true) {
                $params === (string) (int) $params => (int) $params,
                $params === (string) (float) $params => (float) $params,
                $params === 'true' || is_bool($params) => (bool) $params,
                $params === 'false' => false,
                $params === 'null' => null,
                default => $params,
            },
            is_array($params) => array_map(fn($item) => $this->transformTypes($item), $params),
            default => $params,
        };
    }
}
