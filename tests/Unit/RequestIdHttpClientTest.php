<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Contract\RequestIdStorageInterface;
use App\Infrastructure\HttpClient\RequestIdHttpClient;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class RequestIdHttpClientTest extends TestCase
{
    private HttpClientInterface&MockObject $httpClient;

    private RequestIdStorageInterface&MockObject $requestIdStorage;

    private ResponseInterface&MockObject $response;

    #[Override]
    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(type: HttpClientInterface::class);
        $this->requestIdStorage = $this->createMock(type: RequestIdStorageInterface::class);
        $this->response = $this->createMock(type: ResponseInterface::class);

        $this->requestIdStorage
            ->expects($this->any())
            ->method(constraint: 'getRequestId')
            ->willReturn(value: '00000000-0000-6000-8000-000000000000');
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testPassingRequestIdHeader(): void
    {
        $requestIdHttpClient = new RequestIdHttpClient($this->requestIdStorage, $this->httpClient);

        $this->httpClient
            ->expects($this->once())
            ->method(constraint: 'request')
            ->willReturn($this->response);

        $requestIdHttpClient->request(method: 'GET', url: 'https://api.example.com');
    }
}
