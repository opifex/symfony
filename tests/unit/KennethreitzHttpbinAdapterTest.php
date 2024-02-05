<?php

declare(strict_types=1);

namespace App\Tests;

use App\Domain\Exception\HttpbinResponderException;
use App\Infrastructure\Adapter\KennethreitzHttpbinAdapter;
use Codeception\Attribute\DataProvider;
use Codeception\Test\Unit;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

final class KennethreitzHttpbinAdapterTest extends Unit
{
    /**
     * @throws HttpbinResponderException
     */
    #[DataProvider(methodName: 'httpbinResponseProvider')]
    public function testGetJsonReturnResponse(array $response): void
    {
        $mockResponse = new MockResponse(json_encode($response));
        $mockHttpClient = new MockHttpClient($mockResponse);
        $kennethreitzHttpbinAdapter = new KennethreitzHttpbinAdapter($mockHttpClient);

        $json = $kennethreitzHttpbinAdapter->getJson();

        $this->assertSame($json, $response);
    }

    public function testGetJsonThrowsExceptionOnHttpError(): void
    {
        $mockResponse = new MockResponse();
        $mockResponse->cancel();

        $mockHttpClient = new MockHttpClient($mockResponse);
        $kennethreitzHttpbinAdapter = new KennethreitzHttpbinAdapter($mockHttpClient);

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
