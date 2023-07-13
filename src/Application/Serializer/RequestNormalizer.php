<?php

declare(strict_types=1);

namespace App\Application\Serializer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class RequestNormalizer implements NormalizerInterface
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

        $parameters = $this->extractParameters($object);
        $parameters = $this->filterParameters($parameters);
        $parameters = $this->transformTypes($parameters);

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
     * @return array<string, bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [Request::class => true];
    }

    /**
     * @return array<string, mixed>
     */
    private function extractParameters(Request $request): array
    {
        return array_merge_recursive(
            $request->query->all(),
            $request->request->all(),
            (array)$request->attributes->get(key: '_route_params'),
            $request->getContent() !== '' ? $request->toArray() : [],
        );
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

    private function transformTypes(mixed $data): mixed
    {
        return match (true) {
            is_scalar($data) => match (true) {
                $data === strval(intval($data)) => intval($data),
                $data === strval(floatval($data)) => floatval($data),
                $data === 'true' || is_bool($data) => boolval($data),
                $data === 'false' => false,
                $data === 'null' => null,
                default => $data,
            },
            is_array($data) => array_map(fn($item) => $this->transformTypes($item), $data),
            default => $data,
        };
    }
}
