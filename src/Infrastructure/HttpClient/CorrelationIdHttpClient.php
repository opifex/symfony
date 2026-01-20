<?php

declare(strict_types=1);

namespace App\Infrastructure\HttpClient;

use App\Infrastructure\Observability\CorrelationIdProvider;
use Override;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;
use Symfony\Component\HttpClient\DecoratorTrait;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\Service\ResetInterface;

#[AsDecorator(HttpClientInterface::class)]
final class CorrelationIdHttpClient implements HttpClientInterface, ResetInterface
{
    use DecoratorTrait;

    public function __construct(
        private readonly CorrelationIdProvider $correlationIdProvider,
        #[AutowireDecorated]
        ?HttpClientInterface $client = null,
    ) {
        $this->client = $client ?? HttpClient::create();
    }

    /**
     * @param array<string, mixed> $options
     * @throws TransportExceptionInterface
     */
    #[Override]
    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        $options['headers'] ??= [];

        if (is_array($options['headers'])) {
            $correlationId = $this->correlationIdProvider->getCorrelationId();
            $httpHeaderName = $this->correlationIdProvider->getHttpHeaderName();
            $options['headers'][$httpHeaderName] = $correlationId;
        }

        return $this->client->request($method, $url, $options);
    }
}
