<?php

declare(strict_types=1);

namespace Tests\Support;

use RuntimeException;

final class OpenApiSpecProvider
{
    public static function getResponseSchema(object $spec, string $path, string $method, int $statusCode): object
    {
        $paths = $spec->paths ?? throw new RuntimeException(
            message: 'OpenAPI specification has no paths section.',
        );
        $pathEntry = $paths->{$path} ?? throw new RuntimeException(
            sprintf('No OpenAPI path entry for "%s".', $path),
        );
        $methodEntry = $pathEntry->{strtolower($method)} ?? throw new RuntimeException(
            sprintf('No OpenAPI operation for %s "%s".', $method, $path),
        );
        $responseEntry = $methodEntry->responses->{(string) $statusCode} ?? throw new RuntimeException(
            sprintf('No OpenAPI response for %s "%s" status %d.', $method, $path, $statusCode),
        );
        $schema = $responseEntry->content->{'application/json'}->schema ?? throw new RuntimeException(
            sprintf('No OpenAPI schema for %s "%s" status %d.', $method, $path, $statusCode),
        );

        return self::resolveReferences($schema, $spec);
    }

    public static function getComponentSchema(object $spec, string $schemaName): object
    {
        $schema = $spec->components->schemas->{$schemaName} ?? throw new RuntimeException(
            sprintf('Component schema "%s" not found in OpenAPI specification.', $schemaName),
        );

        return self::resolveReferences($schema, $spec);
    }

    private static function resolveReferences(object $schema, object $spec): object
    {
        if (isset($schema->{'$ref'})) {
            $ref = $schema->{'$ref'};

            if (!str_starts_with($ref, '#/components/schemas/')) {
                throw new RuntimeException(sprintf('Cannot resolve $ref "%s".', $ref));
            }

            $schemaName = substr($ref, strlen(string: '#/components/schemas/'));
            $resolved = $spec->components->schemas->{$schemaName} ?? throw new RuntimeException(
                sprintf('$ref target "%s" not found in components/schemas.', $schemaName),
            );

            return self::resolveReferences($resolved, $spec);
        }

        $result = clone $schema;

        if (isset($result->properties)) {
            foreach ((array) $result->properties as $key => $property) {
                $result->properties->{$key} = self::resolveReferences($property, $spec);
            }
        }

        if (isset($result->items)) {
            $result->items = self::resolveReferences($result->items, $spec);
        }

        return $result;
    }
}
