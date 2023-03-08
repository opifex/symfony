<?php

declare(strict_types=1);

namespace App\Application\Serializer;

use Symfony\Component\HttpFoundation\Exception\JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class RequestNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }

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

    /**
     * @return array<string, mixed>
     */
    private function extractContentFromRequest(Request $request): array
    {
        try {
            $content = $request->toArray();
        } catch (JsonException) {
            $content = [];
        }

        return $content;
    }

    /**
     * @return array<string, mixed>
     */
    private function extractParametersFromRequest(Request $request): array
    {
        return array_merge_recursive(
            $request->attributes->all(),
            $request->query->all(),
            $request->request->all(),
            $this->extractContentFromRequest($request),
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
}
