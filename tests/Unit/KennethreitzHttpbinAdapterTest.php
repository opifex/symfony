<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\Adapter\Kennethreitz\Exception\HttpRequestFailedException;
use App\Infrastructure\Adapter\Kennethreitz\HttpbinResponseProvider;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

#[AllowDynamicProperties]
final class KennethreitzHttpbinAdapterTest extends TestCase
{
    #[DataProvider(methodName: 'httpbinResponseProvider')]
    public function testGetJsonReturnResponse(array $response): void
    {
        $apiUrl = 'https://api.example.com';
        $mockResponse = new MockResponse(json_encode($response));
        $mockHttpClient = new MockHttpClient($mockResponse);
        $kennethreitzHttpbinAdapter = new HttpbinResponseProvider($apiUrl, $mockHttpClient);

        $json = $kennethreitzHttpbinAdapter->getJson();

        $this->assertSame($json, $response);
    }

    public function testGetJsonThrowsExceptionOnHttpError(): void
    {
        $mockResponse = new MockResponse();
        $mockResponse->cancel();

        $apiUrl = 'https://api.example.com';
        $mockHttpClient = new MockHttpClient($mockResponse);
        $kennethreitzHttpbinAdapter = new HttpbinResponseProvider($apiUrl, $mockHttpClient);

        $this->expectException(HttpRequestFailedException::class);

        $kennethreitzHttpbinAdapter->getJson();
    }

    public static function httpbinResponseProvider(): iterable
    {
        yield 'slideshow json structure from httpbin' => [
            ['slideshow' => ['author' => 'Yours Truly', 'title' => 'Sample Slide Show']],
        ];
    }
}
