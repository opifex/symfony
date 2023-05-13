<?php

declare(strict_types=1);

namespace App\Application\Serializer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class RequestNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param mixed $object
     * @param string|null $format
     * @param array&array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        if (!$object instanceof Request) {
            throw new InvalidArgumentException(message: 'Object expected to be a valid request type.');
        }

        $parameters = $this->transformTypes($this->extractParametersFromRequest($object));

        return is_array($parameters) ? $parameters : [];
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @param array&array<string, mixed> $context
     *
     * @return bool
     */
    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof Request;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    private function extractParametersFromRequest(Request $request): array
    {
        return $this->filterParameters(
            data: array_merge_recursive(
                $request->query->all(),
                $request->request->all(),
                $request->attributes->get(key: '_route_params') ?? [],
                (array)json_decode($request->getContent(), associative: true),
            )
        );
    }

    private function transformTypes(mixed $data): mixed
    {
        $cast = function (mixed $data): mixed {
            return match (true) {
                is_array($data) => $this->transformTypes($data),
                is_null($data) || $data === 'null' => null,
                is_bool($data) || $data === 'true' => (bool)$data,
                is_scalar($data) => match (true) {
                    $data === (string)(int)$data => (int)$data,
                    $data === (string)(float)$data => (float)$data,
                    $data === 'false' => false,
                    default => $data,
                },
                default => $data,
            };
        };

        return match (true) {
            is_scalar($data) => $cast($data),
            is_array($data) => array_map(fn($item) => $cast($item), $data),
            default => $data,
        };
    }

    /**
     * @param array&array<string, mixed> $data
     *
     * @return array<string, mixed>
     */
    private function filterParameters(array $data): array
    {
        return array_filter($data, fn(string $key) => !str_starts_with($key, '_'), mode: ARRAY_FILTER_USE_KEY);
    }
}
