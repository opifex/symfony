<?php

declare(strict_types=1);

namespace Tests\Support;

use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

trait HttpMockClientTrait
{
    /**
     * @param HttpMockClientResponse[] $mockResponses
     */
    public static function loadMockResponses(array $mockResponses = []): void
    {
        $mockHttpClient = new MockHttpClient();
        $mockHttpClient->setResponseFactory(
            function (string $method, string $url) use ($mockResponses): ResponseInterface {
                foreach ($mockResponses as $mockResponse) {
                    if ($mockResponse->requestMethod === $method && $mockResponse->requestUrl === $url) {
                        return new MockResponse(body: $mockResponse->responseBody, info: [
                            'http_code' => $mockResponse->responseStatusCode,
                            'response_headers' => $mockResponse->responseHeaders,
                        ]);
                    }
                }

                return new MockResponse(info: ['http_code' => Response::HTTP_NOT_FOUND]);
            },
        );
        self::getContainer()->set(HttpClientInterface::class, $mockHttpClient);
    }

    public static function getResponseFromFile(string $file): string
    {
        return file_get_contents(__DIR__ . '/../Support/Response/' . $file);
    }
}
