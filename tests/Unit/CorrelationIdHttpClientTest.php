<?php

declare(strict_types=1);

namespace Tests\Unit;

use AllowDynamicProperties;
use App\Infrastructure\HttpClient\CorrelationIdHttpClient;
use App\Infrastructure\Observability\CorrelationIdProvider;
use Override;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[AllowDynamicProperties]
final class CorrelationIdHttpClientTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(type: HttpClientInterface::class);
        $this->response = $this->createMock(type: ResponseInterface::class);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testPassingRequestIdHeader(): void
    {
        $correlationIdHttpClient = new CorrelationIdHttpClient(
            correlationIdProvider: new CorrelationIdProvider(),
            client: $this->httpClient,
        );

        $this->httpClient
            ->expects($this->once())
            ->method(constraint: 'request')
            ->willReturn($this->response);

        $correlationIdHttpClient->request(method: 'GET', url: 'https://api.example.com');
    }
}
