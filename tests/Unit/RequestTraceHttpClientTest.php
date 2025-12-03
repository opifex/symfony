<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Application\Contract\RequestTraceManagerInterface;
use App\Infrastructure\HttpClient\RequestTraceHttpClient;
use Override;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class RequestTraceHttpClientTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(type: HttpClientInterface::class);
        $this->requestTraceManager = $this->createMock(type: RequestTraceManagerInterface::class);
        $this->response = $this->createMock(type: ResponseInterface::class);

        $this->requestTraceManager
            ->expects($this->any())
            ->method(constraint: 'getTraceId')
            ->willReturn(value: '00000000-0000-6000-8000-000000000000');
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testPassingRequestIdHeader(): void
    {
        $requestTraceHttpClient = new RequestTraceHttpClient(
            requestTraceManager: $this->requestTraceManager,
            client: $this->httpClient,
        );

        $this->httpClient
            ->expects($this->once())
            ->method(constraint: 'request')
            ->willReturn($this->response);

        $requestTraceHttpClient->request(method: 'GET', url: 'https://api.example.com');
    }
}
