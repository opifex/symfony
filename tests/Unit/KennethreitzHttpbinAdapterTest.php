<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Exception\HttpbinResponderException;
use App\Infrastructure\Adapter\Kennethreitz\KennethreitzHttpbinAdapter;
use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class KennethreitzHttpbinAdapterTest extends Unit
{
    #[DataProvider(methodName: 'httpbinResponseProvider')]
    public function testGetJsonReturnResponse(array $response): void
    {
        $apiUrl = 'https://api.example.com';
        $mockResponse = new MockResponse(json_encode($response));
        $mockHttpClient = new MockHttpClient($mockResponse);
        $kennethreitzHttpbinAdapter = new KennethreitzHttpbinAdapter($apiUrl, $mockHttpClient);

        $json = $kennethreitzHttpbinAdapter->getJson();

        $this->assertSame($json, $response);
    }

    public function testGetJsonThrowsExceptionOnHttpError(): void
    {
        $mockResponse = new MockResponse();
        $mockResponse->cancel();

        $apiUrl = 'https://api.example.com';
        $mockHttpClient = new MockHttpClient($mockResponse);
        $kennethreitzHttpbinAdapter = new KennethreitzHttpbinAdapter($apiUrl, $mockHttpClient);

        $this->expectException(HttpbinResponderException::class);

        $kennethreitzHttpbinAdapter->getJson();
    }

    protected function httpbinResponseProvider(): array
    {
        return [
            [['slideshow' => ['author' => 'Yours Truly', 'title' => 'Sample Slide Show']]],
        ];
    }
}
