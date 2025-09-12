<?php

declare(strict_types=1);

namespace Tests\Support;

use Opis\JsonSchema\Validator;
use Symfony\Component\HttpFoundation\Request;

trait HttpClientRequestTrait
{
    public static function assertResponseSchema(string $schema): void
    {
        $schemaObject = json_decode(file_get_contents(__DIR__ . '/../Support/Schema/' . $schema));
        $responseObject = json_decode(self::getClient()->getResponse()->getContent());

        self::assertTrue(new Validator()->validate($responseObject, $schemaObject)->isValid());
    }

    public static function assertResponseContentSame(string $expectedContent): void
    {
        self::assertSame($expectedContent, self::getClient()->getResponse()->getContent());
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
}
