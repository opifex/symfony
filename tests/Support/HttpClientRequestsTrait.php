<?php

declare(strict_types=1);

namespace Tests\Support;

use Opis\JsonSchema\Errors\ValidationError;
use Opis\JsonSchema\Validator;
use Symfony\Component\HttpFoundation\Request;

trait HttpClientRequestsTrait
{
    public static function loadHttpClient(): void
    {
        self::createClient();
    }

    public static function assertResponseSchema(): void
    {
        $method = strtoupper(self::getClient()->getRequest()->getMethod());
        $router = self::getContainer()->get(id: 'router');
        $router->getContext()->setMethod($method);
        $routeInfo = $router->match(self::getClient()->getRequest()->getPathInfo());

        self::validateOpenApiSchema(
            OpenApiSpecProvider::getResponseSchema(
                spec: self::loadOpenApiSpecification(),
                path: $router->getRouteCollection()->get($routeInfo['_route'])->getPath(),
                method: $router->getContext()->getMethod(),
                statusCode: self::getClient()->getResponse()->getStatusCode(),
            ),
        );
    }

    public static function assertErrorResponseSchema(): void
    {
        self::validateOpenApiSchema(
            OpenApiSpecProvider::getComponentSchema(
                spec: self::loadOpenApiSpecification(),
                schemaName: 'ErrorResponse',
            ),
        );
    }

    public static function sendAuthorizationRequest(string $email, string $password): void
    {
        self::sendPostRequest(url: '/api/auth/signin', params: ['email' => $email, 'password' => $password]);
        $jsonResponse = json_decode(self::getClient()->getResponse()->getContent(), associative: true);
        $httpAuthorization = 'Bearer ' . ($jsonResponse['access_token'] ?? '');
        self::getClient()->setServerParameter(key: 'HTTP_AUTHORIZATION', value: $httpAuthorization);
    }

    public static function sendGetRequest(string $url, array $params = [], array $server = []): void
    {
        self::sendHttpRequest(method: Request::METHOD_GET, uri: $url, params: $params, server: $server);
    }

    public static function sendPostRequest(string $url, array $params = [], array $server = []): void
    {
        self::sendHttpRequest(method: Request::METHOD_POST, uri: $url, params: $params, server: $server);
    }

    public static function sendDeleteRequest(string $url, array $params = [], array $server = []): void
    {
        self::sendHttpRequest(method: Request::METHOD_DELETE, uri: $url, params: $params, server: $server);
    }

    public static function sendPatchRequest(string $url, array $params = [], array $server = []): void
    {
        self::sendHttpRequest(method: Request::METHOD_PATCH, uri: $url, params: $params, server: $server);
    }

    private static function sendHttpRequest(string $method, string $uri, array $params, array $server): void
    {
        self::getClient()->jsonRequest($method, $uri, $params, $server, changeHistory: false);
    }

    private static function loadOpenApiSpecification(): object
    {
        static $specification = null;

        if ($specification === null) {
            $generator = self::getContainer()->get(id: 'nelmio_api_doc.generator');
            $specification = json_decode($generator->generate()->toJson());
        }

        return $specification;
    }

    private static function validateOpenApiSchema(object $schema): void
    {
        $responseBody = json_decode(self::getClient()->getResponse()->getContent());
        $result = new Validator()->validate($responseBody, $schema);

        self::assertTrue(
            condition: $result->isValid(),
            message: sprintf(
                "Response body does not match the expected OpenAPI schema:\n%s",
                self::formatOpenApiError($result->error()),
            ),
        );
    }

    private static function formatOpenApiError(?ValidationError $error, int $depth = 0): string
    {
        if ($error === null) {
            return '(no error details)';
        }

        $message = $error->message();

        foreach ($error->args() as $key => $value) {
            $message = str_replace(sprintf('{%s}', $key), (string) $value, $message);
        }

        $path = $error->data()->path();
        $prefix = $path !== [] ? sprintf('[%s] ', implode(separator: '.', array: $path)) : '';

        $lines = [str_repeat(string: '  ', times: $depth) . $prefix . $message];

        foreach ($error->subErrors() as $subError) {
            $lines[] = self::formatOpenApiError($subError, depth: $depth + 1);
        }

        return implode(separator: PHP_EOL, array: $lines);
    }
}
