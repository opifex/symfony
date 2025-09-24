<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Exception\Integration\HttpbinResponseProviderException;
use App\Infrastructure\Adapter\Kennethreitz\KennethreitzHttpbinAdapter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class KennethreitzHttpbinAdapterTest extends TestCase
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

        $this->expectException(HttpbinResponseProviderException::class);

        $kennethreitzHttpbinAdapter->getJson();
    }

    public static function httpbinResponseProvider(): iterable
    {
        return [
            [['slideshow' => ['author' => 'Yours Truly', 'title' => 'Sample Slide Show']]],
        ];
    }
}
